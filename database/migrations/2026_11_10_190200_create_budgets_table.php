<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount_limit', 15, 2);
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->timestamps();

            $table->index(['user_id', 'year', 'month'], 'idx_budgets_user_period');
            $table->unique(['user_id', 'category_id', 'year', 'month'], 'unique_user_category_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
