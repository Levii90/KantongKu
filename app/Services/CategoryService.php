<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    public function createForUser(User $user, array $payload): Category
    {
        return $user->categories()->create([
            'name' => $payload['name'],
            'type' => $payload['type'],
            'icon' => $payload['icon'] ?? 'tag',
            'color' => $payload['color'] ?? '#6B7280',
            'is_system_default' => false,
        ]);
    }

    public function updateForUser(User $user, Category $category, array $payload): Category
    {
        $this->ensureOwnedBy($user, $category);

        if (
            $category->type !== $payload['type']
            && ($category->transactions()->exists() || $category->budgets()->exists())
        ) {
            throw ValidationException::withMessages([
                'type' => ['Tipe kategori tidak dapat diubah karena sudah digunakan oleh transaksi atau budget.'],
            ]);
        }

        $category->update([
            'name' => $payload['name'],
            'type' => $payload['type'],
            'icon' => $payload['icon'] ?? 'tag',
            'color' => $payload['color'] ?? '#6B7280',
        ]);

        return $category->refresh();
    }

    public function deleteForUser(User $user, Category $category): bool
    {
        $this->ensureOwnedBy($user, $category);

        if ($category->transactions()->exists() || $category->budgets()->exists()) {
            throw ValidationException::withMessages([
                'category' => ['Kategori tidak dapat dihapus selama masih digunakan oleh transaksi atau budget.'],
            ]);
        }

        return $category->delete();
    }

    private function ensureOwnedBy(User $user, Category $category): void
    {
        if ($category->user_id !== $user->id || $category->is_system_default) {
            abort(404, 'Resource not found');
        }
    }
}
