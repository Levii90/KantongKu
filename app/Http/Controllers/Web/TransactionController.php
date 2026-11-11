<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionStoreRequest;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct(private readonly TransactionService $transactionService) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $transactions = $this->transactionService->listForUser($user, $request->all());
        $categories = Category::forUser($user)->orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'categories'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $categories = Category::forUser($user)->orderBy('name')->get();

        return view('transactions.create', compact('categories'));
    }

    public function edit(Request $request, Transaction $transaction): View
    {
        abort_unless($transaction->user_id === $request->user()->id, 404);
        $categories = Category::forUser($request->user())->orderBy('name')->get();

        return view('transactions.create', compact('categories', 'transaction'));
    }

    public function store(TransactionStoreRequest $request): RedirectResponse
    {
        $this->transactionService->createForUser($request->user(), $request->validated());

        return redirect()->route('dashboard')->with('success', 'Transaksi berhasil disimpan!');
    }

    public function update(TransactionStoreRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->transactionService->updateForUser($request->user(), $transaction, $request->validated());

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        $this->transactionService->deleteForUser($request->user(), $transaction);

        return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
    }
}
