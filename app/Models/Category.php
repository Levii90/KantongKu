<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'icon',
        'color',
        'is_system_default',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere(function ($query) use ($user) {
                    $query->whereNull('user_id')
                        ->whereNotExists(function ($query) use ($user) {
                            $query->selectRaw('1')
                                ->from('categories as user_categories')
                                ->whereColumn('user_categories.name', 'categories.name')
                                ->whereColumn('user_categories.type', 'categories.type')
                                ->where('user_categories.user_id', $user->id);
                        });
                });
        });
    }

    public static function copyDefaultCategoriesForUser(User $user): void
    {
        foreach (self::whereNull('user_id')->get() as $defaultCategory) {
            $exists = self::where('user_id', $user->id)
                ->where('name', $defaultCategory->name)
                ->where('type', $defaultCategory->type)
                ->exists();

            if ($exists) {
                continue;
            }

            self::create([
                'user_id' => $user->id,
                'name' => $defaultCategory->name,
                'type' => $defaultCategory->type,
                'icon' => $defaultCategory->icon,
                'color' => $defaultCategory->color,
                'is_system_default' => false,
            ]);
        }
    }
}
