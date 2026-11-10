<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRangeRequest;
use App\Services\FinancialReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(private readonly FinancialReportService $financialReportService) {}

    public function summary(ReportRangeRequest $request): JsonResponse
    {
        $filters = $request->validated();

        return response()->json([
            'status' => 'success',
            'data' => $this->financialReportService->getSummary(
                auth()->user(),
                $filters['start_date'],
                $filters['end_date'],
            ),
        ]);
    }

    public function chartData(ReportRangeRequest $request): JsonResponse
    {
        $filters = $request->validated();

        return response()->json([
            'status' => 'success',
            'data' => $this->financialReportService->getChartData(
                auth()->user(),
                $filters['start_date'],
                $filters['end_date'],
            ),
        ]);
    }
}
