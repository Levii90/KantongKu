<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
    }

    public function test_user_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Hafid Hidayat',
            'email' => 'hafid@example.com',
            'password' => 'secretpassword',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.user.email', 'hafid@example.com')
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'email'],
                ],
            ]);
    }

    public function test_user_can_login_and_receive_a_stateless_api_token(): void
    {
        User::factory()->create([
            'email' => 'hafid@example.com',
            'password' => 'secretpassword',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'HAFID@example.com',
            'password' => 'secretpassword',
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.user.email', 'hafid@example.com')
            ->assertJsonStructure(['data' => ['token', 'user' => ['id', 'name', 'email']]]);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_registration_rejects_case_insensitive_duplicate_emails(): void
    {
        User::factory()->create(['email' => 'hafid@example.com']);

        $this->postJson('/api/v1/auth/register', [
            'name' => 'Duplicate User',
            'email' => 'HAFID@example.com',
            'password' => 'secretpassword',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('status', 'error')
            ->assertJsonStructure(['errors' => ['email']]);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_authenticated_user_can_view_dashboard_and_reports(): void
    {
        $user = User::factory()->create();
        $category = Category::where('user_id', $user->id)->first() ?? Category::query()->whereNull('user_id')->first();

        if ($category && $category->user_id === null) {
            $category = Category::create([
                'user_id' => $user->id,
                'name' => $category->name,
                'type' => $category->type,
                'icon' => $category->icon,
                'color' => $category->color,
                'is_system_default' => false,
            ]);
        }

        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/dashboard/summary?month=11&year=2026')
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/budgets', [
                'category_id' => $category->id,
                'amount_limit' => 1500000,
                'month' => 11,
                'year' => 2026,
            ])
            ->assertStatus(201)
            ->assertJsonPath('status', 'success');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/transactions', [
                'type' => 'expense',
                'category_id' => $category->id,
                'amount' => 75000,
                'transaction_date' => '2026-11-11',
                'description' => 'Pembelian perlengkapan kantor',
            ])
            ->assertStatus(201)
            ->assertJsonPath('status', 'success');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/reports/summary?start_date=2026-11-01&end_date=2026-11-30')
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/categories?type=expense')
            ->assertOk();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/budgets?month=11&year=2026')
            ->assertOk();
    }

    public function test_budget_update_and_delete_are_limited_to_the_owner(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = $this->createCategory($user, 'expense');
        $secondCategory = Category::create([
            'user_id' => $user->id,
            'name' => 'Second expense test',
            'type' => 'expense',
            'is_system_default' => false,
        ]);
        $otherCategory = $this->createCategory($otherUser, 'expense');
        $budget = $user->budgets()->create([
            'category_id' => $category->id,
            'amount_limit' => 100000,
            'month' => 11,
            'year' => 2026,
        ]);
        $user->budgets()->create([
            'category_id' => $secondCategory->id,
            'amount_limit' => 200000,
            'month' => 12,
            'year' => 2026,
        ]);
        $otherBudget = $otherUser->budgets()->create([
            'category_id' => $otherCategory->id,
            'amount_limit' => 200000,
            'month' => 11,
            'year' => 2026,
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/budgets/'.$budget->id, [
                'category_id' => $category->id,
                'amount_limit' => 125000,
                'month' => 12,
                'year' => 2026,
            ])
            ->assertOk()
            ->assertJsonPath('data.amount_limit', 125000);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/budgets/'.$budget->id, [
                'category_id' => $secondCategory->id,
                'amount_limit' => 125000,
                'month' => 12,
                'year' => 2026,
            ])
            ->assertUnprocessable()
            ->assertJsonPath('status', 'error')
            ->assertJsonStructure(['errors' => ['category_id']]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/budgets/'.$otherBudget->id, [
                'category_id' => $category->id,
                'amount_limit' => 125000,
                'month' => 12,
                'year' => 2026,
            ])
            ->assertNotFound();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/budgets/'.$otherBudget->id)
            ->assertNotFound();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/budgets/'.$budget->id)
            ->assertOk();

        $this->assertDatabaseMissing('budgets', ['id' => $budget->id]);
        $this->assertDatabaseHas('budgets', ['id' => $otherBudget->id]);
    }

    public function test_budgets_reject_invalid_categories_and_duplicate_periods(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $expenseCategory = $this->createCategory($user, 'expense');
        $incomeCategory = $this->createCategory($user, 'income');
        $otherCategory = $this->createCategory($otherUser, 'expense');
        $user->budgets()->create([
            'category_id' => $expenseCategory->id,
            'amount_limit' => 100000,
            'month' => 11,
            'year' => 2026,
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        foreach ([$incomeCategory, $otherCategory, $expenseCategory] as $category) {
            $this->withHeader('Authorization', 'Bearer '.$token)
                ->postJson('/api/v1/budgets', [
                    'category_id' => $category->id,
                    'amount_limit' => 50000,
                    'month' => 11,
                    'year' => 2026,
                ])
                ->assertUnprocessable()
                ->assertJsonPath('status', 'error')
                ->assertJsonStructure(['errors' => ['category_id']]);
        }
    }

    public function test_dashboard_and_report_filters_are_validated(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/dashboard/summary?month=13&year=2026')
            ->assertUnprocessable()
            ->assertJsonPath('status', 'error')
            ->assertJsonStructure(['errors' => ['month']]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/reports/summary?start_date=2026-12-01&end_date=2026-11-30')
            ->assertUnprocessable()
            ->assertJsonPath('status', 'error')
            ->assertJsonStructure(['errors' => ['end_date']]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/budgets?month=13&year=2026')
            ->assertUnprocessable()
            ->assertJsonPath('status', 'error')
            ->assertJsonStructure(['errors' => ['month']]);
    }

    public function test_report_monthly_trends_respect_the_exact_date_range(): void
    {
        $user = User::factory()->create();
        $category = Category::query()->whereNull('user_id')->where('type', 'expense')->firstOrFail();

        foreach ([
            ['amount' => 900, 'date' => '2026-10-05'],
            ['amount' => 100, 'date' => '2026-10-15'],
            ['amount' => 800, 'date' => '2026-10-25'],
        ] as $entry) {
            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'type' => 'expense',
                'amount' => $entry['amount'],
                'transaction_date' => $entry['date'],
            ]);
        }

        $token = $user->createToken('test-token')->plainTextToken;
        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/reports/summary?start_date=2026-10-10&end_date=2026-10-20')
            ->assertOk()
            ->assertJsonPath('data.total_expense', 100)
            ->assertJsonPath('data.monthly_trends.0.month', '2026-10')
            ->assertJsonPath('data.monthly_trends.0.expense', 100);
    }

    public function test_category_list_does_not_duplicate_user_copies_of_defaults(): void
    {
        $user = User::factory()->create();
        Category::copyDefaultCategoriesForUser($user);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/categories')
            ->assertOk();
        $categories = $response->json('data');
        $categoryKeys = array_map(
            fn (array $category) => $category['type'].':'.$category['name'],
            $categories,
        );

        $this->assertCount(Category::query()->whereNull('user_id')->count(), $categories);
        $this->assertCount(count(array_unique($categoryKeys)), $categoryKeys);
    }

    private function createCategory(User $user, string $type): Category
    {
        return Category::create([
            'user_id' => $user->id,
            'name' => ucfirst($type).' test',
            'type' => $type,
            'is_system_default' => false,
        ]);
    }
}
