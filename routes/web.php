<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\RecurringController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // 1. Dashboard Utama (Ringkasan) & Pengaturan Tanggal Reset
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/reset-day', [DashboardController::class, 'updateResetDay'])->name('dashboard.reset-day');

    // 2. Halaman Mutasi Transaksi (Pemasukan & Pengeluaran)
    Route::get('/mutasi', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/mutasi', [TransactionController::class, 'store'])->name('transactions.store');

    // 3. Halaman Manajemen Dompet & Rekening (Tambah, Toggle, Penyesuaian Saldo, Transfer)
    Route::get('/dompet', [WalletController::class, 'index'])->name('wallets.index');
    Route::post('/dompet', [WalletController::class, 'store'])->name('wallets.store');
    Route::patch('/dompet/{wallet}/toggle', [WalletController::class, 'toggleActive'])->name('wallets.toggle');
    Route::post('/dompet/{wallet}/adjust', [WalletController::class, 'adjustBalance'])->name('wallets.adjust');
    Route::post('/dompet/transfer', [WalletController::class, 'transfer'])->name('wallets.transfer');

    // 4. Halaman Tagihan & Biaya Rutin (Harian, Mingguan, Bulanan, Tanggal Khusus)
    Route::get('/tagihan', [RecurringController::class, 'index'])->name('recurring.index');
    Route::post('/tagihan', [RecurringController::class, 'store'])->name('recurring.store');
    Route::patch('/tagihan/{recurringRule}/toggle', [RecurringController::class, 'toggle'])->name('recurring.toggle');
    Route::delete('/tagihan/{recurringRule}', [RecurringController::class, 'destroy'])->name('recurring.destroy');

    // 5. Halaman Hutang & Piutang
    Route::get('/hutang-piutang', [LoanController::class, 'index'])->name('loans.index');
    Route::post('/hutang-piutang', [LoanController::class, 'store'])->name('loans.store');
    Route::post('/hutang-piutang/{loan}/repay', [LoanController::class, 'repay'])->name('loans.repay');

    // 6. Halaman Riwayat Laporan Keuangan Bulanan
    Route::get('/riwayat', [HistoryController::class, 'index'])->name('history.index');
});
