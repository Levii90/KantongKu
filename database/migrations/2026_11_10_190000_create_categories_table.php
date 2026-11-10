<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->enum('type', ['income', 'expense']);
            $table->string('icon', 50)->nullable()->default('tag');
            $table->string('color', 20)->default('#6B7280');
            $table->boolean('is_system_default')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'type'], 'idx_categories_user_type');
            $table->unique(['user_id', 'name', 'type'], 'unique_user_category_name_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
