@extends('layouts.app')

@section('title', 'Mutasi Transaksi — Finora')

@section('content')
<div class="space-y-8">
    
    <!-- HEADER HALAMAN MUTASI -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-base-content">
                Mutasi Pemasukan & Pengeluaran
            </h1>
            <p class="text-xs sm:text-sm text-base-content/70 mt-1">
                Catat transaksi masuk dan keluar dompet secara presisi. Tanggal dapat dipilih bebas jika mencatat pengeluaran yang terlewat.
            </p>
        </div>
    </div>

    <!-- FORMULIR PENCATATAN MUTASI BARU (DaisyUI Card) -->
    <div 
        x-data="{ 
            txType: '{{ old('type', 'expense') }}',
            amount: '{{ old('amount') }}',
            fee: '{{ old('fee', 0) }}'
        }" 
        class="card bg-base-100 card-border shadow-sm"
    >
        <div class="card-body p-6 sm:p-8">
            <div class="flex items-center justify-between pb-5 border-b border-base-200 mb-6 flex-wrap gap-3">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-base-content">
                        Formulir Catat Transaksi Baru
                    </h2>
                    <p class="text-xs text-base-content/70 mt-0.5">
                        Pilih tipe mutasi di bawah ini untuk memulai pencatatan.
                    </p>
                </div>

                <!-- Segmented Switcher: Pengeluaran vs Pemasukan (DaisyUI Join Button) -->
                <div class="join border border-base-300 rounded-box p-1 bg-base-200/50">
                    <button 
                        type="button" 
                        @click="txType = 'expense'"
                        :class="txType === 'expense' ? 'btn-error text-white font-bold' : 'btn-ghost text-base-content/70'"
                        class="btn btn-sm join-item"
                    >
                        Pengeluaran
                    </button>
                    <button 
                        type="button" 
                        @click="txType = 'income'"
                        :class="txType === 'income' ? 'btn-success text-white font-bold' : 'btn-ghost text-base-content/70'"
                        class="btn btn-sm join-item"
                    >
                        Pemasukan
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('transactions.store') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="type" :value="txType">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <!-- 1. Nominal Transaksi -->
                    <div class="form-control">
                        <label for="amount" class="label">
                            <span class="label-text font-bold text-xs uppercase tracking-wider">
                                Nominal (Rp) <span class="text-error">*</span>
                            </span>
                        </label>
                        <input 
                            type="number" 
                            id="amount" 
                            name="amount" 
                            x-model="amount"
                            min="1" 
                            step="1" 
                            required 
                            placeholder="Contoh: 50000"
                            class="input input-bordered w-full tabular-nums"
                        >
                    </div>

                    <!-- 2. Pilihan Dompet -->
                    <div class="form-control">
                        <label for="wallet_id" class="label">
                            <span class="label-text font-bold text-xs uppercase tracking-wider">
                                <span x-text="txType === 'income' ? 'Dompet Tujuan' : 'Dompet Sumber'"></span> <span class="text-error">*</span>
                            </span>
                        </label>
                        <select 
                            id="wallet_id" 
                            name="wallet_id" 
                            required
                            class="select select-bordered w-full"
                        >
                            <option value="">Pilih Dompet</option>
                            @foreach ($wallets as $wallet)
                                <option value="{{ $wallet->id }}" {{ old('wallet_id') == $wallet->id ? 'selected' : '' }}>
                                    {{ $wallet->name }} (Rp {{ number_format($wallet->balance, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3. Tanggal Transaksi -->
                    <div class="form-control">
                        <label for="transaction_date" class="label">
                            <span class="label-text font-bold text-xs uppercase tracking-wider">
                                Tanggal Transaksi <span class="text-error">*</span>
                            </span>
                        </label>
                        <input 
                            type="date" 
                            id="transaction_date" 
                            name="transaction_date" 
                            value="{{ old('transaction_date', now()->toDateString()) }}" 
                            required 
                            class="input input-bordered w-full"
                        >
                        <p class="text-[11px] text-base-content/60 mt-1">Bebas memilih tanggal lampau jika terlambat mencatat.</p>
                    </div>

                    <!-- 4. Keterangan / Sumber Dana -->
                    <div class="form-control">
                        <label for="description" class="label">
                            <span class="label-text font-bold text-xs uppercase tracking-wider">
                                Keterangan / Keperluan
                            </span>
                        </label>
                        <input 
                            type="text" 
                            id="description" 
                            name="description" 
                            value="{{ old('description') }}" 
                            placeholder="Misal: Makan siang, gaji kantor, beli bensin"
                            class="input input-bordered w-full"
                        >
                    </div>

                    <!-- 5. Biaya Admin Opsional (Tampil jika Pengeluaran) -->
                    <div x-show="txType === 'expense'" class="form-control">
                        <label for="fee" class="label">
                            <span class="label-text font-bold text-xs uppercase tracking-wider">
                                Biaya Admin Tambahan (Rp)
                            </span>
                        </label>
                        <input 
                            type="number" 
                            id="fee" 
                            name="fee" 
                            x-model="fee"
                            min="0" 
                            placeholder="Misal: 1000 atau 2500"
                            class="input input-bordered w-full tabular-nums"
                        >
                    </div>
                </div>

                <!-- Tombol Aksi Simpan (DaisyUI btn-primary) -->
                <div class="pt-3 flex justify-end">
                    <button type="submit" class="btn btn-primary">
                        Simpan Mutasi Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DAFTAR MUTASI DENGAN FILTER TAB -->
    <section class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-base-content">
                    Daftar Mutasi Terdaftar
                </h2>
                <p class="text-xs text-base-content/70 mt-0.5">
                    Riwayat pemasukan dan pengeluaran tersusun secara kronologis.
                </p>
            </div>

            <!-- Filter Kategori Mutasi (DaisyUI Join Buttons) -->
            <div class="join border border-base-300 rounded-box p-1 bg-base-200/50">
                <a 
                    href="{{ route('transactions.index') }}" 
                    class="btn btn-sm join-item {{ !$typeFilter ? 'btn-active font-bold' : 'btn-ghost text-base-content/70' }}"
                >
                    Semua
                </a>
                <a 
                    href="{{ route('transactions.index', ['type' => 'income']) }}" 
                    class="btn btn-sm join-item {{ $typeFilter === 'income' ? 'btn-success text-white font-bold' : 'btn-ghost text-base-content/70' }}"
                >
                    Pemasukan
                </a>
                <a 
                    href="{{ route('transactions.index', ['type' => 'expense']) }}" 
                    class="btn btn-sm join-item {{ $typeFilter === 'expense' ? 'btn-error text-white font-bold' : 'btn-ghost text-base-content/70' }}"
                >
                    Pengeluaran
                </a>
            </div>
        </div>

        <!-- Tabel Riwayat Mutasi (DaisyUI Card) -->
        <div class="card bg-base-100 card-border shadow-sm divide-y divide-base-200 overflow-hidden">
            @forelse ($transactions as $tx)
                <div class="p-4 sm:p-5 flex items-center justify-between gap-4 hover:bg-base-200/50 transition-colors">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <!-- Ikon Mutasi -->
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 
                            @if ($tx->type === 'income')
                                bg-success/15 text-success
                            @else
                                bg-error/15 text-error
                            @endif"
                        >
                            @if ($tx->type === 'income')
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 4.5-15 15m0 0h11.25m-11.25 0V8.25" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                                </svg>
                            @endif
                        </div>

                        <!-- Info Deskripsi, Dompet, Tanggal -->
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

                    <!-- Nominal Transaksi & Biaya Admin -->
                    <div class="text-right shrink-0">
                        <span class="block text-sm sm:text-base font-bold tabular-nums {{ $tx->type === 'income' ? 'text-success' : 'text-base-content' }}">
                            {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </span>
                        @if ($tx->fee > 0)
                            <span class="text-[11px] text-base-content/60 block mt-0.5">
                                Admin: Rp {{ number_format($tx->fee, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-xs text-base-content/70">
                        Belum ada data mutasi yang sesuai dengan filter yang dipilih.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($transactions->hasPages())
            <div class="pt-4 flex justify-center">
                {{ $transactions->links() }}
            </div>
        @endif
    </section>

</div>
@endsection
