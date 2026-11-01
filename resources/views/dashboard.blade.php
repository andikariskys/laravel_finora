@extends('layouts.app')

@section('title', 'Dashboard Ringkasan — Finora')

@section('content')
<div class="space-y-8">
    
    <!-- HEADER RINGKASAN & TANGGAL RESET -->
    <div x-data="{ resetModalOpen: false }" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-base-content">
                Ringkasan Keuangan Periode Ini
            </h1>
            <div class="flex items-center gap-2 mt-1 text-xs sm:text-sm text-base-content/70 flex-wrap">
                <span>Periode: <strong>{{ $periodLabel }}</strong></span>
                <span>/</span>
                <span>Tanggal Reset: <strong>Tanggal {{ $resetDay }} setiap bulan</strong></span>
                <button 
                    type="button" 
                    @click="resetModalOpen = true"
                    class="btn btn-outline btn-xs"
                >
                    Ubah
                </button>
            </div>
        </div>

        <!-- MODAL UBAH TANGGAL RESET (DaisyUI Modal) -->
        <div 
            class="modal" 
            :class="{ 'modal-open': resetModalOpen }" 
            role="dialog"
            x-show="resetModalOpen" 
            x-cloak
            @keydown.escape.window="resetModalOpen = false"
        >
            <div class="modal-box max-w-md" @click.away="resetModalOpen = false">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-bold text-base-content">
                        Atur Tanggal Reset Bulanan
                    </h3>
                    <button 
                        type="button" 
                        @click="resetModalOpen = false"
                        class="btn btn-ghost btn-sm btn-circle"
                    >
                        ✕
                    </button>
                </div>

                <p class="text-xs text-base-content/70 leading-relaxed mb-4">
                    Tanggal reset menentukan batas awal dan akhir (cut-off) siklus perputaran keuangan bulanan. Cocok disesuaikan dengan tanggal penerimaan gaji bulanan Anda.
                </p>

                <form method="POST" action="{{ route('dashboard.reset-day') }}" class="space-y-4">
                    @csrf
                    <div class="form-control">
                        <label for="monthly_reset_day" class="label">
                            <span class="label-text font-bold text-xs uppercase tracking-wider">Pilih Tanggal Cut-off (1 - 28)</span>
                        </label>
                        <select 
                            id="monthly_reset_day" 
                            name="monthly_reset_day" 
                            class="select select-bordered w-full"
                        >
                            @for ($day = 1; $day <= 28; $day++)
                                <option value="{{ $day }}" {{ (int) $resetDay === $day ? 'selected' : '' }}>
                                    Tanggal {{ $day }} setiap bulan {{ $day === 1 ? '(Awal Bulan Kalender)' : ($day === 25 ? '(Umum Gajian)' : '') }}
                                </option>
                            @endfor
                        </select>
                        <p class="text-[11px] text-base-content/60 mt-1.5">
                            Maksimal tanggal 28 agar tanggal reset valid di seluruh bulan dalam setahun.
                        </p>
                    </div>

                    <div class="modal-action">
                        <button 
                            type="button" 
                            @click="resetModalOpen = false"
                            class="btn btn-ghost btn-sm"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="btn btn-primary btn-sm"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-backdrop" @click="resetModalOpen = false"></div>
        </div>
    </div>

    <!-- 1. CARD BAGIAN ATAS: 3 KARTU HIGHLIGHT (SALDO AWAL, PEMASUKAN, PENGELUARAN) (DaisyUI Cards) -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- A. Saldo Awal Periode (Default: Terlihat) -->
        <div 
            x-data="{ show: true }" 
            class="card bg-base-100 card-border shadow-sm hover:border-base-content/20 transition-colors"
        >
            <div class="card-body p-6 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-base-content/70">
                        Saldo Awal Periode
                    </span>
                    <button 
                        type="button" 
                        @click="show = !show" 
                        class="btn btn-ghost btn-circle btn-xs text-base-content/60"
                        :aria-label="show ? 'Sembunyikan Saldo Awal' : 'Tampilkan Saldo Awal'"
                    >
                        <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg x-show="!show" class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold tabular-nums text-base-content mt-2">
                    <span x-show="show">Rp {{ number_format($openingBalance, 0, ',', '.') }}</span>
                    <span x-show="!show" class="opacity-40">Rp ********</span>
                </div>
                <p class="text-[11px] text-base-content/60 mt-2">
                    Bawaan rollover akhir bulan lalu
                </p>
            </div>
        </div>

        <!-- B. Pemasukan Bulan Ini (Default: Terlihat) -->
        <div 
            x-data="{ show: true }" 
            class="card bg-base-100 card-border shadow-sm border-success/30 hover:border-success transition-colors"
        >
            <div class="card-body p-6 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-success">
                        Pemasukan Bulan Ini
                    </span>
                    <button 
                        type="button" 
                        @click="show = !show" 
                        class="btn btn-ghost btn-circle btn-xs text-base-content/60"
                        :aria-label="show ? 'Sembunyikan Pemasukan' : 'Tampilkan Pemasukan'"
                    >
                        <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg x-show="!show" class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold tabular-nums text-success mt-2">
                    <span x-show="show">+ Rp {{ number_format($periodIncome, 0, ',', '.') }}</span>
                    <span x-show="!show" class="opacity-40">+ Rp ********</span>
                </div>
                <p class="text-[11px] text-base-content/60 mt-2">
                    Total dana masuk periode berjalan
                </p>
            </div>
        </div>

        <!-- C. Pengeluaran Bulan Ini (Default: Terlihat) -->
        <div 
            x-data="{ show: true }" 
            class="card bg-base-100 card-border shadow-sm border-error/30 hover:border-error transition-colors"
        >
            <div class="card-body p-6 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-error">
                        Pengeluaran Bulan Ini
                    </span>
                    <button 
                        type="button" 
                        @click="show = !show" 
                        class="btn btn-ghost btn-circle btn-xs text-base-content/60"
                        :aria-label="show ? 'Sembunyikan Pengeluaran' : 'Tampilkan Pengeluaran'"
                    >
                        <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg x-show="!show" class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold tabular-nums text-error mt-2">
                    <span x-show="show">- Rp {{ number_format($periodExpense, 0, ',', '.') }}</span>
                    <span x-show="!show" class="opacity-40">- Rp ********</span>
                </div>
                <p class="text-[11px] text-base-content/60 mt-2">
                    Termasuk mutasi biaya admin transfer
                </p>
            </div>
        </div>
    </section>

    <!-- 2. SUB-MENU DAFTAR DOMPET & REKENING (DIGABUNG TOTAL SALDO KESELURUHAN) -->
    <section class="space-y-4">
        <!-- Banner Header Sub-Menu: Daftar Dompet & Total Saldo Keseluruhan (DaisyUI Card) -->
        <div class="card bg-neutral text-neutral-content card-border shadow-sm p-5 sm:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-neutral-content">
                    Daftar Dompet & Rekening
                </h2>
                <p class="text-xs text-neutral-content/70 mt-0.5">
                    Urutan: Uang Tunai, Rekening Bank, lalu Dompet Digital ({{ $activeWallets->count() }} dompet aktif).
                </p>
            </div>

            <!-- Total Saldo Keseluruhan Terintegrasi (Secara Default Hidden) -->
            <div 
                x-data="{ show: false }" 
                class="flex items-center gap-3 bg-neutral-content/10 px-4 py-2.5 rounded-box border border-neutral-content/20 self-start md:self-auto"
            >
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-content/70 block">
                        Total Saldo Keseluruhan
                    </span>
                    <div class="text-base sm:text-lg font-extrabold tabular-nums text-neutral-content">
                        <span x-show="show">Rp {{ number_format($totalBalance, 0, ',', '.') }}</span>
                        <span x-show="!show" class="text-neutral-content/50 tracking-wider">Rp ********</span>
                    </div>
                </div>

                <!-- Tombol Privasi Total Saldo -->
                <button 
                    type="button" 
                    @click="show = !show" 
                    class="btn btn-ghost btn-circle btn-xs text-neutral-content/80 hover:text-neutral-content"
                    :aria-label="show ? 'Sembunyikan Total Saldo Keseluruhan' : 'Tampilkan Total Saldo Keseluruhan'"
                >
                    <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <svg x-show="!show" class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Grid Dompet & Rekening (DaisyUI Cards, Secara Default Saldo Disembunyikan / show: false) -->
        @if ($wallets->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($wallets as $wallet)
                    <div 
                        x-data="{ show: false }" 
                        class="card bg-base-100 card-border shadow-sm hover:border-base-content/20 transition-colors"
                    >
                        <div class="card-body p-5 justify-between">
                            <div>
                                <!-- Header Dompet & Badge Tipe -->
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="badge badge-neutral badge-soft badge-xs font-bold uppercase tracking-wider">
                                        @if ($wallet->type === 'cash')
                                            Uang Tunai
                                        @elseif ($wallet->type === 'bank')
                                            Rekening Bank
                                        @elseif ($wallet->type === 'ewallet')
                                            Dompet Digital
                                        @else
                                            Investasi
                                        @endif
                                    </span>

                                    <!-- Tombol Privasi Individual Dompet (Default Hidden) -->
                                    <button 
                                        type="button" 
                                        @click="show = !show" 
                                        class="btn btn-ghost btn-circle btn-xs text-base-content/60"
                                        :aria-label="show ? 'Sembunyikan saldo {{ $wallet->name }}' : 'Tampilkan saldo {{ $wallet->name }}'"
                                    >
                                        <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <svg x-show="!show" class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </button>
                                </div>

                                <h3 class="text-base font-bold text-base-content">
                                    {{ $wallet->name }}
                                </h3>
                            </div>

                            <div class="mt-4 pt-3 border-t border-base-200">
                                <span class="text-[11px] text-base-content/60 block mb-0.5">Saldo Terkini</span>
                                <div class="text-lg font-bold tabular-nums text-base-content">
                                    <span x-show="show">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</span>
                                    <span x-show="!show" class="opacity-40 tracking-wider">Rp ********</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card card-border border-dashed bg-base-100 p-6 text-center">
                <p class="text-xs text-base-content/70">
                    Belum ada dompet didaftarkan. Anda dapat mengelolanya di menu Dompet & Rekening.
                </p>
            </div>
        @endif
    </section>

    <!-- 3. MUTASI TERAKHIR (DESKTOP MODE: FULL KANAN-KIRI / FULL WIDTH) (DaisyUI Card) -->
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-base-content">
                    Mutasi Terakhir
                </h2>
                <p class="text-xs text-base-content/70 mt-0.5">
                    Aktivitas mutasi terkini akun Anda.
                </p>
            </div>
            <a 
                href="{{ route('transactions.index') }}" 
                class="btn btn-ghost btn-sm text-primary"
            >
                Lihat Semua Mutasi &rarr;
            </a>
        </div>

        <div class="card bg-base-100 card-border shadow-sm divide-y divide-base-200 overflow-hidden w-full">
            @forelse ($recentTransactions as $tx)
                <div class="p-4 sm:p-5 flex items-center justify-between gap-4 hover:bg-base-200/50 transition-colors">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <!-- Ikon Mutasi -->
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 
                            @if ($tx->type === 'income')
                                bg-success/15 text-success
                            @elseif ($tx->type === 'expense' || $tx->type === 'recurring_expense')
                                bg-error/15 text-error
                            @else
                                bg-info/15 text-info
                            @endif"
                        >
                            @if ($tx->type === 'income')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 4.5-15 15m0 0h11.25m-11.25 0V8.25" />
                                </svg>
                            @elseif ($tx->type === 'expense' || $tx->type === 'recurring_expense')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                                </svg>
                            @endif
                        </div>

                        <!-- Detail Keterangan -->
                        <div class="min-w-0">
                            <span class="block text-sm font-bold text-base-content truncate">
                                {{ $tx->description ?: ($tx->source ?: 'Transaksi Finora') }}
                            </span>
                            <div class="flex flex-wrap items-center gap-2 text-xs text-base-content/70 mt-0.5">
                                <span class="font-medium">{{ \Carbon\Carbon::parse($tx->transaction_date)->format('d M Y') }}</span>
                                <span>/</span>
                                <span class="badge badge-sm badge-neutral badge-soft">
                                    {{ $tx->wallet->name ?? 'Dompet' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Nominal Transaksi (Tanpa tombol hide/show, tampil langsung) -->
                    <div class="text-right shrink-0">
                        <span class="block text-sm sm:text-base font-bold tabular-nums {{ $tx->type === 'income' ? 'text-success' : 'text-base-content' }}">
                            {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </span>
                        @if ($tx->fee > 0)
                            <span class="text-[11px] text-base-content/60 block mt-0.5">
                                Biaya admin: Rp {{ number_format($tx->fee, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-xs text-base-content/70">
                        Belum ada mutasi transaksi bulan ini. Buka halaman Mutasi untuk mencatat.
                    </p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- 4. LANGGANAN & PINJAMAN: 2 KOLOM PADA MODE DESKTOP -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        
        <!-- KOLOM 1: LANGGANAN & BIAYA RUTIN (DaisyUI Card) -->
        <section class="card bg-base-100 card-border shadow-sm p-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-base-content">
                        Langganan & Biaya Rutin
                    </h3>
                    <p class="text-xs text-base-content/70">
                        Daftar tagihan otomatis bulanan.
                    </p>
                </div>
                <a 
                    href="{{ route('recurring.index') }}" 
                    class="btn btn-ghost btn-xs text-primary"
                >
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($recurringRules as $rule)
                    <div class="p-3.5 rounded-box border border-base-200 bg-base-200/50 flex items-center justify-between">
                        <div>
                            <span class="block text-xs font-bold text-base-content">
                                {{ $rule->name }}
                            </span>
                            <span class="text-[11px] text-base-content/70">
                                @if ($rule->frequency === 'daily')
                                    Harian
                                @elseif ($rule->frequency === 'weekly')
                                    Mingguan
                                @elseif ($rule->frequency === 'custom_date')
                                    Khusus ({{ $rule->due_date ? \Carbon\Carbon::parse($rule->due_date)->format('d M') : '' }})
                                @else
                                    Jatuh tempo tgl {{ $rule->execution_day }}
                                @endif
                                / {{ $rule->wallet->name ?? 'Dompet' }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold tabular-nums text-base-content block">
                                Rp {{ number_format($rule->amount, 0, ',', '.') }}
                            </span>
                            <span class="badge badge-success badge-soft badge-xs">
                                Terjadwal
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-base-content/60 text-center py-4">
                        Belum ada jadwal langganan atau biaya rutin terdaftar.
                    </p>
                @endforelse
            </div>
        </section>

        <!-- KOLOM 2: PINJAMAN & HUTANG (DaisyUI Card) -->
        <section class="card bg-base-100 card-border shadow-sm p-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-base-content">
                        Pinjaman & Hutang
                    </h3>
                    <p class="text-xs text-base-content/70">
                        Pantau piutang rekan dan kewajiban hutang.
                    </p>
                </div>
                <a 
                    href="{{ route('loans.index') }}" 
                    class="btn btn-ghost btn-xs text-primary"
                >
                    Kelola &rarr;
                </a>
            </div>

            <!-- Ringkasan Nilai Piutang vs Hutang (DaisyUI stats-style cards) -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="p-3 rounded-box bg-success/10 border border-success/20">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-success block">
                        Piutang
                    </span>
                    <span class="text-sm font-bold tabular-nums text-success block mt-1">
                        Rp {{ number_format($totalReceivable, 0, ',', '.') }}
                    </span>
                </div>

                <div class="p-3 rounded-box bg-warning/10 border border-warning/20">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-warning block">
                        Hutang
                    </span>
                    <span class="text-sm font-bold tabular-nums text-warning block mt-1">
                        Rp {{ number_format($totalDebt, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Daftar Ringkas Pinjaman -->
            <div class="space-y-3">
                @forelse ($activeLoans as $loan)
                    <div class="p-3.5 rounded-box border border-base-200 bg-base-200/50 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-bold text-base-content">
                                    {{ $loan->person_name }}
                                </span>
                                <span class="badge badge-xs {{ $loan->type === 'loan_given' ? 'badge-success badge-soft' : 'badge-warning badge-soft' }}">
                                    {{ $loan->type === 'loan_given' ? 'Piutang' : 'Hutang' }}
                                </span>
                            </div>
                            <span class="text-[11px] text-base-content/70 block mt-0.5">
                                {{ $loan->notes ?: 'Catatan pinjaman' }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold tabular-nums text-base-content block">
                                Sisa: Rp {{ number_format($loan->remaining_amount, 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] text-base-content/50 block">
                                Total: Rp {{ number_format($loan->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-base-content/60 text-center py-4">
                        Tidak ada pinjaman atau hutang aktif saat ini.
                    </p>
                @endforelse
            </div>
        </section>

    </div>
</div>
@endsection
