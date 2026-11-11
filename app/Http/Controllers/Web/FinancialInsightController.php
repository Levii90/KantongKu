<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinancialInsightStoreRequest;
use App\Models\FinancialInsight;
use App\Services\FinancialInsightService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialInsightController extends Controller
{
    public function __construct(private readonly FinancialInsightService $insightService) {}

    public function index(Request $request): View
    {
        $insights = $this->insightService->listForUser($request->user());

        return view('insights.index', compact('insights'));
    }

    public function store(FinancialInsightStoreRequest $request): RedirectResponse
    {
        $this->insightService->createForUser($request->user(), $request->validated());

        return redirect()->route('insights.index')->with('success', 'Insight berhasil dibuat.');
    }

    public function edit(Request $request, FinancialInsight $insight): View
    {
        abort_unless($insight->user_id === $request->user()->id, 404);

        return view('insights.form', compact('insight'));
    }

    public function update(
        FinancialInsightStoreRequest $request,
        FinancialInsight $insight,
    ): RedirectResponse {
        $this->insightService->updateForUser($request->user(), $insight, $request->validated());

        return redirect()->route('insights.index')->with('success', 'Insight berhasil diperbarui.');
    }

    public function destroy(Request $request, FinancialInsight $insight): RedirectResponse
    {
        $this->insightService->deleteForUser($request->user(), $insight);

        return redirect()->route('insights.index')->with('success', 'Insight berhasil dihapus.');
    }
}
