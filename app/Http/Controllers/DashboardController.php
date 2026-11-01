<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Loan;
use App\Models\RecurringRule;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan antarmuka utama ringkasan finansial Finora.
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Ambil HANYA dompet aktif untuk dashboard dengan urutan: Uang Tunai, Bank, Dompet Digital
        $typePriority = [
            'cash' => 1,
            'bank' => 2,
            'ewallet' => 3,
            'investment' => 4,
        ];

        $wallets = $user->wallets()
            ->where('is_active', true)
            ->get()
            ->sortBy(function ($wallet) use ($typePriority) {
                return $typePriority[$wallet->type] ?? 9;
            })
            ->values();

        $activeWallets = $wallets;
        $totalBalance = (float) $activeWallets->sum('balance');

        // 2. Tentukan periode cutoff bulanan pengguna
        $now = Carbon::now();
        $resetDay = max(1, min(28, (int) ($user->monthly_reset_day ?? 1)));

        if ($now->day >= $resetDay) {
            $periodStart = Carbon::create($now->year, $now->month, $resetDay)->startOfDay();
            $periodEnd = (clone $periodStart)->addMonth()->subSecond();
        } else {
            $periodStart = Carbon::create($now->year, $now->month, $resetDay)->subMonth()->startOfDay();
            $periodEnd = Carbon::create($now->year, $now->month, $resetDay)->subSecond();
        }

        $startDateStr = $periodStart->toDateString();
        $endDateStr = $periodEnd->toDateString();

        // 3. Hitung Pemasukan dan Pengeluaran periode berjalan
        $periodIncome = (float) $user->transactions()
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$startDateStr, $endDateStr])
            ->sum('amount');

        $periodExpense = (float) $user->transactions()
            ->whereIn('type', ['expense', 'recurring_expense'])
            ->whereBetween('transaction_date', [$startDateStr, $endDateStr])
            ->selectRaw('SUM(amount + fee) as total')
            ->value('total') ?? 0.0;

        $netCashflow = $periodIncome - $periodExpense;

        // Saldo Awal (Opening Balance) periode berjalan
        $openingBalance = max(0, $totalBalance - $netCashflow);

        // 4. Daftar 3 transaksi mutasi terbaru
        $recentTransactions = $user->transactions()
            ->with(['wallet', 'category'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(3)
            ->get();

        // 5. Aturan Berulang / Langganan Aktif
        $recurringRules = $user->recurringRules()
            ->with('wallet')
            ->where('is_active', true)
            ->orderBy('execution_day')
            ->take(4)
            ->get();

        // 6. Pinjaman & Hutang Aktif
        $activeLoans = $user->loans()
            ->with('wallet')
            ->where('status', '!=', 'paid')
            ->orderBy('due_date')
            ->take(4)
            ->get();

        $totalReceivable = (float) $user->loans()
            ->where('type', 'loan_given')
            ->where('status', '!=', 'paid')
            ->sum('remaining_amount');

        $totalDebt = (float) $user->loans()
            ->where('type', 'debt_taken')
            ->where('status', '!=', 'paid')
            ->sum('remaining_amount');

        // Kategori untuk dropdown quick action modal
        $categories = $user->categories()->orderBy('name')->get();

        // Nama Bulan & Tahun dalam Bahasa Indonesia
        $bulanIndonesia = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $periodLabel = $bulanIndonesia[$periodStart->month] . ' ' . $periodStart->year;

        return view('dashboard', compact(
            'user',
            'wallets',
            'activeWallets',
            'totalBalance',
            'openingBalance',
            'periodIncome',
            'periodExpense',
            'netCashflow',
            'periodLabel',
            'recentTransactions',
            'recurringRules',
            'activeLoans',
            'totalReceivable',
            'totalDebt',
            'categories',
            'resetDay'
        ));
    }

    /**
     * Perbarui tanggal reset cutoff bulanan pengguna (1 s.d. 28).
     */
    public function updateResetDay(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'monthly_reset_day' => ['required', 'integer', 'min:1', 'max:28'],
        ], [
            'monthly_reset_day.required' => 'Pilih tanggal reset bulanan.',
            'monthly_reset_day.integer' => 'Tanggal reset harus berupa angka.',
            'monthly_reset_day.min' => 'Tanggal reset minimal tanggal 1.',
            'monthly_reset_day.max' => 'Tanggal reset maksimal tanggal 28 agar konsisten setiap bulan.',
        ]);

        $user->update([
            'monthly_reset_day' => $validated['monthly_reset_day'],
        ]);

        return redirect()->route('dashboard')
            ->with('success', "Tanggal reset bulanan berhasil diubah menjadi tanggal {$validated['monthly_reset_day']} setiap bulan.");
    }
}
