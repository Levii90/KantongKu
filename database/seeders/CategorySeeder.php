<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['name' => 'Gaji', 'type' => 'income', 'icon' => 'wallet', 'color' => '#10B981'],
            ['name' => 'Makanan & Minuman', 'type' => 'expense', 'icon' => 'utensils', 'color' => '#EF4444'],
            ['name' => 'Transportasi', 'type' => 'expense', 'icon' => 'car', 'color' => '#3B82F6'],
            ['name' => 'Tagihan & Utilitas', 'type' => 'expense', 'icon' => 'bolt', 'color' => '#F59E0B'],
            ['name' => 'Hiburan', 'type' => 'expense', 'icon' => 'ticket', 'color' => '#8B5CF6'],
            ['name' => 'Belanja', 'type' => 'expense', 'icon' => 'shopping-cart', 'color' => '#EC4899'],
            ['name' => 'Kesehatan', 'type' => 'expense', 'icon' => 'heart', 'color' => '#14B8A6'],
            ['name' => 'Pendidikan', 'type' => 'expense', 'icon' => 'book', 'color' => '#6366F1'],
            ['name' => 'Investasi', 'type' => 'income', 'icon' => 'trending-up', 'color' => '#22C55E'],
        ];

        foreach ($defaults as $category) {
            Category::firstOrCreate([
                'user_id' => null,
                'name' => $category['name'],
                'type' => $category['type'],
            ], [
                'icon' => $category['icon'],
                'color' => $category['color'],
                'is_system_default' => true,
            ]);
        }
    }
}
