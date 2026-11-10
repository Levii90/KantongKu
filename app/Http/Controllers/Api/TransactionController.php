<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionIndexRequest;
use App\Http\Requests\TransactionStoreRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(private readonly TransactionService $transactionService)
    {
    }

    public function index(TransactionIndexRequest $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->transactionService->listForUser(auth()->user(), $request->validated()),
        ]);
    }

    public function store(TransactionStoreRequest $request): JsonResponse
    {
        $transaction = $this->transactionService->createForUser(auth()->user(), $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi berhasil dicatat.',
            'data' => [
                'id' => (int) $transaction->id,
                'type' => $transaction->type,
                'amount' => (float) $transaction->amount,
                'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
                'category_id' => (int) $transaction->category_id,
                'description' => $transaction->description,
            ],
        ], 201);
    }

    public function show(Transaction $transaction): JsonResponse
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(404, 'Resource not found');
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => (int) $transaction->id,
                'type' => $transaction->type,
                'amount' => (float) $transaction->amount,
                'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
                'category_id' => (int) $transaction->category_id,
                'description' => $transaction->description,
            ],
        ]);
    }
}
