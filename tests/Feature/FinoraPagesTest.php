<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Loan;
use App\Models\RecurringRule;
use App\Models\Transaction;
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

    public function test_mutasi_semua_only_displays_income_and_expense_mutations(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'BCA Utama',
            'type' => 'bank',
            'balance' => 5000000,
            'is_active' => true,
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'income',
            'amount' => 5000000,
            'description' => 'Gaji Pokok Kantor',
            'transaction_date' => now()->toDateString(),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'expense',
            'amount' => 25000,
            'description' => 'Makan Siang Soto',
            'transaction_date' => now()->toDateString(),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'initial_balance',
            'amount' => 1000000,
            'description' => 'Saldo Awal Dompet: BCA Utama',
            'transaction_date' => now()->toDateString(),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'balance_adjustment',
            'amount' => 50000,
            'description' => 'Penyesuaian Saldo: BCA Utama',
            'transaction_date' => now()->toDateString(),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'transfer_out',
            'amount' => 200000,
            'description' => 'Transfer Saldo ke Gopay',
            'transaction_date' => now()->toDateString(),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'transfer_in',
            'amount' => 200000,
            'description' => 'Transfer Saldo dari BCA Utama',
            'transaction_date' => now()->toDateString(),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'recurring_expense',
            'amount' => 60000,
            'description' => 'Pemotongan otomatis tagihan rutin: Spotify Premium',
            'transaction_date' => now()->toDateString(),
        ]);

        // 1. Opsi Semua (Default)
        $response = $this->actingAs($user)->get('/mutasi');
        $response->assertStatus(200);
        $response->assertSee('Gaji Pokok Kantor');
        $response->assertSee('Makan Siang Soto');
        $response->assertDontSee('Saldo Awal Dompet: BCA Utama');
        $response->assertDontSee('Penyesuaian Saldo: BCA Utama');
        $response->assertDontSee('Transfer Saldo ke Gopay');
        $response->assertDontSee('Transfer Saldo dari BCA Utama');
        $response->assertDontSee('Pemotongan otomatis tagihan rutin: Spotify Premium');

        // 2. Filter Pemasukan
        $responseIncome = $this->actingAs($user)->get('/mutasi?type=income');
        $responseIncome->assertStatus(200);
        $responseIncome->assertSee('Gaji Pokok Kantor');
        $responseIncome->assertDontSee('Makan Siang Soto');
        $responseIncome->assertDontSee('Saldo Awal Dompet: BCA Utama');

        // 3. Filter Pengeluaran
        $responseExpense = $this->actingAs($user)->get('/mutasi?type=expense');
        $responseExpense->assertStatus(200);
        $responseExpense->assertSee('Makan Siang Soto');
        $responseExpense->assertDontSee('Gaji Pokok Kantor');
        $responseExpense->assertDontSee('Pemotongan otomatis tagihan rutin: Spotify Premium');
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

    public function test_mutasi_ajax_returns_json_html_for_spa(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'BCA',
            'type' => 'bank',
            'balance' => 1000000,
            'is_active' => true,
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'income',
            'amount' => 500000,
            'description' => 'Freelance Design',
            'transaction_date' => now()->toDateString(),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'expense',
            'amount' => 50000,
            'description' => 'Beli Kuota Data',
            'transaction_date' => now()->toDateString(),
        ]);

        // AJAX request Semua
        $responseAll = $this->actingAs($user)->getJson('/mutasi');
        $responseAll->assertStatus(200);
        $responseAll->assertJsonStructure(['html', 'type']);
        $this->assertStringContainsString('Freelance Design', $responseAll->json('html'));
        $this->assertStringContainsString('Beli Kuota Data', $responseAll->json('html'));

        // AJAX request Pemasukan
        $responseIncome = $this->actingAs($user)->getJson('/mutasi?type=income');
        $responseIncome->assertStatus(200);
        $this->assertStringContainsString('Freelance Design', $responseIncome->json('html'));
        $this->assertStringNotContainsString('Beli Kuota Data', $responseIncome->json('html'));

        // AJAX request Pengeluaran
        $responseExpense = $this->actingAs($user)->getJson('/mutasi?type=expense');
        $responseExpense->assertStatus(200);
        $this->assertStringContainsString('Beli Kuota Data', $responseExpense->json('html'));
        $this->assertStringNotContainsString('Freelance Design', $responseExpense->json('html'));
    }

    public function test_history_pdf_can_be_viewed_and_downloaded(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'BCA',
            'type' => 'bank',
            'balance' => 1000000,
            'is_active' => true,
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'income',
            'amount' => 2000000,
            'description' => 'Penjualan Barang',
            'transaction_date' => now()->toDateString(),
        ]);

        // 1. Pratinjau PDF Semua Riwayat (Inline Stream)
        $responseViewAll = $this->actingAs($user)->get('/riwayat/pdf?period=all&action=view');
        $responseViewAll->assertStatus(200);
        $this->assertEquals('application/pdf', $responseViewAll->headers->get('Content-Type'));
        $this->assertStringContainsString('inline', $responseViewAll->headers->get('Content-Disposition'));

        // 2. Unduh PDF Semua Riwayat (Attachment Download)
        $responseDownloadAll = $this->actingAs($user)->get('/riwayat/pdf?period=all&action=download');
        $responseDownloadAll->assertStatus(200);
        $this->assertEquals('application/pdf', $responseDownloadAll->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $responseDownloadAll->headers->get('Content-Disposition'));

        // 3. Pratinjau PDF Periode Spesifik Bulan Tertentu
        $periodKey = now()->format('Y-m');
        $responsePeriod = $this->actingAs($user)->get("/riwayat/pdf?period={$periodKey}&action=view");
        $responsePeriod->assertStatus(200);
        $this->assertEquals('application/pdf', $responsePeriod->headers->get('Content-Type'));
    }

    public function test_history_pdf_filters_inactive_wallets_correctly(): void
    {
        $user = User::factory()->create();

        // 1. Dompet aktif
        $walletActive = Wallet::create([
            'user_id' => $user->id,
            'name' => 'BCA Aktif',
            'type' => 'bank',
            'balance' => 1000000,
            'is_active' => true,
        ]);

        // 2. Dompet dinonaktifkan, tetapi MEMILIKI transaksi bulan ini
        $walletInactiveWithTx = Wallet::create([
            'user_id' => $user->id,
            'name' => 'GoPay Nonaktif Berisi Tx',
            'type' => 'ewallet',
            'balance' => 50000,
            'is_active' => false,
        ]);

        // 3. Dompet dinonaktifkan, dan TIDAK MEMILIKI transaksi bulan ini
        $walletInactiveWithoutTx = Wallet::create([
            'user_id' => $user->id,
            'name' => 'Mandiri Nonaktif Kosong',
            'type' => 'bank',
            'balance' => 0,
            'is_active' => false,
        ]);

        $periodKey = now()->format('Y-m');

        // Catat transaksi pada Dompet 2 di bulan ini
        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $walletInactiveWithTx->id,
            'type' => 'expense',
            'amount' => 20000,
            'description' => 'Beli Pulsa Sebelum Dinonaktifkan',
            'transaction_date' => now()->toDateString(),
        ]);

        // Endpoint PDF dapat diakses
        $response = $this->actingAs($user)->get("/riwayat/pdf?period={$periodKey}&action=view");
        $response->assertStatus(200);

        // Uji penyaringan dompet
        $transactions = $user->transactions()
            ->whereBetween('transaction_date', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString()
            ])
            ->get();

        $wallets = $user->wallets()->get()->filter(function ($w) use ($transactions) {
            return $w->is_active || $transactions->contains('wallet_id', $w->id);
        })->values();

        $this->assertTrue($wallets->contains('id', $walletActive->id));
        $this->assertTrue($wallets->contains('id', $walletInactiveWithTx->id));
        $this->assertFalse($wallets->contains('id', $walletInactiveWithoutTx->id));

        // Uji rendering baris total saldo dompet pada view PDF
        $totalWalletBalance = (float) $wallets->sum('balance');
        $view = view('history.pdf', [
            'user' => $user,
            'transactions' => $transactions,
            'wallets' => $wallets,
            'totalWalletBalance' => $totalWalletBalance,
            'periodLabel' => 'November 2026',
            'totalIncome' => 0,
            'totalExpense' => 20000,
            'netCashflow' => -20000,
        ])->render();

        $this->assertStringContainsString('Total Saldo Semua Dompet', $view);
        $this->assertStringContainsString('1.050.000', $view);
    }
}
