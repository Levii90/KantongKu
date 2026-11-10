<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\BudgetStoreRequest;
use App\Models\Budget;
use App\Services\BudgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function __construct(private readonly BudgetService $budgetService)
    {
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
}
