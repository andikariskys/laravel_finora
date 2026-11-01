<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoanController extends Controller
{
    /**
     * Tampilkan daftar dan manajemen hutang piutang pengguna.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $loans = $user->loans()
            ->with(['wallet', 'repayments'])
            ->orderByRaw("CASE WHEN status != 'paid' THEN 0 ELSE 1 END")
            ->orderBy('due_date', 'asc')
            ->get();

        $totalReceivable = (float) $loans->where('type', 'loan_given')->where('status', '!=', 'paid')->sum('remaining_amount');
        $totalDebt = (float) $loans->where('type', 'debt_taken')->where('status', '!=', 'paid')->sum('remaining_amount');

        $wallets = $user->wallets()
            ->where('is_active', true)
            ->get()
            ->sortBy(function ($wallet) {
                $priority = ['cash' => 1, 'bank' => 2, 'ewallet' => 3];
                return $priority[$wallet->type] ?? 4;
            })
            ->values();

        return view('loans.index', compact('loans', 'totalReceivable', 'totalDebt', 'wallets'));
    }

    /**
     * Catat pinjaman yang diberikan (piutang) atau hutang baru.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'type' => ['required', 'in:loan_given,debt_taken'],
            'person_name' => ['required', 'string', 'max:150'],
            'wallet_id' => ['required', 'integer', 'exists:wallets,id'],
            'total_amount' => ['required', 'numeric', 'min:1'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'type.required' => 'Pilih jenis pinjaman (piutang atau hutang).',
            'person_name.required' => 'Nama rekan atau pihak terkait wajib diisi.',
            'wallet_id.required' => 'Pilih dompet yang terhubung.',
            'total_amount.required' => 'Nominal pinjaman wajib diisi.',
            'total_amount.min' => 'Nominal pinjaman minimal Rp 1.',
        ]);

        $wallet = $user->wallets()->findOrFail($validated['wallet_id']);
        $amount = (float) $validated['total_amount'];

        DB::transaction(function () use ($user, $wallet, $validated, $amount) {
            $loan = $user->loans()->create([
                'wallet_id' => $wallet->id,
                'type' => $validated['type'],
                'person_name' => $validated['person_name'],
                'total_amount' => $amount,
                'remaining_amount' => $amount,
                'due_date' => $validated['due_date'] ?? null,
                'status' => 'unpaid',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Jika Piutang (loan_given): saldo berkurang karena dipinjamkan
            // Jika Hutang (debt_taken): saldo bertambah karena menerima pinjaman
            if ($validated['type'] === 'loan_given') {
                $wallet->decrement('balance', $amount);
                $txType = 'expense';
                $desc = "Memberikan pinjaman kepada {$validated['person_name']}";
            } else {
                $wallet->increment('balance', $amount);
                $txType = 'income';
                $desc = "Menerima pinjaman dari {$validated['person_name']}";
            }

            $user->transactions()->create([
                'wallet_id' => $wallet->id,
                'type' => $txType,
                'amount' => $amount,
                'fee' => 0,
                'source' => $validated['person_name'],
                'description' => $desc,
                'transaction_date' => now()->toDateString(),
                'related_loan_id' => $loan->id,
            ]);
        });

        $label = $validated['type'] === 'loan_given' ? 'Piutang' : 'Hutang';

        return redirect()->route('loans.index')
            ->with('success', "Catatan {$label} untuk {$validated['person_name']} berhasil disimpan.");
    }

    /**
     * Catat pelunasan atau cicilan pinjaman.
     */
    public function repay(Request $request, Loan $loan): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($loan->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', "max:{$loan->remaining_amount}"],
            'wallet_id' => ['required', 'integer', 'exists:wallets,id'],
            'repayment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'amount.required' => 'Nominal pembayaran cicilan wajib diisi.',
            'amount.max' => 'Nominal pembayaran tidak boleh melebihi sisa pokok pinjaman.',
            'wallet_id.required' => 'Pilih dompet penerima/pembayar.',
        ]);

        $wallet = $user->wallets()->findOrFail($validated['wallet_id']);
        $repayAmount = (float) $validated['amount'];

        DB::transaction(function () use ($user, $loan, $wallet, $repayAmount, $validated) {
            $loan->repayments()->create([
                'wallet_id' => $wallet->id,
                'amount' => $repayAmount,
                'repayment_date' => $validated['repayment_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $newRemaining = max(0, (float) $loan->remaining_amount - $repayAmount);
            $newStatus = $newRemaining <= 0 ? 'paid' : 'partial';

            $loan->update([
                'remaining_amount' => $newRemaining,
                'status' => $newStatus,
            ]);

            // Jika Piutang: pelunasan menambah saldo dompet penampung
            // Jika Hutang: pelunasan mengurangi saldo dompet pembayar
            if ($loan->type === 'loan_given') {
                $wallet->increment('balance', $repayAmount);
                $txType = 'income';
                $desc = "Penerimaan cicilan piutang dari {$loan->person_name}";
            } else {
                $wallet->decrement('balance', $repayAmount);
                $txType = 'expense';
                $desc = "Pembayaran cicilan hutang ke {$loan->person_name}";
            }

            $user->transactions()->create([
                'wallet_id' => $wallet->id,
                'type' => $txType,
                'amount' => $repayAmount,
                'fee' => 0,
                'source' => $loan->person_name,
                'description' => $desc,
                'transaction_date' => $validated['repayment_date'],
                'related_loan_id' => $loan->id,
            ]);
        });

        return redirect()->route('loans.index')
            ->with('success', "Pelunasan sebesar Rp " . number_format($repayAmount, 0, ',', '.') . " berhasil dicatat.");
    }
}
