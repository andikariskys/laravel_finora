<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\RecurringRule;
use App\Models\User;
use App\Models\Wallet;
use App\Services\RecurringPaymentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecurringSchedulerTest extends TestCase
{
    use RefreshDatabase;

    public function test_artisan_command_processes_due_recurring_rules(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'BCA Utama',
            'type' => 'bank',
            'balance' => 1000000,
            'is_active' => true,
        ]);

        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Tagihan Rutin',
            'type' => 'expense',
        ]);

        // Aturan bulanan yang jatuh tempo pada tanggal 1
        $today = Carbon::create(2026, 11, 1);
        Carbon::setTestNow($today);

        $rule = RecurringRule::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'category_id' => $category->id,
            'name' => 'Spotify Premium',
            'amount' => 50000,
            'frequency' => 'monthly',
            'frequency_value' => '1',
            'execution_day' => 1,
            'last_executed_month' => null,
            'is_active' => true,
        ]);

        $this->artisan('finora:process-recurring')
            ->expectsOutputToContain('Berhasil memproses 1 tagihan rutin')
            ->assertExitCode(0);

        // Saldo berkurang
        $this->assertEquals(950000, $wallet->fresh()->balance);

        // Mutasi transaksi tercatat
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'recurring_expense',
            'amount' => 50000,
        ]);
        $this->assertEquals('2026-11-01', \App\Models\Transaction::where('type', 'recurring_expense')->first()->transaction_date->toDateString());

        // Status last_executed_month terisi
        $this->assertEquals('2026-11', $rule->fresh()->last_executed_month);

        // Eksekusi ulang pada hari yang sama tidak boleh memotong saldo ganda
        $this->artisan('finora:process-recurring')
            ->expectsOutputToContain('Tidak ada tagihan rutin yang perlu diproses hari ini')
            ->assertExitCode(0);

        $this->assertEquals(950000, $wallet->fresh()->balance);

        Carbon::setTestNow();
    }

    public function test_on_access_trigger_in_dashboard_executes_due_rule(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'GoPay',
            'type' => 'ewallet',
            'balance' => 200000,
            'is_active' => true,
        ]);

        $today = Carbon::create(2026, 11, 1);
        Carbon::setTestNow($today);

        $rule = RecurringRule::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'name' => 'YouTube Premium',
            'amount' => 59000,
            'frequency' => 'monthly',
            'frequency_value' => '1',
            'execution_day' => 1,
            'last_executed_month' => null,
            'is_active' => true,
        ]);

        // Mengakses dashboard akan memicu on-access trigger
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);

        $this->assertEquals(141000, $wallet->fresh()->balance);
        $this->assertEquals('2026-11', $rule->fresh()->last_executed_month);

        Carbon::setTestNow();
    }

    public function test_custom_date_rule_deactivates_after_execution(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'BCA',
            'type' => 'bank',
            'balance' => 500000,
            'is_active' => true,
        ]);

        $today = Carbon::create(2026, 11, 20);
        Carbon::setTestNow($today);

        $rule = RecurringRule::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'name' => 'Pajak Tahunan',
            'amount' => 150000,
            'frequency' => 'custom_date',
            'frequency_value' => '2026-11-20',
            'due_date' => '2026-11-20',
            'execution_day' => 20,
            'last_executed_month' => null,
            'is_active' => true,
        ]);

        $service = app(RecurringPaymentService::class);
        $executed = $service->processAll($today);

        $this->assertCount(1, $executed);
        $this->assertEquals(350000, $wallet->fresh()->balance);
        $this->assertFalse($rule->fresh()->is_active);

        Carbon::setTestNow();
    }
}
