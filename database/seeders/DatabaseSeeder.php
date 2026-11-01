<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Loan;
use App\Models\RecurringRule;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Hapus akun lama jika ada
        User::where('email', 'rangga@finora.test')->delete();

        // Akun Utama Pengguna Uji Coba Finora
        $user = User::updateOrCreate(
            ['email' => 'admin@email.com'],
            [
                'name' => 'Admin Test',
                'password' => Hash::make('admin@123'),
                'monthly_reset_day' => 1,
                'theme_preference' => 'light',
            ]
        );

        // Hapus data dummy lama milik user jika sudah ada agar bersih
        $user->transactions()->delete();
        $user->loans()->delete();
        $user->recurringRules()->delete();
        $user->categories()->delete();
        $user->wallets()->delete();

        // 1. Kategori Finansial
        $catGaji = Category::create([
            'user_id' => $user->id,
            'name' => 'Gaji & Pendapatan',
            'type' => 'income',
            'color' => 'emerald',
        ]);

        $catProyek = Category::create([
            'user_id' => $user->id,
            'name' => 'Proyek Lepas',
            'type' => 'income',
            'color' => 'teal',
        ]);

        $catMakan = Category::create([
            'user_id' => $user->id,
            'name' => 'Makanan & Minuman',
            'type' => 'expense',
            'color' => 'rose',
        ]);

        $catTransport = Category::create([
            'user_id' => $user->id,
            'name' => 'Transportasi',
            'type' => 'expense',
            'color' => 'amber',
        ]);

        $catTagihan = Category::create([
            'user_id' => $user->id,
            'name' => 'Tagihan & Langganan',
            'type' => 'expense',
            'color' => 'indigo',
        ]);

        $catBelanja = Category::create([
            'user_id' => $user->id,
            'name' => 'Kebutuhan Rumah & Belanja',
            'type' => 'expense',
            'color' => 'purple',
        ]);

        $catAdmin = Category::create([
            'user_id' => $user->id,
            'name' => 'Biaya Admin Transfer',
            'type' => 'expense',
            'color' => 'slate',
        ]);

        // 2. Multi-Wallet
        $bca = Wallet::create([
            'user_id' => $user->id,
            'name' => 'BCA Utama',
            'type' => 'bank',
            'balance' => 8450000,
            'color' => 'blue',
            'icon' => 'building-library',
            'is_active' => true,
        ]);

        $mandiri = Wallet::create([
            'user_id' => $user->id,
            'name' => 'Mandiri Bisnis',
            'type' => 'bank',
            'balance' => 4200000,
            'color' => 'indigo',
            'icon' => 'building-library',
            'is_active' => true,
        ]);

        $gopay = Wallet::create([
            'user_id' => $user->id,
            'name' => 'GoPay',
            'type' => 'ewallet',
            'balance' => 750000,
            'color' => 'emerald',
            'icon' => 'device-phone-mobile',
            'is_active' => true,
        ]);

        $cash = Wallet::create([
            'user_id' => $user->id,
            'name' => 'Uang Tunai (Dompet)',
            'type' => 'cash',
            'balance' => 450000,
            'color' => 'amber',
            'icon' => 'banknotes',
            'is_active' => true,
        ]);

        // 3. Mutasi Transaksi Bulan Berjalan
        $today = Carbon::today();

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $bca->id,
            'category_id' => $catGaji->id,
            'type' => 'income',
            'amount' => 12500000,
            'fee' => 0,
            'source' => 'Gaji Bulanan PT Digital Media',
            'description' => 'Transfer payroll awal bulan',
            'transaction_date' => $today->copy()->subDays(3)->toDateString(),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $mandiri->id,
            'category_id' => $catProyek->id,
            'type' => 'income',
            'amount' => 3500000,
            'fee' => 0,
            'source' => 'Klien Website Portofolio',
            'description' => 'Pembayaran termin kedua proyek frontend',
            'transaction_date' => $today->copy()->subDays(2)->toDateString(),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $bca->id,
            'category_id' => $catBelanja->id,
            'type' => 'expense',
            'amount' => 680000,
            'fee' => 0,
            'description' => 'Belanja bulanan sembako supermarket',
            'transaction_date' => $today->copy()->subDays(2)->toDateString(),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $gopay->id,
            'category_id' => $catMakan->id,
            'type' => 'expense',
            'amount' => 45000,
            'fee' => 0,
            'description' => 'Makan siang dan es kopi susu',
            'transaction_date' => $today->copy()->subDay()->toDateString(),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $cash->id,
            'category_id' => $catTransport->id,
            'type' => 'expense',
            'amount' => 50000,
            'fee' => 0,
            'description' => 'Isi bensin pertamax motor',
            'transaction_date' => $today->toDateString(),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $bca->id,
            'category_id' => $catAdmin->id,
            'type' => 'expense',
            'amount' => 200000,
            'fee' => 1000,
            'description' => 'Top-up saldo GoPay dari BCA (Biaya admin Rp 1.000)',
            'transaction_date' => $today->toDateString(),
        ]);

        // 4. Aturan Biaya Rutin / Langganan (Harian, Mingguan, Bulanan, Tanggal Khusus)
        RecurringRule::create([
            'user_id' => $user->id,
            'wallet_id' => $gopay->id,
            'category_id' => $catTagihan->id,
            'name' => 'Spotify Premium Family',
            'amount' => 86900,
            'frequency' => 'monthly',
            'frequency_value' => '1',
            'execution_day' => 1,
            'last_executed_month' => $today->format('Y-m'),
            'is_active' => true,
        ]);

        RecurringRule::create([
            'user_id' => $user->id,
            'wallet_id' => $mandiri->id,
            'category_id' => $catTagihan->id,
            'name' => 'Sewa Server VPS Cloud',
            'amount' => 180000,
            'frequency' => 'monthly',
            'frequency_value' => '5',
            'execution_day' => 5,
            'last_executed_month' => null,
            'is_active' => true,
        ]);

        RecurringRule::create([
            'user_id' => $user->id,
            'wallet_id' => $bca->id,
            'category_id' => $catAdmin->id,
            'name' => 'Biaya Admin Bulanan BCA',
            'amount' => 15000,
            'frequency' => 'monthly',
            'frequency_value' => '25',
            'execution_day' => 25,
            'last_executed_month' => null,
            'is_active' => true,
        ]);

        RecurringRule::create([
            'user_id' => $user->id,
            'wallet_id' => $cash->id,
            'category_id' => $catTransport->id,
            'name' => 'Parkir Harian Kantor',
            'amount' => 5000,
            'frequency' => 'daily',
            'frequency_value' => 'daily',
            'execution_day' => 1,
            'last_executed_month' => null,
            'is_active' => true,
        ]);

        RecurringRule::create([
            'user_id' => $user->id,
            'wallet_id' => $gopay->id,
            'category_id' => $catTagihan->id,
            'name' => 'Langganan Laundry Mingguan',
            'amount' => 45000,
            'frequency' => 'weekly',
            'frequency_value' => 'friday',
            'execution_day' => 5,
            'last_executed_month' => null,
            'is_active' => true,
        ]);

        RecurringRule::create([
            'user_id' => $user->id,
            'wallet_id' => $bca->id,
            'category_id' => $catTagihan->id,
            'name' => 'Pajak Kendaraan Tahunan',
            'amount' => 380000,
            'frequency' => 'custom_date',
            'frequency_value' => '2026-11-20',
            'due_date' => '2026-11-20',
            'execution_day' => 20,
            'last_executed_month' => null,
            'is_active' => true,
        ]);

        // 5. Pinjaman & Hutang
        Loan::create([
            'user_id' => $user->id,
            'wallet_id' => $bca->id,
            'type' => 'loan_given', // Piutang
            'person_name' => 'Dimas Saputra',
            'total_amount' => 500000,
            'remaining_amount' => 250000,
            'due_date' => $today->copy()->addDays(5)->toDateString(),
            'status' => 'partial',
            'notes' => 'Pinjaman talangan servis laptop (sudah dicicil separuh)',
        ]);

        Loan::create([
            'user_id' => $user->id,
            'wallet_id' => $cash->id,
            'type' => 'debt_taken', // Hutang
            'person_name' => 'Sarah Wijaya',
            'total_amount' => 120000,
            'remaining_amount' => 120000,
            'due_date' => $today->copy()->addDays(10)->toDateString(),
            'status' => 'unpaid',
            'notes' => 'Talangan patungan kado rekan kerja',
        ]);
    }
}
