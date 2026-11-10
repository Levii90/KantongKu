<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\FinancialReportService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private readonly FinancialReportService $financialReportService,
        private readonly TransactionService $transactionService
    ) {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        $summary = $this->financialReportService->getSummary($user, $startDate, $endDate);
        $transactions = $this->transactionService->listForUser($user, $request->all());
        $categories = Category::forUser($user)->orderBy('name')->get();

        return view('reports.index', compact('summary', 'transactions', 'categories', 'startDate', 'endDate'));
    }
}
