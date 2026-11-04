<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    /**
     * Unduh atau pratinjau dokumen PDF riwayat transaksi.
     */
    public function exportPdf(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $period = $request->query('period', 'all');
        $action = $request->query('action', 'view'); // 'view' (inline) atau 'download'

        $bulanIndonesia = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $query = $user->transactions()
            ->with('wallet')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');

        if ($period !== 'all' && preg_match('/^\d{4}-\d{2}$/', $period)) {
            [$year, $month] = explode('-', $period);
            $startDate = Carbon::createFromDate((int) $year, (int) $month, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::createFromDate((int) $year, (int) $month, 1)->endOfMonth()->toDateString();
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
            $periodLabel = ($bulanIndonesia[(int) $month] ?? $month) . ' ' . $year;
            $filename = "laporan-mutasi-{$period}.pdf";
        } else {
            $periodLabel = 'Semua Riwayat Transaksi';
            $filename = 'laporan-semua-riwayat-mutasi-finora.pdf';
        }

        $transactions = $query->get();

        // Ambil seluruh dompet pengguna dan filter sesuai aktivitas pada periode ini:
        // - Dompet aktif selalu ditampilkan.
        // - Dompet non-aktif hanya ditampilkan jika memiliki riwayat transaksi pada periode laporan.
        $allWallets = $user->wallets()
            ->orderBy('name')
            ->get();

        $wallets = $allWallets->filter(function ($wallet) use ($transactions) {
            if ($wallet->is_active) {
                return true;
            }

            return $transactions->contains('wallet_id', $wallet->id);
        })->values();

        $totalIncome = (float) $transactions->where('type', 'income')->sum('amount');
        $totalExpense = (float) $transactions->whereIn('type', ['expense', 'recurring_expense'])->sum(function ($tx) {
            return (float) $tx->amount + (float) ($tx->fee ?? 0);
        });
        $netCashflow = $totalIncome - $totalExpense;
        $totalWalletBalance = (float) $wallets->sum('balance');

        $pdf = Pdf::loadView('history.pdf', compact(
            'user',
            'transactions',
            'wallets',
            'totalWalletBalance',
            'periodLabel',
            'totalIncome',
            'totalExpense',
            'netCashflow'
        ))->setPaper('a4', 'portrait');

        if ($action === 'download') {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }
}
