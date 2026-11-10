<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasOversizedCategory = DB::table('categories')
            ->get(['name', 'icon'])
            ->contains(fn (object $category) => mb_strlen($category->name) > 100
                || ($category->icon !== null && mb_strlen($category->icon) > 50));

        if ($hasOversizedCategory) {
            throw new RuntimeException('Category names or icons exceed the database specification limits.');
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->string('name', 100)->change();
            $table->string('icon', 50)->nullable()->default('tag')->change();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name')->change();
            $table->string('icon')->default('tag')->change();
        });
    }
};
