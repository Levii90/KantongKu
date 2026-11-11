<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinancialInsightStoreRequest;
use App\Models\FinancialInsight;
use App\Services\FinancialInsightService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinancialInsightController extends Controller
{
    public function __construct(private readonly FinancialInsightService $insightService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'month' => ['sometimes', 'integer', 'between:1,12'],
            'year' => ['sometimes', 'integer', 'between:2000,32767'],
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $this->insightService->listForUser(
                $request->user(),
                isset($filters['month']) ? (int) $filters['month'] : null,
                isset($filters['year']) ? (int) $filters['year'] : null,
            ),
        ]);
    }

    public function store(FinancialInsightStoreRequest $request): JsonResponse
    {
        $insight = $this->insightService->createForUser($request->user(), $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Insight berhasil dibuat.',
            'data' => $insight,
        ], 201);
    }

    public function show(FinancialInsight $insight, Request $request): JsonResponse
    {
        abort_unless($insight->user_id === $request->user()->id, 404);

        return response()->json(['status' => 'success', 'data' => $insight]);
    }

    public function update(
        FinancialInsight $insight,
        FinancialInsightStoreRequest $request,
    ): JsonResponse {
        $insight = $this->insightService->updateForUser($request->user(), $insight, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Insight berhasil diperbarui.',
            'data' => $insight,
        ]);
    }

    public function destroy(FinancialInsight $insight, Request $request): JsonResponse
    {
        $this->insightService->deleteForUser($request->user(), $insight);

        return response()->json(['status' => 'success', 'message' => 'Insight berhasil dihapus.']);
    }
}
