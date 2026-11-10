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
        if (! Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
                $table->enum('type', ['income', 'expense']);
                $table->decimal('amount', 15, 2);
                $table->date('transaction_date');
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'transaction_date'], 'idx_transactions_user_date');
                $table->index(['user_id', 'type'], 'idx_transactions_user_type');
                $table->index('category_id', 'idx_transactions_category');
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
