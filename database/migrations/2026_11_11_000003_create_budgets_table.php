<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('budgets')) {
            Schema::create('budgets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
                $table->decimal('amount_limit', 15, 2);
                $table->unsignedTinyInteger('month');
                $table->unsignedSmallInteger('year');
                $table->timestamps();

                $table->index(['user_id', 'year', 'month'], 'idx_budgets_user_period');
                $table->unique(['user_id', 'category_id', 'year', 'month'], 'unique_user_category_period');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This duplicate migration does not own the table created by the earlier migration.
    }
};
