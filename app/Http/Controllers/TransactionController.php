<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Tampilkan formulir pencatatan dan daftar mutasi pemasukan/pengeluaran.
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $typeFilter = $request->query('type'); // all, income, expense

        $query = $user->transactions()
            ->with('wallet')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');

        if ($typeFilter && in_array($typeFilter, ['income', 'expense'])) {
            $query->where('type', $typeFilter);
        }

        $transactions = $query->paginate(20)->withQueryString();

        $wallets = $user->wallets()
            ->where('is_active', true)
            ->get()
            ->sortBy(function ($wallet) {
                $priority = ['cash' => 1, 'bank' => 2, 'ewallet' => 3];
                return $priority[$wallet->type] ?? 4;
            })
            ->values();

        return view('transactions.index', compact('transactions', 'wallets', 'typeFilter'));
    }

    /**
     * Simpan transaksi pemasukan atau pengeluaran baru.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'wallet_id' => ['required', 'integer', 'exists:wallets,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'fee' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],
            // Tanggal bebas tanpa batasan min/max untuk kompensasi pencatatan telat
            'transaction_date' => ['required', 'date'],
        ], [
            'type.required' => 'Pilih jenis mutasi (pemasukan atau pengeluaran).',
            'wallet_id.required' => 'Pilih dompet yang digunakan.',
            'amount.required' => 'Nominal transaksi wajib diisi.',
            'amount.min' => 'Nominal minimal Rp 1.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date' => 'Format tanggal transaksi tidak valid.',
        ]);

        $wallet = $user->wallets()->findOrFail($validated['wallet_id']);
        $amount = (float) $validated['amount'];
        $fee = (float) ($validated['fee'] ?? 0);

        DB::transaction(function () use ($user, $wallet, $validated, $amount, $fee) {
            $user->transactions()->create([
                'wallet_id' => $wallet->id,
                'category_id' => null,
                'type' => $validated['type'],
                'amount' => $amount,
                'fee' => $fee,
                'source' => $validated['source'] ?? null,
                'description' => $validated['description'] ?? null,
                'transaction_date' => $validated['transaction_date'],
            ]);

            if ($validated['type'] === 'income') {
                $wallet->increment('balance', $amount);
            } else {
                $wallet->decrement('balance', $amount + $fee);
            }
        });

        $label = $validated['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran';

        return redirect()->route('transactions.index')
            ->with('success', "{$label} berhasil disimpan ke dompet {$wallet->name}.");
    }
}
