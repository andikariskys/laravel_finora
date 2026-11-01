<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\RecurringPaymentService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessRecurringExpenses extends Command
{
    /**
     * Nama dan signature perintah konsol.
     *
     * @var string
     */
    protected $signature = 'finora:process-recurring {--user= : ID pengguna spesifik yang ingin diproses}';

    /**
     * Deskripsi perintah konsol.
     *
     * @var string
     */
    protected $description = 'Memproses dan memotong pengeluaran tagihan rutin otomatis yang telah jatuh tempo (Finora Scheduler)';

    /**
     * Jalankan perintah konsol.
     */
    public function handle(RecurringPaymentService $service): int
    {
        $userId = $this->option('user');
        $today = Carbon::today();

        $this->info("Finora Scheduler: Memeriksa tagihan rutin untuk tanggal {$today->toDateString()}...");

        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("Pengguna dengan ID {$userId} tidak ditemukan.");
                return self::FAILURE;
            }

            $executed = $service->processForUser($user, $today);
        } else {
            $executed = $service->processAll($today);
        }

        $count = $executed->count();

        if ($count === 0) {
            $this->line("Tidak ada tagihan rutin yang perlu diproses hari ini.");
            return self::SUCCESS;
        }

        $this->newLine();
        $this->info("Berhasil memproses {$count} tagihan rutin:");

        $tableData = $executed->map(function ($rule) {
            return [
                'ID' => $rule->id,
                'Pengguna' => $rule->user?->name ?? "User #{$rule->user_id}",
                'Nama Tagihan' => $rule->name,
                'Frekuensi' => ucfirst($rule->frequency),
                'Dompet' => $rule->wallet?->name ?? '-',
                'Nominal' => 'Rp ' . number_format((float) $rule->amount, 0, ',', '.'),
            ];
        });

        $this->table(['ID', 'Pengguna', 'Nama Tagihan', 'Frekuensi', 'Dompet', 'Nominal'], $tableData);

        return self::SUCCESS;
    }
}
