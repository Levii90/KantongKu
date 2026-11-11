<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FinancialInsight;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
    }

    public function test_api_supports_owned_category_and_transaction_crud(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $token = $user->createToken('crud-test')->plainTextToken;
        $headers = ['Authorization' => 'Bearer '.$token];

        $categoryResponse = $this->withHeaders($headers)->postJson('/api/v1/categories', [
            'name' => 'Freelance',
            'type' => 'income',
            'icon' => 'briefcase',
            'color' => '#22AA77',
        ])->assertCreated();
        $categoryId = $categoryResponse->json('data.id');

        $this->withHeaders($headers)->getJson('/api/v1/categories/'.$categoryId)
            ->assertOk()
            ->assertJsonPath('data.name', 'Freelance');

        $this->withHeaders($headers)->putJson('/api/v1/categories/'.$categoryId, [
            'name' => 'Proyek',
            'type' => 'income',
            'icon' => 'wallet',
            'color' => '#3366FF',
        ])->assertOk()->assertJsonPath('data.name', 'Proyek');
        $transaction = $this->withHeaders($headers)->postJson('/api/v1/transactions', [
            'type' => 'income',
            'category_id' => $categoryId,
            'amount' => 1000000,
            'transaction_date' => '2026-11-11',
            'description' => 'Pekerjaan pertama',
        ])->assertCreated()->json('data');

        $this->withHeaders($headers)->putJson('/api/v1/transactions/'.$transaction['id'], [
            'type' => 'income',
            'category_id' => $categoryId,
            'amount' => 1500000,
            'transaction_date' => '2026-11-12',
            'description' => 'Pekerjaan diperbarui',
        ])->assertOk()->assertJsonPath('data.amount', 1500000);

        $this->withHeaders($headers)->putJson('/api/v1/categories/'.$categoryId, [
            'name' => 'Proyek',
            'type' => 'expense',
            'icon' => 'wallet',
            'color' => '#3366FF',
        ])->assertUnprocessable();

        $otherCategory = Category::create([
            'user_id' => $otherUser->id,
            'name' => 'Income private',
            'type' => 'income',
            'is_system_default' => false,
        ]);
        $this->withHeaders($headers)->putJson('/api/v1/categories/'.$otherCategory->id, [
            'name' => 'Unauthorized edit',
            'type' => 'income',
        ])->assertNotFound();
        $this->withHeaders($headers)->getJson('/api/v1/categories/'.$otherCategory->id)->assertNotFound();

        $otherTransaction = Transaction::create([
            'user_id' => $otherUser->id,
            'category_id' => Category::query()->whereNull('user_id')->where('type', 'income')->firstOrFail()->id,
            'type' => 'income',
            'amount' => 30000,
            'transaction_date' => '2026-11-11',
        ]);
        $this->withHeaders($headers)->deleteJson('/api/v1/transactions/'.$otherTransaction->id)->assertNotFound();

        $this->withHeaders($headers)->deleteJson('/api/v1/categories/'.$categoryId)->assertUnprocessable();
        $this->withHeaders($headers)->deleteJson('/api/v1/transactions/'.$transaction['id'])->assertOk();
        $this->withHeaders($headers)->deleteJson('/api/v1/categories/'.$categoryId)->assertOk();
        $this->assertDatabaseMissing('categories', ['id' => $categoryId]);
    }

    public function test_api_supports_manual_insight_crud_and_owner_scoping(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $token = $user->createToken('insight-test')->plainTextToken;
        $headers = ['Authorization' => 'Bearer '.$token];

        $insight = $this->withHeaders($headers)->postJson('/api/v1/insights', [
            'message' => 'Sisihkan 10 persen pemasukan.',
            'month' => 11,
            'year' => 2026,
        ])->assertCreated()->json('data');

        $this->withHeaders($headers)->getJson('/api/v1/insights')
            ->assertOk()
            ->assertJsonPath('data.0.message', 'Sisihkan 10 persen pemasukan.');

        $this->withHeaders($headers)->putJson('/api/v1/insights/'.$insight['id'], [
            'message' => 'Sisihkan 15 persen pemasukan.',
            'month' => 12,
            'year' => 2026,
        ])->assertOk()->assertJsonPath('data.message', 'Sisihkan 15 persen pemasukan.');

        $otherInsight = FinancialInsight::create([
            'user_id' => $otherUser->id,
            'message' => 'Catatan privat pengguna lain',
            'month' => 11,
            'year' => 2026,
        ]);
        $this->withHeaders($headers)->getJson('/api/v1/insights/'.$otherInsight->id)->assertNotFound();
        $this->withHeaders($headers)->deleteJson('/api/v1/insights/'.$otherInsight->id)->assertNotFound();

        $this->withHeaders($headers)->deleteJson('/api/v1/insights/'.$insight['id'])->assertOk();
        $this->assertDatabaseMissing('financial_insights', ['id' => $insight['id']]);
    }

    public function test_web_pages_support_category_budget_transaction_and_insight_crud(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Transport pribadi',
            'type' => 'expense',
            'icon' => 'car',
            'color' => '#22AA77',
            'is_system_default' => false,
        ]);
        $this->actingAs($user);

        $this->get('/categories')->assertOk()->assertSee('Transport pribadi');
        $this->put('/categories/'.$category->id, [
            'name' => 'Transport',
            'type' => 'expense',
            'icon' => 'car',
            'color' => '#22AA77',
        ])->assertRedirect('/categories');
        $this->post('/categories', [
            'name' => 'Pendapatan proyek',
            'type' => 'income',
            'icon' => 'briefcase',
            'color' => '#2255AA',
        ])->assertRedirect('/categories');
        $createdCategory = $user->categories()->where('name', 'Pendapatan proyek')->firstOrFail();
        $this->get('/categories/'.$createdCategory->id.'/edit')->assertOk()->assertSee('Edit Kategori');
        $this->delete('/categories/'.$createdCategory->id)->assertRedirect('/categories');

        $this->post('/budgets', [
            'category_id' => $category->id,
            'amount_limit' => 250000,
            'month' => 11,
            'year' => 2026,
        ])->assertRedirect();
        $budget = $user->budgets()->firstOrFail();
        $budgetToken = $user->createToken('budget-crud-test')->plainTextToken;
        $this->withHeader('Authorization', 'Bearer '.$budgetToken)
            ->getJson('/api/v1/budgets/'.$budget->id)
            ->assertOk()
            ->assertJsonPath('data.amount_limit', 250000);
        $this->get('/budgets')->assertOk()->assertSee('Transport');
        $this->get('/budgets/'.$budget->id.'/edit')->assertOk()->assertSee('Edit Anggaran');
        $this->put('/budgets/'.$budget->id, [
            'category_id' => $category->id,
            'amount_limit' => 300000,
            'month' => 11,
            'year' => 2026,
        ])->assertRedirect('/budgets');
        $this->delete('/budgets/'.$budget->id)->assertRedirect();

        $this->post('/transactions', [
            'type' => 'expense',
            'category_id' => $category->id,
            'amount' => 50000,
            'transaction_date' => '2026-11-11',
            'description' => 'Parkir',
        ])->assertRedirect();
        $transaction = $user->transactions()->firstOrFail();
        $this->get('/transactions')->assertOk()->assertSee('Parkir')->assertSee('Edit');
        $this->get('/transactions/'.$transaction->id.'/edit')->assertOk()->assertSee('Edit Transaksi');
        $this->put('/transactions/'.$transaction->id, [
            'type' => 'expense',
            'category_id' => $category->id,
            'amount' => 60000,
            'transaction_date' => '2026-11-12',
            'description' => 'Parkir diperbarui',
        ])->assertRedirect('/transactions');

        $this->post('/insights', [
            'message' => 'Kurangi belanja impulsif.',
            'month' => 11,
            'year' => 2026,
        ])->assertRedirect('/insights');
        $insight = $user->financialInsights()->firstOrFail();
        $this->get('/insights')->assertOk()->assertSee('Kurangi belanja impulsif.');
        $this->get('/insights/'.$insight->id.'/edit')->assertOk()->assertSee('Edit Insight');
        $this->put('/insights/'.$insight->id, [
            'message' => 'Evaluasi pengeluaran mingguan.',
            'month' => 11,
            'year' => 2026,
        ])->assertRedirect('/insights');

        $this->delete('/transactions/'.$transaction->id)->assertRedirect();
        $this->delete('/insights/'.$insight->id)->assertRedirect('/insights');
        $this->delete('/categories/'.$category->id)->assertRedirect('/categories');

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
        $this->assertDatabaseMissing('financial_insights', ['id' => $insight->id]);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
