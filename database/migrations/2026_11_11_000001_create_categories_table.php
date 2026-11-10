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
        if (! Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('name', 100);
                $table->enum('type', ['income', 'expense']);
                $table->string('icon', 50)->nullable()->default('tag');
                $table->string('color', 20)->nullable()->default('#6B7280');
                $table->boolean('is_system_default')->default(false);
                $table->timestamps();

                $table->index(['user_id', 'type'], 'idx_categories_user_type');
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
