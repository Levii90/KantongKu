<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BudgetService
{
    public function listForUser(User $user, ?int $month = null, ?int $year = null): array
    {
        $month ??= now()->month;
        $year ??= now()->year;

        return $user->budgets()
            ->with('category')
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('amount_limit', 'desc')
            ->get()
            ->map(function (Budget $budget) {
                return [
                    'id' => (int) $budget->id,
                    'category_id' => (int) $budget->category_id,
                    'category_name' => $budget->category?->name ?? 'Uncategorized',
                    'amount_limit' => (float) $budget->amount_limit,
                    'month' => (int) $budget->month,
                    'year' => (int) $budget->year,
                ];
            })
            ->values()
            ->all();
    }

    public function getBudgetsWithSpending(User $user, ?int $month = null, ?int $year = null): array
    {
        $month ??= now()->month;
        $year ??= now()->year;

        $budgets = $this->listForUser($user, $month, $year);

        $start = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $end = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $expenses = $user->transactions()
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$start, $end])
            ->selectRaw('category_id, SUM(amount) as total_spent')
            ->groupBy('category_id')
            ->pluck('total_spent', 'category_id')
            ->toArray();

        foreach ($budgets as &$b) {
            $spent = (float) ($expenses[$b['category_id']] ?? 0);
            $limit = (float) $b['amount_limit'];
            $b['spent'] = round($spent, 2);
            $b['remaining'] = round(max(0, $limit - $spent), 2);
            $b['percentage'] = $limit > 0 ? round(($spent / $limit) * 100, 1) : 0;
            $b['is_over_budget'] = $spent > $limit;
        }

        return $budgets;
    }

    public function createForUser(User $user, array $payload): Budget
    {
        return DB::transaction(function () use ($user, $payload) {
            $category = $this->expenseCategoryForUser($user, (int) $payload['category_id']);

            $exists = $user->budgets()->where('category_id', $category->id)
                ->where('year', $payload['year'])
                ->where('month', $payload['month'])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'category_id' => ['Budget untuk kategori ini pada periode yang sama sudah ada.'],
                ]);
            }

            return $user->budgets()->create([
                'category_id' => $category->id,
                'amount_limit' => (float) $payload['amount_limit'],
                'month' => (int) $payload['month'],
                'year' => (int) $payload['year'],
            ]);
        });
    }

    public function updateForUser(User $user, Budget $budget, array $payload): Budget
    {
        if ($budget->user_id !== $user->id) {
            abort(404, 'Resource not found');
        }

        return DB::transaction(function () use ($user, $budget, $payload) {
            $category = $this->expenseCategoryForUser($user, (int) $payload['category_id']);

            $exists = $user->budgets()
                ->where('category_id', $category->id)
                ->where('year', $payload['year'])
                ->where('month', $payload['month'])
                ->where('id', '!=', $budget->id)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'category_id' => ['Budget untuk kategori ini pada periode yang sama sudah ada.'],
                ]);
            }

            $budget->update([
                'category_id' => $category->id,
                'amount_limit' => (float) $payload['amount_limit'],
                'month' => (int) $payload['month'],
                'year' => (int) $payload['year'],
            ]);

            return $budget->refresh();
        });
    }

    public function deleteForUser(User $user, Budget $budget): bool
    {
        if ($budget->user_id !== $user->id) {
            abort(404, 'Resource not found');
        }

        return $budget->delete();
    }

    private function expenseCategoryForUser(User $user, int $categoryId): Category
    {
        $category = Category::query()->find($categoryId);

        if (
            ! $category
            || $category->type !== 'expense'
            || ($category->user_id !== null && $category->user_id !== $user->id)
        ) {
            throw ValidationException::withMessages([
                'category_id' => ['Kategori pengeluaran yang dipilih tidak valid.'],
            ]);
        }

        return $category;
    }
}
