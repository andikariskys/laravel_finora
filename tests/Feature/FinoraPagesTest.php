<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Loan;
use App\Models\RecurringRule;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinoraPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_mutasi_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/mutasi');
        $response->assertStatus(200);
        $response->assertSee('Mutasi Pemasukan');
    }

    public function test_can_record_transaction_with_past_date(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'BCA Utama',
            'type' => 'bank',
            'balance' => 1000000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/mutasi', [
            'type' => 'expense',
            'wallet_id' => $wallet->id,
            'amount' => 50000,
            'fee' => 1000,
            'description' => 'Makan malam kemarin',
            // Tanggal lampau (kompensasi telat catat)
            'transaction_date' => '2026-09-15',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'expense',
            'amount' => 50000,
            'fee' => 1000,
        ]);

        // Saldo berkurang 50000 + 1000
        $this->assertEquals(949000, $wallet->fresh()->balance);
    }

    public function test_dompet_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dompet');
        $response->assertStatus(200);
        $response->assertSee('Manajemen Dompet');
        $response->assertSee('Riwayat Aktivitas Dompet');
    }

    public function test_can_create_new_wallet(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/dompet', [
            'name' => 'BCA Utama',
            'type' => 'bank',
            'initial_balance' => 250000,
        ]);

        $response->assertRedirect(route('wallets.index'));
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'name' => 'BCA Utama',
            'type' => 'bank',
            'balance' => 250000,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'initial_balance',
            'amount' => 250000,
        ]);
    }

    public function test_cannot_create_cash_wallet_via_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/dompet', [
            'name' => 'Uang Tunai Cadangan',
            'type' => 'cash',
            'initial_balance' => 100000,
        ]);

        $response->assertSessionHasErrors(['type']);
        $this->assertDatabaseMissing('wallets', [
            'user_id' => $user->id,
            'name' => 'Uang Tunai Cadangan',
        ]);
    }

    public function test_can_toggle_wallet_active_status(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'GoPay',
            'type' => 'ewallet',
            'balance' => 50000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->patch("/dompet/{$wallet->id}/toggle");
        $response->assertRedirect(route('wallets.index'));
        $this->assertFalse($wallet->fresh()->is_active);
    }

    public function test_hutang_piutang_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/hutang-piutang');
        $response->assertStatus(200);
        $response->assertSee('Pencatatan Hutang');
    }

    public function test_can_record_new_loan(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'Mandiri',
            'type' => 'bank',
            'balance' => 500000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/hutang-piutang', [
            'type' => 'loan_given',
            'person_name' => 'Dimas',
            'wallet_id' => $wallet->id,
            'total_amount' => 100000,
            'due_date' => '2026-11-10',
            'notes' => 'Pinjaman beli buku',
        ]);

        $response->assertRedirect(route('loans.index'));
        $this->assertDatabaseHas('loans', [
            'user_id' => $user->id,
            'person_name' => 'Dimas',
            'type' => 'loan_given',
            'total_amount' => 100000,
            'remaining_amount' => 100000,
            'status' => 'unpaid',
        ]);

        // Karena pinjamkan (piutang), saldo berkurang
        $this->assertEquals(400000, $wallet->fresh()->balance);
    }

    public function test_riwayat_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/riwayat');
        $response->assertStatus(200);
        $response->assertSee('Riwayat Laporan Keuangan Bulanan');
        $response->assertSee('Unduh PDF');
    }

    public function test_can_update_monthly_reset_day(): void
    {
        $user = User::factory()->create(['monthly_reset_day' => 1]);

        $response = $this->actingAs($user)->post('/dashboard/reset-day', [
            'monthly_reset_day' => 25,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals(25, $user->fresh()->monthly_reset_day);
    }

    public function test_can_adjust_wallet_balance(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'Dompet Fisik',
            'type' => 'cash',
            'balance' => 150000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post("/dompet/{$wallet->id}/adjust", [
            'new_balance' => 200000,
            'reason' => 'Koreksi uang saku',
        ]);

        $response->assertRedirect(route('wallets.index'));
        $this->assertEquals(200000, $wallet->fresh()->balance);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'balance_adjustment',
            'amount' => 50000,
        ]);
    }

    public function test_can_transfer_between_wallets_with_admin_fee(): void
    {
        $user = User::factory()->create();
        $source = Wallet::create([
            'user_id' => $user->id,
            'name' => 'BCA',
            'type' => 'bank',
            'balance' => 500000,
            'is_active' => true,
        ]);

        $dest = Wallet::create([
            'user_id' => $user->id,
            'name' => 'GoPay',
            'type' => 'ewallet',
            'balance' => 50000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/dompet/transfer', [
            'from_wallet_id' => $source->id,
            'to_wallet_id' => $dest->id,
            'amount' => 100000,
            'fee' => 1000,
            'notes' => 'Top up harian',
        ]);

        $response->assertRedirect(route('wallets.index'));

        // Saldo sumber berkurang amount + fee (500000 - 101000 = 399000)
        $this->assertEquals(399000, $source->fresh()->balance);

        // Saldo tujuan bertambah amount (50000 + 100000 = 150000)
        $this->assertEquals(150000, $dest->fresh()->balance);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $source->id,
            'type' => 'transfer_out',
            'amount' => 100000,
            'fee' => 1000,
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $dest->id,
            'type' => 'transfer_in',
            'amount' => 100000,
            'fee' => 0,
        ]);
    }

    public function test_tagihan_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/tagihan');
        $response->assertStatus(200);
        $response->assertSee('Pengeluaran Rutin');
    }

    public function test_can_create_recurring_rules_with_different_frequencies(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'Bank Mandiri',
            'type' => 'bank',
            'balance' => 1000000,
            'is_active' => true,
        ]);

        // 1. Bulanan
        $response = $this->actingAs($user)->post('/tagihan', [
            'name' => 'Netflix Premium',
            'wallet_id' => $wallet->id,
            'amount' => 186000,
            'frequency' => 'monthly',
            'monthly_day' => 15,
        ]);
        $response->assertRedirect(route('recurring.index'));

        $this->assertDatabaseHas('recurring_rules', [
            'user_id' => $user->id,
            'name' => 'Netflix Premium',
            'frequency' => 'monthly',
            'frequency_value' => '15',
            'execution_day' => 15,
        ]);

        // 2. Mingguan
        $response = $this->actingAs($user)->post('/tagihan', [
            'name' => 'Laundry Mingguan',
            'wallet_id' => $wallet->id,
            'amount' => 40000,
            'frequency' => 'weekly',
            'weekly_day' => 'friday',
        ]);
        $response->assertRedirect(route('recurring.index'));

        $this->assertDatabaseHas('recurring_rules', [
            'user_id' => $user->id,
            'name' => 'Laundry Mingguan',
            'frequency' => 'weekly',
            'frequency_value' => 'friday',
        ]);

        // 3. Tanggal Khusus
        $response = $this->actingAs($user)->post('/tagihan', [
            'name' => 'Pajak Motor',
            'wallet_id' => $wallet->id,
            'amount' => 250000,
            'frequency' => 'custom_date',
            'due_date' => '2026-11-20',
        ]);
        $response->assertRedirect(route('recurring.index'));

        $this->assertDatabaseHas('recurring_rules', [
            'user_id' => $user->id,
            'name' => 'Pajak Motor',
            'frequency' => 'custom_date',
        ]);
    }

    public function test_can_toggle_and_delete_recurring_rule(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'BCA',
            'type' => 'bank',
            'balance' => 500000,
            'is_active' => true,
        ]);

        $rule = RecurringRule::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'name' => 'Spotify',
            'amount' => 54000,
            'frequency' => 'monthly',
            'frequency_value' => '1',
            'execution_day' => 1,
            'is_active' => true,
        ]);

        // Toggle
        $response = $this->actingAs($user)->patch("/tagihan/{$rule->id}/toggle");
        $response->assertRedirect(route('recurring.index'));
        $this->assertFalse($rule->fresh()->is_active);

        // Delete
        $response = $this->actingAs($user)->delete("/tagihan/{$rule->id}");
        $response->assertRedirect(route('recurring.index'));
        $this->assertDatabaseMissing('recurring_rules', ['id' => $rule->id]);
    }
}
