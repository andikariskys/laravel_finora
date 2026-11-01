<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WalletController extends Controller
{
    /**
     * Tampilkan daftar dan manajemen seluruh dompet/rekening pengguna.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $typePriority = ['cash' => 1, 'bank' => 2, 'ewallet' => 3, 'investment' => 4];

        $wallets = $user->wallets()
            ->withCount('transactions')
            ->get()
            ->sortBy(function ($wallet) use ($typePriority) {
                $activeOrder = $wallet->is_active ? 0 : 10;
                $typeOrder = $typePriority[$wallet->type] ?? 9;
                return ($activeOrder * 10) + $typeOrder;
            })
            ->values();

        $totalActiveBalance = (float) $wallets->where('is_active', true)->sum('balance');
        $activeCount = $wallets->where('is_active', true)->count();
        $inactiveCount = $wallets->where('is_active', false)->count();

        // Riwayat aktivitas dompet (Transfer/Top Up, Penyesuaian Saldo, dan Saldo Awal Dompet)
        $walletActivities = $user->transactions()
            ->with('wallet')
            ->whereIn('type', ['transfer_out', 'transfer_in', 'balance_adjustment', 'initial_balance'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(20)
            ->get();

        return view('wallets.index', compact(
            'wallets',
            'totalActiveBalance',
            'activeCount',
            'inactiveCount',
            'walletActivities'
        ));
    }

    /**
     * Tambahkan dompet atau rekening baru dengan saldo awal.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:bank,ewallet,investment'],
            'initial_balance' => ['nullable', 'numeric', 'min:0'],
            'color' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'Nama dompet atau rekening wajib diisi.',
            'type.required' => 'Pilih jenis dompet.',
            'type.in' => 'Pilih jenis akun yang valid (Rekening Bank, Dompet Digital, atau Investasi).',
            'initial_balance.min' => 'Saldo awal minimal Rp 0.',
        ]);

        $initialBalance = (float) ($validated['initial_balance'] ?? 0);

        DB::transaction(function () use ($user, $validated, $initialBalance) {
            $wallet = $user->wallets()->create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'balance' => $initialBalance,
                'color' => $validated['color'] ?? 'emerald',
                'is_active' => true,
            ]);

            // Jika ada saldo awal, catat sebagai mutasi saldo awal
            if ($initialBalance > 0) {
                $user->transactions()->create([
                    'wallet_id' => $wallet->id,
                    'type' => 'initial_balance',
                    'amount' => $initialBalance,
                    'fee' => 0,
                    'source' => 'Saldo Awal Pembuatan Dompet',
                    'description' => "Saldo awal pembukaan dompet {$wallet->name}",
                    'transaction_date' => now()->toDateString(),
                ]);
            }
        });

        return redirect()->route('wallets.index')
            ->with('success', "Dompet {$validated['name']} berhasil ditambahkan.");
    }

    /**
     * Beralih status aktif / non-aktif dompet.
     */
    public function toggleActive(Wallet $wallet): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($wallet->user_id !== $user->id) {
            abort(403);
        }

        $wallet->is_active = ! $wallet->is_active;
        $wallet->save();

        $statusText = $wallet->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';

        return redirect()->route('wallets.index')
            ->with('success', "Dompet {$wallet->name} berhasil {$statusText}.");
    }

    /**
     * Penyesuaian saldo manual untuk pencocokan saldo riil.
     */
    public function adjustBalance(Request $request, Wallet $wallet): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($wallet->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'new_balance' => ['required', 'numeric', 'min:0'],
            'reason' => ['nullable', 'string', 'max:255'],
        ], [
            'new_balance.required' => 'Nominal saldo baru wajib diisi.',
            'new_balance.min' => 'Nominal saldo tidak boleh bernilai negatif.',
        ]);

        $oldBalance = (float) $wallet->balance;
        $newBalance = (float) $validated['new_balance'];
        $diff = $newBalance - $oldBalance;

        DB::transaction(function () use ($user, $wallet, $oldBalance, $newBalance, $diff, $validated) {
            $wallet->balance = $newBalance;
            $wallet->save();

            $reasonText = !empty($validated['reason']) ? " ({$validated['reason']})" : "";
            $diffText = ($diff >= 0 ? "+" : "-") . " Rp " . number_format(abs($diff), 0, ',', '.');

            $user->transactions()->create([
                'wallet_id' => $wallet->id,
                'type' => 'balance_adjustment',
                'amount' => abs($diff),
                'fee' => 0,
                'source' => 'Penyesuaian Saldo Manual',
                'description' => "Penyesuaian saldo dompet {$wallet->name} dari Rp " . number_format($oldBalance, 0, ',', '.') . " menjadi Rp " . number_format($newBalance, 0, ',', '.') . " [Selisih: {$diffText}]{$reasonText}",
                'transaction_date' => now()->toDateString(),
            ]);
        });

        return redirect()->route('wallets.index')
            ->with('success', "Saldo dompet {$wallet->name} berhasil disesuaikan menjadi Rp " . number_format($newBalance, 0, ',', '.') . ".");
    }

    /**
     * Transfer antar dompet atau top up saldo lengkap dengan biaya admin.
     */
    public function transfer(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'from_wallet_id' => ['required', 'integer', 'exists:wallets,id'],
            'to_wallet_id' => ['required', 'integer', 'exists:wallets,id', 'different:from_wallet_id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'fee' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'from_wallet_id.required' => 'Pilih dompet asal pemotongan.',
            'to_wallet_id.required' => 'Pilih dompet tujuan transfer.',
            'to_wallet_id.different' => 'Dompet tujuan harus berbeda dari dompet sumber.',
            'amount.required' => 'Nominal transfer wajib diisi.',
            'amount.min' => 'Nominal transfer minimal Rp 1.',
        ]);

        $fromWallet = $user->wallets()->where('is_active', true)->findOrFail($validated['from_wallet_id']);
        $toWallet = $user->wallets()->where('is_active', true)->findOrFail($validated['to_wallet_id']);

        $amount = (float) $validated['amount'];
        $fee = (float) ($validated['fee'] ?? 0);
        $totalDeduction = $amount + $fee;

        if ($fromWallet->balance < $totalDeduction) {
            return back()->withInput()->withErrors([
                'amount' => "Saldo {$fromWallet->name} (Rp " . number_format($fromWallet->balance, 0, ',', '.') . ") tidak mencukupi untuk transfer Rp " . number_format($amount, 0, ',', '.') . " ditambah biaya admin Rp " . number_format($fee, 0, ',', '.') . ".",
            ]);
        }

        DB::transaction(function () use ($user, $fromWallet, $toWallet, $amount, $fee, $totalDeduction, $validated) {
            $fromWallet->decrement('balance', $totalDeduction);
            $toWallet->increment('balance', $amount);

            $notesText = !empty($validated['notes']) ? " - {$validated['notes']}" : "";

            // 1. Transaksi keluar dari dompet asal
            $user->transactions()->create([
                'wallet_id' => $fromWallet->id,
                'type' => 'transfer_out',
                'amount' => $amount,
                'fee' => $fee,
                'source' => $toWallet->name,
                'description' => "Transfer ke {$toWallet->name}" . ($fee > 0 ? " (Biaya admin Rp " . number_format($fee, 0, ',', '.') . ")" : "") . $notesText,
                'transaction_date' => now()->toDateString(),
            ]);

            // 2. Transaksi masuk ke dompet tujuan
            $user->transactions()->create([
                'wallet_id' => $toWallet->id,
                'type' => 'transfer_in',
                'amount' => $amount,
                'fee' => 0,
                'source' => $fromWallet->name,
                'description' => "Penerimaan transfer dari {$fromWallet->name}" . $notesText,
                'transaction_date' => now()->toDateString(),
            ]);
        });

        return redirect()->route('wallets.index')
            ->with('success', "Transfer Rp " . number_format($amount, 0, ',', '.') . " dari {$fromWallet->name} ke {$toWallet->name} berhasil dilakukan.");
    }
}
