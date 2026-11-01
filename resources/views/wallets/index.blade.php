@extends('layouts.app')

@section('title', 'Dompet & Rekening — Finora')

@section('content')
<div 
    x-data="{ 
        activeTab: 'transfer', 
        adjustModalOpen: false,
        selectedWallet: { id: null, name: '', balance: 0 },
        openAdjustModal(wallet) {
            this.selectedWallet = wallet;
            this.adjustModalOpen = true;
        }
    }" 
    class="space-y-8"
>
    
    <!-- HEADER HALAMAN DOMPET -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-base-content">
                Manajemen Dompet & Rekening
            </h1>
            <p class="text-xs sm:text-sm text-base-content/70 mt-1">
                Kelola uang tunai, rekening bank, dompet digital, top up, serta penyesuaian saldo riil.
            </p>
        </div>
    </div>

    <!-- WIDGET RINGKASAN DOMPET (DaisyUI Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- 1. Total Saldo Dompet Aktif -->
        <div x-data="{ show: true }" class="card bg-base-100 card-border shadow-sm">
            <div class="card-body p-5 justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-base-content/70">
                        Total Saldo Aktif
                    </span>
                    <button 
                        type="button" 
                        @click="show = !show" 
                        class="btn btn-ghost btn-circle btn-xs text-base-content/60"
                        :aria-label="show ? 'Sembunyikan Saldo' : 'Tampilkan Saldo'"
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
                <div class="text-xl sm:text-2xl font-bold tabular-nums text-base-content mt-1">
                    <span x-show="show">Rp {{ number_format($totalActiveBalance, 0, ',', '.') }}</span>
                    <span x-show="!show" class="opacity-40">Rp ********</span>
                </div>
                <p class="text-[11px] text-base-content/60 mt-2">
                    Hanya dari dompet berstatus aktif
                </p>
            </div>
        </div>

        <!-- 2. Jumlah Dompet Aktif -->
        <div class="card bg-base-100 card-border shadow-sm">
            <div class="card-body p-5 justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-success">
                    Dompet Aktif
                </span>
                <div class="text-2xl font-bold text-base-content mt-2">
                    {{ $activeCount }} Dompet
                </div>
                <p class="text-[11px] text-base-content/60 mt-2">
                    Siap digunakan untuk transaksi mutasi
                </p>
            </div>
        </div>

        <!-- 3. Dompet Non-Aktif / Diarsipkan -->
        <div class="card bg-base-100 card-border shadow-sm">
            <div class="card-body p-5 justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-base-content/70">
                    Dompet Non-Aktif
                </span>
                <div class="text-2xl font-bold text-base-content mt-2">
                    {{ $inactiveCount }} Dompet
                </div>
                <p class="text-[11px] text-base-content/60 mt-2">
                    Disembunyikan dari pilihan mutasi harian
                </p>
            </div>
        </div>
    </div>

    <!-- PANEL AKSI: TRANSFER SALDO ATAU TAMBAH DOMPET (DaisyUI Card & Tabs) -->
    <div class="card bg-base-100 card-border shadow-sm">
        <div class="card-body p-6 sm:p-8 space-y-6">
            <!-- Tab Navigation Buttons (DaisyUI Join / Tabs) -->
            <div class="join border border-base-300 rounded-box p-1 bg-base-200/50">
                <button 
                    type="button" 
                    @click="activeTab = 'transfer'" 
                    :class="activeTab === 'transfer' ? 'btn-primary font-bold' : 'btn-ghost text-base-content/70'"
                    class="btn btn-sm join-item"
                >
                    Transfer / Top Up Saldo
                </button>
                <button 
                    type="button" 
                    @click="activeTab = 'create'" 
                    :class="activeTab === 'create' ? 'btn-primary font-bold' : 'btn-ghost text-base-content/70'"
                    class="btn btn-sm join-item"
                >
                    Tambah Dompet Baru
                </button>
            </div>

            <!-- TAB 1: FORMULIR TRANSFER / TOP UP ANTAR DOMPET -->
            <div x-show="activeTab === 'transfer'" class="space-y-5">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-base-content">
                        Transfer Antar Dompet & Top Up Saldo
                    </h2>
                    <p class="text-xs text-base-content/70 mt-0.5">
                        Pindahkan dana antar rekening, top up dompet digital, atau tarik tunai lengkap dengan pencatatan biaya admin.
                    </p>
                </div>

                <form method="POST" action="{{ route('wallets.transfer') }}" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <!-- 1. Dompet Sumber -->
                        <div class="form-control">
                            <label for="from_wallet_id" class="label">
                                <span class="label-text font-bold text-xs uppercase tracking-wider">
                                    Dari Dompet (Sumber) <span class="text-error">*</span>
                                </span>
                            </label>
                            <select 
                                id="from_wallet_id" 
                                name="from_wallet_id" 
                                required
                                class="select select-bordered w-full"
                            >
                                <option value="">Pilih dompet asal...</option>
                                @foreach ($wallets->where('is_active', true) as $w)
                                    <option value="{{ $w->id }}" {{ old('from_wallet_id') == $w->id ? 'selected' : '' }}>
                                        {{ $w->name }} (Rp {{ number_format($w->balance, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2. Dompet Tujuan -->
                        <div class="form-control">
                            <label for="to_wallet_id" class="label">
                                <span class="label-text font-bold text-xs uppercase tracking-wider">
                                    Ke Dompet (Tujuan) <span class="text-error">*</span>
                                </span>
                            </label>
                            <select 
                                id="to_wallet_id" 
                                name="to_wallet_id" 
                                required
                                class="select select-bordered w-full"
                            >
                                <option value="">Pilih dompet tujuan...</option>
                                @foreach ($wallets->where('is_active', true) as $w)
                                    <option value="{{ $w->id }}" {{ old('to_wallet_id') == $w->id ? 'selected' : '' }}>
                                        {{ $w->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 3. Nominal Transfer -->
                        <div class="form-control">
                            <label for="transfer_amount" class="label">
                                <span class="label-text font-bold text-xs uppercase tracking-wider">
                                    Nominal Transfer (Rp) <span class="text-error">*</span>
                                </span>
                            </label>
                            <input 
                                type="number" 
                                id="transfer_amount" 
                                name="amount" 
                                value="{{ old('amount') }}" 
                                required 
                                min="1" 
                                step="1" 
                                placeholder="Contoh: 100000"
                                class="input input-bordered w-full tabular-nums"
                            >
                        </div>

                        <!-- 4. Biaya Admin -->
                        <div class="form-control">
                            <label for="transfer_fee" class="label">
                                <span class="label-text font-bold text-xs uppercase tracking-wider">
                                    Biaya Admin (Rp)
                                </span>
                            </label>
                            <input 
                                type="number" 
                                id="transfer_fee" 
                                name="fee" 
                                value="{{ old('fee', 0) }}" 
                                min="0" 
                                step="1" 
                                placeholder="Contoh: 1000"
                                class="input input-bordered w-full tabular-nums"
                            >
                        </div>
                    </div>

                    <!-- Catatan / Keterangan Transfer -->
                    <div class="form-control">
                        <label for="transfer_notes" class="label">
                            <span class="label-text font-bold text-xs uppercase tracking-wider">
                                Keterangan / Catatan Tambahan (Opsional)
                            </span>
                        </label>
                        <input 
                            type="text" 
                            id="transfer_notes" 
                            name="notes" 
                            value="{{ old('notes') }}" 
                            placeholder="Contoh: Top up GoPay via BCA Mobile, Tarik tunai ATM Mandiri"
                            class="input input-bordered w-full"
                        >
                    </div>

                    <!-- Tombol Submit Transfer (DaisyUI btn-primary) -->
                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="btn btn-primary">
                            Kirim Transfer & Sesuaikan Saldo
                        </button>
                    </div>
                </form>
            </div>

            <!-- TAB 2: FORMULIR TAMBAH DOMPET BARU -->
            <div x-show="activeTab === 'create'" class="space-y-5">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-base-content">
                        Tambah Dompet atau Rekening Baru
                    </h2>
                    <p class="text-xs text-base-content/70 mt-0.5">
                        Daftarkan rekening bank, e-money, atau tabungan investasi beserta saldo awalnya.
                    </p>
                </div>

                <form method="POST" action="{{ route('wallets.store') }}" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <!-- 1. Nama Dompet -->
                        <div class="form-control">
                            <label for="name" class="label">
                                <span class="label-text font-bold text-xs uppercase tracking-wider">
                                    Nama Dompet / Rekening <span class="text-error">*</span>
                                </span>
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ old('name') }}" 
                                required 
                                placeholder="Contoh: BCA Tabungan, Mandiri, GoPay, OVO"
                                class="input input-bordered w-full"
                            >
                        </div>

                        <!-- 2. Jenis Dompet (Bank, Dompet Digital, Tabungan / Investasi) -->
                        <div class="form-control">
                            <label for="type" class="label">
                                <span class="label-text font-bold text-xs uppercase tracking-wider">
                                    Jenis Akun <span class="text-error">*</span>
                                </span>
                            </label>
                            <select 
                                id="type" 
                                name="type" 
                                required
                                class="select select-bordered w-full"
                            >
                                <option value="bank" {{ old('type', 'bank') === 'bank' ? 'selected' : '' }}>Rekening Bank</option>
                                <option value="ewallet" {{ old('type') === 'ewallet' ? 'selected' : '' }}>Dompet Digital (E-Money)</option>
                                <option value="investment" {{ old('type') === 'investment' ? 'selected' : '' }}>Tabungan / Investasi</option>
                            </select>
                        </div>

                        <!-- 3. Saldo Awal -->
                        <div class="form-control">
                            <label for="initial_balance" class="label">
                                <span class="label-text font-bold text-xs uppercase tracking-wider">
                                    Saldo Awal Saat Ini (Rp)
                                </span>
                            </label>
                            <input 
                                type="number" 
                                id="initial_balance" 
                                name="initial_balance" 
                                value="{{ old('initial_balance', 0) }}" 
                                min="0" 
                                step="1" 
                                placeholder="Contoh: 1500000"
                                class="input input-bordered w-full tabular-nums"
                            >
                        </div>
                    </div>

                    <!-- Tombol Simpan Dompet (DaisyUI btn-primary) -->
                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="btn btn-primary">
                            Tambah Dompet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DAFTAR LENGKAP DOMPET (URUTAN: UANG TUNAI, BANK, DOMPET DIGITAL) -->
    <section class="space-y-4">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-base-content">
                Daftar Lengkap Dompet Finora
            </h2>
            <p class="text-xs text-base-content/70 mt-0.5">
                Urutan penataan: Uang Tunai, Rekening Bank, lalu Dompet Digital.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($wallets as $wallet)
                <div 
                    x-data="{ show: true }" 
                    class="card bg-base-100 card-border shadow-sm flex flex-col justify-between {{ !$wallet->is_active ? 'opacity-70 bg-base-200/50' : '' }}"
                >
                    <div class="card-body p-6 justify-between">
                        <div>
                            <!-- Header Kartu & Status (DaisyUI Badges) -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="badge badge-neutral badge-soft badge-sm font-bold uppercase tracking-wider">
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

                                <span class="badge badge-sm {{ $wallet->is_active ? 'badge-success badge-soft' : 'badge-neutral badge-soft' }}">
                                    {{ $wallet->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-base-content">
                                {{ $wallet->name }}
                            </h3>

                            <!-- Saldo Individual dengan Tombol Sensor -->
                            <div class="mt-4 pt-3 border-t border-base-200 flex items-center justify-between">
                                <div>
                                    <span class="text-[11px] text-base-content/60 block mb-0.5">Saldo Berjalan</span>
                                    <div class="text-xl font-bold tabular-nums text-base-content">
                                        <span x-show="show">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</span>
                                        <span x-show="!show" class="opacity-40">Rp ********</span>
                                    </div>
                                </div>
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

                            <div class="mt-2 text-xs text-base-content/60">
                                {{ $wallet->transactions_count }} mutasi tercatat
                            </div>
                        </div>

                        <!-- Tombol Aksi: Penyesuaian Saldo & Toggle Status (DaisyUI Buttons) -->
                        <div class="mt-6 pt-4 border-t border-base-200 flex items-center justify-between gap-2 flex-wrap">
                            <!-- Tombol Sesuaikan Saldo -->
                            <button 
                                type="button" 
                                @click="openAdjustModal({ id: {{ $wallet->id }}, name: '{{ addslashes($wallet->name) }}', balance: {{ (float) $wallet->balance }} })"
                                class="btn btn-outline btn-primary btn-sm"
                            >
                                Sesuaikan Saldo
                            </button>

                            <!-- Tombol Toggle Aktif / Nonaktif -->
                            <form method="POST" action="{{ route('wallets.toggle', $wallet) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline btn-sm">
                                    {{ $wallet->is_active ? 'Nonaktifkan' : 'Aktifkan Kembali' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card bg-base-100 card-border p-8 col-span-3 text-center">
                    <p class="text-xs text-base-content/70">
                        Belum ada dompet didaftarkan. Gunakan formulir di atas untuk membuat dompet pertama Anda.
                    </p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- RIWAYAT AKTIVITAS DOMPET & REKENING (TRANSFER, TOP UP, PENYESUAIAN, & PENAMBAHAN DOMPET) (DaisyUI Card) -->
    <section class="space-y-4">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-base-content">
                Riwayat Aktivitas Dompet & Rekening
            </h2>
            <p class="text-xs text-base-content/70 mt-0.5">
                Log historis top up, transfer antar dompet, penyesuaian saldo riil, serta penambahan saldo awal dompet.
            </p>
        </div>

        <div class="card bg-base-100 card-border shadow-sm divide-y divide-base-200 overflow-hidden w-full">
            @forelse ($walletActivities as $activity)
                <div class="p-4 sm:p-5 flex items-center justify-between gap-4 hover:bg-base-200/50 transition-colors">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <!-- Ikon Aktivitas Sesuai Tipe -->
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 
                            @if ($activity->type === 'transfer_in')
                                bg-success/15 text-success
                            @elseif ($activity->type === 'transfer_out')
                                bg-warning/15 text-warning
                            @elseif ($activity->type === 'balance_adjustment')
                                bg-info/15 text-info
                            @elseif ($activity->type === 'initial_balance')
                                bg-primary/15 text-primary
                            @else
                                bg-base-300 text-base-content/70
                            @endif"
                        >
                            @if ($activity->type === 'transfer_in')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 4.5-15 15m0 0h11.25m-11.25 0V8.25" />
                                </svg>
                            @elseif ($activity->type === 'transfer_out')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                                </svg>
                            @elseif ($activity->type === 'balance_adjustment')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                                </svg>
                            @elseif ($activity->type === 'initial_balance')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            @endif
                        </div>

                        <!-- Deskripsi & Keterangan Dompet -->
                        <div class="min-w-0">
                            <span class="block text-sm font-bold text-base-content truncate">
                                {{ $activity->description ?: ($activity->source ?: 'Aktivitas Dompet') }}
                            </span>
                            <div class="flex flex-wrap items-center gap-2 text-xs text-base-content/70 mt-0.5">
                                <span class="font-medium">{{ \Carbon\Carbon::parse($activity->transaction_date)->format('d M Y') }}</span>
                                <span>/</span>
                                <span class="badge badge-sm badge-neutral badge-soft">
                                    {{ $activity->wallet->name ?? 'Dompet' }}
                                </span>
                                <span>/</span>
                                <span class="badge badge-sm badge-soft
                                    @if ($activity->type === 'transfer_in')
                                        badge-success
                                    @elseif ($activity->type === 'transfer_out')
                                        badge-warning
                                    @elseif ($activity->type === 'balance_adjustment')
                                        badge-info
                                    @elseif ($activity->type === 'initial_balance')
                                        badge-primary
                                    @endif"
                                >
                                    @if ($activity->type === 'transfer_in')
                                        Transfer Masuk / Top Up
                                    @elseif ($activity->type === 'transfer_out')
                                        Transfer Keluar
                                    @elseif ($activity->type === 'balance_adjustment')
                                        Penyesuaian Saldo
                                    @elseif ($activity->type === 'initial_balance')
                                        Saldo Awal Dompet
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Nominal Aktivitas -->
                    <div class="text-right shrink-0">
                        <span class="block text-sm sm:text-base font-bold tabular-nums 
                            @if ($activity->type === 'transfer_in' || $activity->type === 'initial_balance')
                                text-success
                            @elseif ($activity->type === 'transfer_out')
                                text-base-content
                            @else
                                text-info
                            @endif"
                        >
                            @if ($activity->type === 'transfer_in' || $activity->type === 'initial_balance')
                                + Rp {{ number_format($activity->amount, 0, ',', '.') }}
                            @elseif ($activity->type === 'transfer_out')
                                - Rp {{ number_format($activity->amount, 0, ',', '.') }}
                            @else
                                Rp {{ number_format($activity->amount, 0, ',', '.') }}
                            @endif
                        </span>
                        @if ($activity->fee > 0)
                            <span class="text-[11px] text-base-content/60 block mt-0.5">
                                Admin: Rp {{ number_format($activity->fee, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-xs text-base-content/70">
                        Belum ada riwayat aktivitas top up, transfer, atau penyesuaian saldo dompet.
                    </p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- MODAL PENYESUAIAN SALDO (REKONSILIASI SALDO RIIL) (DaisyUI Modal) -->
    <div 
        class="modal" 
        :class="{ 'modal-open': adjustModalOpen }" 
        role="dialog"
        x-show="adjustModalOpen" 
        x-cloak
        @keydown.escape.window="adjustModalOpen = false"
    >
        <div class="modal-box max-w-md" @click.away="adjustModalOpen = false">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-lg font-bold text-base-content">
                        Penyesuaian Saldo Riil
                    </h3>
                    <p class="text-xs text-primary font-semibold" x-text="selectedWallet.name"></p>
                </div>
                <button 
                    type="button" 
                    @click="adjustModalOpen = false"
                    class="btn btn-ghost btn-sm btn-circle"
                >
                    ✕
                </button>
            </div>

            <p class="text-xs text-base-content/70 leading-relaxed mb-4">
                Gunakan fitur ini jika saldo di aplikasi tidak cocok dengan saldo fisik di dunia nyata. Sistem akan mencatat selisihnya secara transparan sebagai mutasi penyesuaian saldo.
            </p>

            <form 
                method="POST" 
                :action="'{{ url('/dompet') }}/' + selectedWallet.id + '/adjust'" 
                class="space-y-4"
            >
                @csrf

                <!-- Tampilan Saldo Saat Ini -->
                <div class="p-3.5 rounded-box bg-base-200/60 border border-base-300 flex items-center justify-between">
                    <span class="text-xs text-base-content/70">Saldo Tercatat Saat Ini:</span>
                    <span class="text-sm font-bold tabular-nums text-base-content" x-text="'Rp ' + Number(selectedWallet.balance).toLocaleString('id-ID')"></span>
                </div>

                <!-- Input Saldo Riil Baru -->
                <div class="form-control">
                    <label for="new_balance" class="label">
                        <span class="label-text font-bold text-xs uppercase tracking-wider">
                            Saldo Riil Baru (Rp) <span class="text-error">*</span>
                        </span>
                    </label>
                    <input 
                        type="number" 
                        id="new_balance" 
                        name="new_balance" 
                        required 
                        min="0" 
                        step="1"
                        placeholder="Masukkan total saldo riil baru"
                        class="input input-bordered w-full tabular-nums"
                    >
                </div>

                <!-- Alasan / Catatan Penyesuaian -->
                <div class="form-control">
                    <label for="adjust_reason" class="label">
                        <span class="label-text font-bold text-xs uppercase tracking-wider">
                            Alasan / Keterangan Penyesuaian (Opsional)
                        </span>
                    </label>
                    <input 
                        type="text" 
                        id="adjust_reason" 
                        name="reason" 
                        placeholder="Contoh: Selisih uang receh belanja, Pembulatan kas fisik"
                        class="input input-bordered w-full"
                    >
                </div>

                <!-- Tombol Aksi Modal (DaisyUI modal-action) -->
                <div class="modal-action">
                    <button 
                        type="button" 
                        @click="adjustModalOpen = false"
                        class="btn btn-ghost btn-sm"
                    >
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        Simpan Saldo Baru
                    </button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop" @click="adjustModalOpen = false"></div>
    </div>

</div>
@endsection
