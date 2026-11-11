<?php

namespace App\Services;

use App\Models\FinancialInsight;
use App\Models\User;

class FinancialInsightService
{
    public function listForUser(User $user, ?int $month = null, ?int $year = null): array
    {
        $query = $user->financialInsights()->orderByDesc('year')->orderByDesc('month')->orderByDesc('id');

        if ($month !== null) {
            $query->where('month', $month);
        }

        if ($year !== null) {
            $query->where('year', $year);
        }

        return $query->get()->all();
    }

    public function createForUser(User $user, array $payload): FinancialInsight
    {
        return $user->financialInsights()->create($payload);
    }

    public function updateForUser(User $user, FinancialInsight $insight, array $payload): FinancialInsight
    {
        $this->ensureOwnedBy($user, $insight);
        $insight->update($payload);

        return $insight->refresh();
    }

    public function deleteForUser(User $user, FinancialInsight $insight): bool
    {
        $this->ensureOwnedBy($user, $insight);

        return $insight->delete();
    }

    private function ensureOwnedBy(User $user, FinancialInsight $insight): void
    {
        if ($insight->user_id !== $user->id) {
            abort(404, 'Resource not found');
        }
    }
}
