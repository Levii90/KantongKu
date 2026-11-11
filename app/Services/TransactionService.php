<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionService
{
    public function listForUser(User $user, array $filters = []): array
    {
        $query = $user->transactions()->with('category')->orderByDesc('transaction_date')->orderByDesc('id');

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['month']) && ! empty($filters['year'])) {
            $query->whereMonth('transaction_date', (int) $filters['month'])
                ->whereYear('transaction_date', (int) $filters['year']);
        }

        if (! empty($filters['start_date']) && ! empty($filters['end_date'])) {
            $query->whereBetween('transaction_date', [$filters['start_date'], $filters['end_date']]);
        }

        return $query->get()->map(function (Transaction $transaction) {
            return [
                'id' => (int) $transaction->id,
                'type' => $transaction->type,
                'category_id' => (int) $transaction->category_id,
                'category_name' => $transaction->category?->name ?? 'Uncategorized',
                'amount' => (float) $transaction->amount,
                'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
                'description' => $transaction->description,
            ];
        })->values()->all();
    }

    public function createForUser(User $user, array $payload): Transaction
    {
        return DB::transaction(function () use ($user, $payload) {
            $category = $this->categoryForTransaction($user, $payload);
            $amount = (float) ($payload['amount'] ?? 0);

            return $user->transactions()->create([
                'category_id' => $category->id,
                'type' => $payload['type'],
                'amount' => $amount,
                'transaction_date' => $payload['transaction_date'],
                'description' => $payload['description'] ?? null,
            ])->load('category');
        });
    }

    public function updateForUser(User $user, Transaction $transaction, array $payload): Transaction
    {
        if ($transaction->user_id !== $user->id) {
            abort(404, 'Resource not found');
        }

        return DB::transaction(function () use ($user, $transaction, $payload) {
            $category = $this->categoryForTransaction($user, $payload);
            $transaction->update([
                'category_id' => $category->id,
                'type' => $payload['type'],
                'amount' => (float) $payload['amount'],
                'transaction_date' => $payload['transaction_date'],
                'description' => $payload['description'] ?? null,
            ]);

            return $transaction->refresh()->load('category');
        });
    }

    public function deleteForUser(User $user, Transaction $transaction): bool
    {
        if ($transaction->user_id !== $user->id) {
            abort(404, 'Resource not found');
        }

        return $transaction->delete();
    }

    private function categoryForTransaction(User $user, array $payload): Category
    {
        $category = Category::query()->find($payload['category_id'] ?? null);

        if (
            ! $category
            || ($category->user_id !== null && $category->user_id !== $user->id)
        ) {
            throw ValidationException::withMessages([
                'category_id' => ['Kategori yang dipilih tidak valid.'],
            ]);
        }

        if ($category->type !== ($payload['type'] ?? null)) {
            throw ValidationException::withMessages([
                'category_id' => ['Kategori yang dipilih tidak sesuai dengan tipe transaksi.'],
            ]);
        }

        $amount = (float) ($payload['amount'] ?? 0);
        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => ['Jumlah transaksi harus lebih dari 0.'],
            ]);
        }

        return $category;
    }
}
