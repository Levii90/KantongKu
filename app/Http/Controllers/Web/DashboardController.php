<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\BudgetService;
use App\Services\FinancialReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly FinancialReportService $financialReportService,
        private readonly BudgetService $budgetService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $month = (int) ($request->query('month') ?? now()->month);
        $year = (int) ($request->query('year') ?? now()->year);

        $summary = $this->financialReportService->getDashboardSummary($user, $month, $year);
        $budgets = $this->budgetService->getBudgetsWithSpending($user, $month, $year);
        $manualInsights = $user->financialInsights()
            ->where('month', $month)
            ->where('year', $year)
            ->latest()
            ->get();
        $categories = Category::forUser($user)->orderBy('name')->get();

        return view('dashboard', compact('summary', 'budgets', 'manualInsights', 'categories', 'month', 'year'));
    }
}
