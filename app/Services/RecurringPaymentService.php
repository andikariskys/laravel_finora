<?php

namespace App\Services;

use App\Models\RecurringRule;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecurringPaymentService
{
    /**
     * Memproses seluruh aturan tagihan rutin yang aktif dan telah jatuh tempo.
     *
     * @param Carbon|null $date
     * @return Collection Daftar aturan yang berhasil dieksekusi
     */
    public function processAll(?Carbon $date = null): Collection
    {
        $date = $date ? $date->copy() : Carbon::today();

        $rules = RecurringRule::with(['wallet', 'user'])
            ->where('is_active', true)
            ->get();

        $executed = collect();

        foreach ($rules as $rule) {
            if ($this->shouldExecute($rule, $date)) {
                $success = $this->executeRule($rule, $date);
                if ($success) {
                    $executed->push($rule);
                }
            }
        }

        return $executed;
    }

    /**
     * Memproses aturan tagihan rutin khusus untuk satu pengguna tertentu (On-Access Trigger).
     */
    public function processForUser(User $user, ?Carbon $date = null): Collection
    {
        $date = $date ? $date->copy() : Carbon::today();

        $rules = $user->recurringRules()
            ->with('wallet')
            ->where('is_active', true)
            ->get();

        $executed = collect();

        foreach ($rules as $rule) {
            if ($this->shouldExecute($rule, $date)) {
                $success = $this->executeRule($rule, $date);
                if ($success) {
                    $executed->push($rule);
                }
            }
        }

        return $executed;
    }

    /**
     * Memeriksa apakah aturan tagihan tertentu telah jatuh tempo pada tanggal yang ditentukan.
     */
    public function shouldExecute(RecurringRule $rule, Carbon $date): bool
    {
        if (!$rule->is_active || !$rule->wallet) {
            return false;
        }

        $currentMonth = $date->format('Y-m');

        switch ($rule->frequency) {
            case 'daily':
                // Cek apakah sudah pernah dipotong pada hari ini
                return !Transaction::where('wallet_id', $rule->wallet_id)
                    ->where('type', 'recurring_expense')
                    ->where('source', 'Tagihan: ' . $rule->name)
                    ->whereDate('transaction_date', $date->toDateString())
                    ->exists();

            case 'weekly':
                // Cocokkan apakah hari ini >= hari eksekusi dalam minggu berjalan
                // execution_day: 1 (Senin) s.d. 7 (Minggu)
                if ($date->dayOfWeekIso < (int) $rule->execution_day) {
                    return false;
                }

                $startOfWeek = $date->copy()->startOfWeek()->startOfDay()->toDateTimeString();
                $endOfWeek = $date->copy()->endOfWeek()->endOfDay()->toDateTimeString();

                return !Transaction::where('wallet_id', $rule->wallet_id)
                    ->where('type', 'recurring_expense')
                    ->where('source', 'Tagihan: ' . $rule->name)
                    ->whereBetween('transaction_date', [$startOfWeek, $endOfWeek])
                    ->exists();

            case 'monthly':
                // Eksekusi jika tanggal hari ini >= tanggal eksekusi dan belum dieksekusi bulan ini
                if ($date->day < (int) $rule->execution_day) {
                    return false;
                }

                return $rule->last_executed_month !== $currentMonth;

            case 'custom_date':
                if (!$rule->due_date) {
                    return false;
                }

                $dueDate = Carbon::parse($rule->due_date);
                if ($date->toDateString() < $dueDate->toDateString()) {
                    return false;
                }

                return $rule->last_executed_month === null;

            default:
                return false;
        }
    }

    /**
     * Eksekusi pemotongan saldo dan pencatatan transaksi mutasi untuk aturan yang jatuh tempo.
     */
    public function executeRule(RecurringRule $rule, Carbon $date): bool
    {
        return DB::transaction(function () use ($rule, $date) {
            $wallet = $rule->wallet;
            if (!$wallet) {
                return false;
            }

            // 1. Kurangi saldo dompet penampung
            $wallet->decrement('balance', $rule->amount);

            // 2. Catat riwayat mutasi transaksi pengeluaran rutin
            $rule->user->transactions()->create([
                'wallet_id' => $wallet->id,
                'category_id' => $rule->category_id,
                'type' => 'recurring_expense',
                'amount' => $rule->amount,
                'fee' => 0,
                'source' => 'Tagihan: ' . $rule->name,
                'description' => "Pemotongan otomatis tagihan rutin: {$rule->name}",
                'transaction_date' => $date->toDateString(),
            ]);

            // 3. Perbarui status rule
            $updates = [
                'last_executed_month' => $date->format('Y-m'),
            ];

            // Tagihan dengan tanggal khusus dianggap selesai setelah satu kali eksekusi
            if ($rule->frequency === 'custom_date') {
                $updates['is_active'] = false;
            }

            $rule->update($updates);

            return true;
        });
    }
}
