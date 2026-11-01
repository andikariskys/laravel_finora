<?php

namespace App\Http\Controllers;

use App\Models\RecurringRule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RecurringController extends Controller
{
    /**
     * Tampilkan daftar seluruh tagihan dan biaya rutin pengguna.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $rules = $user->recurringRules()
            ->with('wallet')
            ->orderBy('is_active', 'desc')
            ->orderBy('execution_day')
            ->get();

        $activeRules = $rules->where('is_active', true);
        $inactiveRules = $rules->where('is_active', false);

        // Estimasi total beban pengeluaran rutin per bulan
        $monthlyTotalEstimate = $activeRules->sum(function ($rule) {
            return match ($rule->frequency) {
                'daily' => (float) $rule->amount * 30,
                'weekly' => (float) $rule->amount * 4,
                'monthly' => (float) $rule->amount,
                'custom_date' => (float) $rule->amount,
                default => (float) $rule->amount,
            };
        });

        $activeWallets = $user->wallets()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('recurring.index', compact(
            'rules',
            'activeRules',
            'inactiveRules',
            'monthlyTotalEstimate',
            'activeWallets'
        ));
    }

    /**
     * Tambahkan tagihan atau biaya rutin baru (Harian, Mingguan, Bulanan, Tanggal Khusus).
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'wallet_id' => ['required', 'integer', 'exists:wallets,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'frequency' => ['required', 'in:daily,weekly,monthly,custom_date'],
            'weekly_day' => ['nullable', 'required_if:frequency,weekly', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'monthly_day' => ['nullable', 'required_if:frequency,monthly', 'integer', 'min:1', 'max:28'],
            'due_date' => ['nullable', 'required_if:frequency,custom_date', 'date'],
        ], [
            'name.required' => 'Nama tagihan atau langganan wajib diisi.',
            'wallet_id.required' => 'Pilih dompet sumber pembayaran.',
            'amount.required' => 'Nominal tagihan wajib diisi.',
            'amount.min' => 'Nominal tagihan minimal Rp 1.',
            'frequency.required' => 'Pilih frekuensi pembayaran.',
            'weekly_day.required_if' => 'Pilih hari untuk pengeluaran mingguan.',
            'monthly_day.required_if' => 'Pilih tanggal eksekusi bulanan (1 s.d. 28).',
            'due_date.required_if' => 'Pilih tanggal khusus jatuh tempo tagihan.',
        ]);

        $wallet = $user->wallets()->where('is_active', true)->findOrFail($validated['wallet_id']);

        $frequency = $validated['frequency'];
        $frequencyValue = null;
        $executionDay = 1;
        $dueDate = null;

        $weeklyMap = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 7,
        ];

        if ($frequency === 'daily') {
            $frequencyValue = 'daily';
            $executionDay = 1;
        } elseif ($frequency === 'weekly') {
            $frequencyValue = $validated['weekly_day'];
            $executionDay = $weeklyMap[$validated['weekly_day']] ?? 1;
        } elseif ($frequency === 'monthly') {
            $frequencyValue = (string) $validated['monthly_day'];
            $executionDay = (int) $validated['monthly_day'];
        } elseif ($frequency === 'custom_date') {
            $frequencyValue = $validated['due_date'];
            $dueDate = $validated['due_date'];
            $executionDay = Carbon::parse($validated['due_date'])->day;
        }

        $user->recurringRules()->create([
            'wallet_id' => $wallet->id,
            'name' => $validated['name'],
            'amount' => $validated['amount'],
            'frequency' => $frequency,
            'frequency_value' => $frequencyValue,
            'due_date' => $dueDate,
            'execution_day' => $executionDay,
            'is_active' => true,
        ]);

        return redirect()->route('recurring.index')
            ->with('success', "Tagihan {$validated['name']} berhasil ditambahkan.");
    }

    /**
     * Beralih status aktif / non-aktif tagihan.
     */
    public function toggle(RecurringRule $recurringRule): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($recurringRule->user_id !== $user->id) {
            abort(403);
        }

        $recurringRule->is_active = ! $recurringRule->is_active;
        $recurringRule->save();

        $statusText = $recurringRule->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';

        return redirect()->route('recurring.index')
            ->with('success', "Tagihan {$recurringRule->name} berhasil {$statusText}.");
    }

    /**
     * Hapus aturan tagihan rutin.
     */
    public function destroy(RecurringRule $recurringRule): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($recurringRule->user_id !== $user->id) {
            abort(403);
        }

        $name = $recurringRule->name;
        $recurringRule->delete();

        return redirect()->route('recurring.index')
            ->with('success', "Tagihan {$name} berhasil dihapus.");
    }
}
