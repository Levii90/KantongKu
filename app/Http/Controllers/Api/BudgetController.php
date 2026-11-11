<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BudgetIndexRequest;
use App\Http\Requests\BudgetStoreRequest;
use App\Models\Budget;
use App\Services\BudgetService;
use Illuminate\Http\JsonResponse;

class BudgetController extends Controller
{
    public function __construct(private readonly BudgetService $budgetService) {}

    public function index(BudgetIndexRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $month = (int) ($filters['month'] ?? now()->month);
        $year = (int) ($filters['year'] ?? now()->year);

        return response()->json([
            'status' => 'success',
            'data' => $this->budgetService->listForUser(auth()->user(), (int) $month, (int) $year),
        ]);
    }

    public function show(Budget $budget): JsonResponse
    {
        abort_unless($budget->user_id === auth()->id(), 404);
        $budget->load('category');

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => (int) $budget->id,
                'category_id' => (int) $budget->category_id,
                'category_name' => $budget->category?->name ?? 'Uncategorized',
                'amount_limit' => (float) $budget->amount_limit,
                'month' => (int) $budget->month,
                'year' => (int) $budget->year,
            ],
        ]);
    }

    public function store(BudgetStoreRequest $request): JsonResponse
    {
        $budget = $this->budgetService->createForUser(auth()->user(), $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Budget berhasil disimpan.',
            'data' => [
                'id' => (int) $budget->id,
                'category_id' => (int) $budget->category_id,
                'amount_limit' => (float) $budget->amount_limit,
                'month' => (int) $budget->month,
                'year' => (int) $budget->year,
            ],
        ], 201);
    }

    public function update(Budget $budget, BudgetStoreRequest $request): JsonResponse
    {
        $budget = $this->budgetService->updateForUser(auth()->user(), $budget, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Budget berhasil diperbarui.',
            'data' => [
                'id' => (int) $budget->id,
                'category_id' => (int) $budget->category_id,
                'amount_limit' => (float) $budget->amount_limit,
                'month' => (int) $budget->month,
                'year' => (int) $budget->year,
            ],
        ]);
    }

    public function destroy(Budget $budget): JsonResponse
    {
        $this->budgetService->deleteForUser(auth()->user(), $budget);

        return response()->json([
            'status' => 'success',
            'message' => 'Budget berhasil dihapus.',
        ]);
    }
}
