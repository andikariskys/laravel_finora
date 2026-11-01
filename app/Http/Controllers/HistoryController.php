<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HistoryController extends Controller
{
    /**
     * Tampilkan riwayat keuangan bulanan sebelumnya dalam format tabel dengan tombol demo unduh PDF.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $bulanIndonesia = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Buat daftar 6 bulan terakhir untuk ikhtisar riwayat
        $historyData = [];
        $now = Carbon::now();

        for ($i = 0; $i < 6; $i++) {
            $monthDate = (clone $now)->subMonths($i);
            $startDate = $monthDate->copy()->startOfMonth()->toDateString();
            $endDate = $monthDate->copy()->endOfMonth()->toDateString();

            $income = (float) $user->transactions()
                ->where('type', 'income')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount');

            $expense = (float) $user->transactions()
                ->whereIn('type', ['expense', 'recurring_expense'])
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->selectRaw('SUM(amount + fee) as total')
                ->value('total') ?? 0.0;

            $txCount = $user->transactions()
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->count();

            $net = $income - $expense;

            $historyData[] = [
                'period_key' => $monthDate->format('Y-m'),
                'period_label' => $bulanIndonesia[$monthDate->month] . ' ' . $monthDate->year,
                'income' => $income,
                'expense' => $expense,
                'net' => $net,
                'tx_count' => $txCount,
                'is_current' => ($i === 0),
            ];
        }

        return view('history.index', compact('historyData'));
    }
}
