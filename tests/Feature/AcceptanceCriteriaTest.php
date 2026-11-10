<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcceptanceCriteriaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
    }

    /**
     * TC-AUTH-01: Login dengan kredensial yang salah harus return HTTP 422
     * dan menampilkan pesan validasi error pada API maupun error bag pada web.
     */
    public function test_tc_auth_01_invalid_credentials_returns_422(): void
    {
        // 1. Test API endpoint
        $apiResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'wrong@example.com',
            'password' => 'invalidpassword',
        ]);

        $apiResponse->assertStatus(422)
            ->assertJsonPath('status', 'error')
            ->assertJsonStructure(['errors' => ['email']]);

        // 2. Test Web route
        $webResponse = $this->from('/login')->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'invalidpassword',
        ]);

        $webResponse->assertRedirect('/login');
        $webResponse->assertSessionHasErrors('email');
    }

    /**
     * TC-DASH-01: Dashboard ketika pengguna baru belum memiliki transaksi
     * harus menampilkan Total Saldo Rp 0, status 200, dan empty state.
     */
    public function test_tc_dash_01_empty_dashboard_shows_zero_balance_and_empty_state(): void
    {
        $user = User::factory()->create();

        // 1. Test Web Dashboard View
        $webResponse = $this->actingAs($user)->get('/dashboard');
        $webResponse->assertOk()
            ->assertSee('Total Saldo')
            ->assertSee('Rp 0')
            ->assertSee('Belum ada data transaksi');

        // 2. Test API Dashboard Summary
        $token = $user->createToken('test')->plainTextToken;
        $apiResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/dashboard/summary?month=11&year=2026');

        $apiResponse->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.current_balance', 0)
            ->assertJsonPath('data.total_income_this_month', 0)
            ->assertJsonPath('data.total_expense_this_month', 0)
            ->assertJsonPath('data.recent_transactions', []);
    }

    /**
     * TC-TX-01: Form input transaksi & pembuatan transaksi
     * Verifikasi backend menerima input dan form memiliki guard attribute.
     */
    public function test_tc_tx_01_transaction_creation_and_attributes(): void
    {
        $user = User::factory()->create();
        $category = Category::where('user_id', $user->id)->first() ?? Category::first();

        // 1. Web View contains double click guard
        $createView = $this->actingAs($user)->get('/transactions/create');
        $createView->assertOk()
            ->assertSee('isSubmitting')
            ->assertSee(':disabled="isSubmitting"', false);

        // 2. Store via Web POST
        $postResponse = $this->actingAs($user)->post('/transactions', [
            'type' => $category->type,
            'amount' => 50000,
            'category_id' => $category->id,
            'transaction_date' => '2026-11-11',
            'description' => 'Makan siang uji coba',
        ]);

        $postResponse->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 50000,
            'category_id' => $category->id,
        ]);
    }

    /**
     * TC-REP-01: Filter rentang tanggal pada laporan
     * Grafik tren dan persentase kategori diperbarui sesuai rentang tanggal.
     */
    public function test_tc_rep_01_reports_date_range_filtering(): void
    {
        $user = User::factory()->create();
        $expenseCategory = Category::where('type', 'expense')->first();

        // Buat transaksi di bulan Oktober
        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $expenseCategory->id,
            'type' => 'expense',
            'amount' => 150000,
            'transaction_date' => '2026-10-15',
            'description' => 'Transaksi Oktober',
        ]);

        // Buat transaksi di bulan November
        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $expenseCategory->id,
            'type' => 'expense',
            'amount' => 200000,
            'transaction_date' => '2026-11-05',
            'description' => 'Transaksi November',
        ]);

        // Filter rentang bulan Oktober
        $response = $this->actingAs($user)->get('/reports?start_date=2026-10-01&end_date=2026-10-31');
        $response->assertOk()
            ->assertSee('Rp 150.000')
            ->assertDontSee('Rp 200.000');

        // Test API Endpoint
        $token = $user->createToken('test')->plainTextToken;
        $apiResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/reports/summary?start_date=2026-10-01&end_date=2026-10-31');

        $apiResponse->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.total_expense', 150000);
    }

    /**
     * TC-SEC-01: Pengguna terautentikasi mencoba mengakses ID transaksi milik pengguna lain melalui API
     * Expected Output: HTTP 404 (Resource not found, mencegah IDOR & kebocoran ID).
     */
    public function test_tc_sec_01_cannot_access_other_users_transaction(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $category = Category::first();

        $transactionB = Transaction::create([
            'user_id' => $userB->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 99000,
            'transaction_date' => '2026-11-10',
            'description' => 'Rahasia User B',
        ]);

        $tokenA = $userA->createToken('token-a')->plainTextToken;

        // User A mencoba mengakses transaksi milik User B
        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenA)
            ->getJson('/api/v1/transactions/' . $transactionB->id);

        $response->assertStatus(404);
    }
}
