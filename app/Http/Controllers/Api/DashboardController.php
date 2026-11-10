<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashboardSummaryRequest;
use App\Services\FinancialReportService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(private readonly FinancialReportService $financialReportService) {}

    public function summary(DashboardSummaryRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $month = (int) ($filters['month'] ?? now()->month);
        $year = (int) ($filters['year'] ?? now()->year);

        return response()->json([
            'status' => 'success',
            'data' => $this->financialReportService->getDashboardSummary(auth()->user(), $month, $year),
        ]);
    }
}
