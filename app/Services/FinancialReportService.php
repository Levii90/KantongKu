<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class FinancialReportService
{
    public function getDashboardSummary(User $user, ?int $month = null, ?int $year = null): array
    {
        $month ??= now()->month;
        $year ??= now()->year;

        $periodStart = Carbon::create($year, $month, 1, 0, 0, 0);
        $periodEnd = $periodStart->copy()->endOfMonth();

        $monthIncome = $this->getTotalsForPeriod($user, $periodStart->toDateString(), $periodEnd->toDateString(), 'income');
        $monthExpense = $this->getTotalsForPeriod($user, $periodStart->toDateString(), $periodEnd->toDateString(), 'expense');
        $totalIncome = $this->getTotalsForType($user, 'income');
        $totalExpense = $this->getTotalsForType($user, 'expense');

        $currentBalance = (float) $totalIncome - (float) $totalExpense;
        $recentTransactions = $user->transactions()->with('category')->orderByDesc('transaction_date')->orderByDesc('id')->limit(5)->get()->map(function ($transaction) {
            return [
                'id' => (int) $transaction->id,
                'type' => $transaction->type,
                'category_name' => $transaction->category?->name ?? 'Uncategorized',
                'amount' => (float) $transaction->amount,
                'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
                'description' => $transaction->description,
            ];
        })->values()->all();

        return [
            'current_balance' => round($currentBalance, 2),
            'total_income_this_month' => round((float) $monthIncome, 2),
            'total_expense_this_month' => round((float) $monthExpense, 2),
            'recent_transactions' => $recentTransactions,
            'insights' => $this->generateInsights($user, $periodStart, $periodEnd),
        ];
    }

    public function getSummary(User $user, string $startDate, string $endDate): array
    {
        $transactions = $user->transactions()->with('category')->whereBetween('transaction_date', [$startDate, $endDate])->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $netSavings = $totalIncome - $totalExpense;

        $categoryBreakdown = $transactions->filter(fn ($transaction) => $transaction->type === 'expense')
            ->groupBy(fn ($transaction) => $transaction->category?->name ?? 'Uncategorized')
            ->map(function ($items, $categoryName) use ($totalExpense) {
                $sum = $items->sum('amount');

                return [
                    'category_name' => $categoryName,
                    'total_amount' => round((float) $sum, 2),
                    'percentage' => $totalExpense > 0 ? round((($sum / $totalExpense) * 100), 2) : 0,
                ];
            })
            ->sortByDesc('total_amount')
            ->values()
            ->all();

        $monthlyTrends = $this->buildMonthlyTrend($transactions, $startDate, $endDate);

        return [
            'total_income' => round((float) $totalIncome, 2),
            'total_expense' => round((float) $totalExpense, 2),
            'net_savings' => round((float) $netSavings, 2),
            'category_breakdown' => $categoryBreakdown,
            'monthly_trends' => $monthlyTrends,
        ];
    }

    public function getChartData(User $user, string $startDate, string $endDate): array
    {
        return $this->getSummary($user, $startDate, $endDate);
    }

    protected function generateInsights(User $user, Carbon $periodStart, Carbon $periodEnd): array
    {
        $thisMonthExpense = $this->getTotalsForPeriod($user, $periodStart->toDateString(), $periodEnd->toDateString(), 'expense');
        $thisMonthIncome = $this->getTotalsForPeriod($user, $periodStart->toDateString(), $periodEnd->toDateString(), 'income');

        if ((float) $thisMonthExpense === 0 && (float) $thisMonthIncome === 0) {
            return ['Belum ada transaksi untuk periode ini.'];
        }

        $previousMonthStart = $periodStart->copy()->subMonth()->startOfMonth();
        $previousMonthEnd = $previousMonthStart->copy()->endOfMonth();
        $previousMonthExpense = $this->getTotalsForPeriod($user, $previousMonthStart->toDateString(), $previousMonthEnd->toDateString(), 'expense');

        $insights = [];
        if ((float) $previousMonthExpense > 0) {
            $changePercent = ((float) $thisMonthExpense - (float) $previousMonthExpense) / (float) $previousMonthExpense * 100;
            $direction = $changePercent >= 0 ? 'meningkat' : 'turun';
            $insights[] = 'Pengeluaran Anda bulan ini '.$direction.' '.round(abs($changePercent), 2).'% dibanding bulan lalu.';
        }

        $largestCategory = $user->transactions()->with('category')
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->get()
            ->groupBy(fn ($transaction) => $transaction->category?->name ?? 'Uncategorized')
            ->map(fn ($items) => $items->sum('amount'))
            ->sortDesc()
            ->keys()
            ->first();

        if ($largestCategory) {
            $insights[] = 'Kategori pengeluaran terbesar bulan ini adalah '.$largestCategory.'.';
        }

        return $insights;
    }

    protected function getTotalsForType(User $user, string $type): float
    {
        return (float) $user->transactions()->where('type', $type)->sum('amount');
    }

    protected function getTotalsForPeriod(User $user, string $startDate, string $endDate, string $type): float
    {
        return (float) $user->transactions()->where('type', $type)->whereBetween('transaction_date', [$startDate, $endDate])->sum('amount');
    }

    protected function buildMonthlyTrend(Collection $transactions, string $startDate, string $endDate): array
    {
        $periodStart = Carbon::parse($startDate)->startOfMonth();
        $periodEnd = Carbon::parse($endDate)->endOfMonth();
        $transactionsByMonth = $transactions->groupBy(
            fn ($transaction) => $transaction->transaction_date->format('Y-m'),
        );

        $trend = [];
        foreach (CarbonPeriod::create($periodStart, '1 month', $periodEnd) as $month) {
            $monthTransactions = $transactionsByMonth->get($month->format('Y-m'), collect());

            $trend[] = [
                'month' => $month->format('Y-m'),
                'income' => round((float) $monthTransactions->where('type', 'income')->sum('amount'), 2),
                'expense' => round((float) $monthTransactions->where('type', 'expense')->sum('amount'), 2),
            ];
        }

        return $trend;
    }
}
