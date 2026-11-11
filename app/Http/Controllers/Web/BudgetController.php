<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\BudgetStoreRequest;
use App\Models\Budget;
use App\Models\Category;
use App\Services\BudgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function __construct(private readonly BudgetService $budgetService) {}

    public function index(Request $request): View
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        $budgets = $this->budgetService->getBudgetsWithSpending($request->user(), $month, $year);
        $categories = Category::forUser($request->user())->where('type', 'expense')->orderBy('name')->get();

        return view('budgets.index', compact('budgets', 'categories', 'month', 'year'));
    }

    public function edit(Request $request, Budget $budget): View
    {
        abort_unless($budget->user_id === $request->user()->id, 404);
        $categories = Category::forUser($request->user())->where('type', 'expense')->orderBy('name')->get();

        return view('budgets.form', compact('budget', 'categories'));
    }

    public function store(BudgetStoreRequest $request): RedirectResponse
    {
        $this->budgetService->createForUser($request->user(), $request->validated());

        return redirect()->back()->with('success', 'Anggaran berhasil ditetapkan!');
    }

    public function destroy(Request $request, Budget $budget): RedirectResponse
    {
        $this->budgetService->deleteForUser($request->user(), $budget);

        return redirect()->back()->with('success', 'Anggaran berhasil dihapus.');
    }

    public function update(BudgetStoreRequest $request, Budget $budget): RedirectResponse
    {
        $this->budgetService->updateForUser($request->user(), $budget, $request->validated());

        return redirect()->route('budgets.index')->with('success', 'Anggaran berhasil diperbarui.');
    }
}
