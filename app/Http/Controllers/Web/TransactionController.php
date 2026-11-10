<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionStoreRequest;
use App\Models\Category;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct(private readonly TransactionService $transactionService)
    {
    }

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

    public function store(TransactionStoreRequest $request): RedirectResponse
    {
        $this->transactionService->createForUser($request->user(), $request->validated());

        return redirect()->route('dashboard')->with('success', 'Transaksi berhasil disimpan!');
    }
}
