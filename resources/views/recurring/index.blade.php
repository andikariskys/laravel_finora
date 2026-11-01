@extends('layouts.app')

@section('title', 'Tagihan & Biaya Rutin — Finora')

@section('content')
<div class="space-y-8">
    
    <!-- HEADER HALAMAN TAGIHAN -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-base-content">
                Tagihan & Pengeluaran Rutin
            </h1>
            <p class="text-xs sm:text-sm text-base-content/70 mt-1">
                Kelola langganan aplikasi, iuran, cicilan rutin harian, mingguan, bulanan, maupun tanggal khusus.
            </p>
        </div>
    </div>

    <!-- WIDGET RINGKASAN TAGIHAN (DaisyUI Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- 1. Estimasi Total Beban Rutin per Bulan -->
        <div class="card bg-base-100 card-border shadow-sm">
            <div class="card-body p-5 justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-base-content/70">
                    Estimasi Beban Bulanan
                </span>
                <div class="text-xl sm:text-2xl font-bold tabular-nums text-base-content mt-1">
                    Rp {{ number_format($monthlyTotalEstimate, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-base-content/60 mt-2">
                    Proyeksi total pengeluaran dari tagihan aktif
                </p>
            </div>
        </div>

        <!-- 2. Tagihan Aktif -->
        <div class="card bg-base-100 card-border shadow-sm">
            <div class="card-body p-5 justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-success">
                    Tagihan Aktif
                </span>
                <div class="text-2xl font-bold text-base-content mt-1">
                    {{ $activeRules->count() }} Tagihan
                </div>
                <p class="text-[11px] text-base-content/60 mt-2">
                    Dipantau secara berkala oleh sistem
                </p>
            </div>
        </div>

        <!-- 3. Tagihan Non-Aktif / Dijeda -->
        <div class="card bg-base-100 card-border shadow-sm">
            <div class="card-body p-5 justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-base-content/70">
                    Tagihan Dijeda
                </span>
                <div class="text-2xl font-bold text-base-content mt-1">
                    {{ $inactiveRules->count() }} Tagihan
                </div>
                <p class="text-[11px] text-base-content/60 mt-2">
                    Langganan yang dihentikan sementara
                </p>
            </div>
        </div>
    </div>

    <!-- FORMULIR TAMBAH TAGIHAN RUTIN BARU (DaisyUI Card) -->
    <div 
        x-data="{ 
            frequency: '{{ old('frequency', 'monthly') }}',
            weeklyDay: '{{ old('weekly_day', 'friday') }}',
            monthlyDay: '{{ old('monthly_day', 1) }}',
            dueDate: '{{ old('due_date', '') }}'
        }" 
        class="card bg-base-100 card-border shadow-sm"
    >
        <div class="card-body p-6 sm:p-8 space-y-6">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-base-content">
                    Tambah Tagihan & Biaya Rutin Baru
                </h2>
                <p class="text-xs text-base-content/70 mt-0.5">
                    Atur jadwal pengeluaran rutin Anda berdasarkan siklus harian, mingguan, bulanan, atau tanggal khusus tertentu.
                </p>
            </div>

            <form method="POST" action="{{ route('recurring.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <!-- 1. Nama Tagihan -->
                    <div class="form-control lg:col-span-1">
                        <label for="name" class="label">
                            <span class="label-text font-bold text-xs uppercase tracking-wider">
                                Nama Tagihan / Langganan <span class="text-error">*</span>
                            </span>
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}" 
                            required 
                            placeholder="Contoh: Spotify, Listrik PLN, Sewa VPS, Laundry"
                            class="input input-bordered w-full"
                        >
                    </div>

                    <!-- 2. Dompet Sumber Pembayaran -->
                    <div class="form-control">
                        <label for="wallet_id" class="label">
                            <span class="label-text font-bold text-xs uppercase tracking-wider">
                                Sumber Dompet Pemotongan <span class="text-error">*</span>
                            </span>
                        </label>
                        <select 
                            id="wallet_id" 
                            name="wallet_id" 
                            required
                            class="select select-bordered w-full"
                        >
                            <option value="">Pilih dompet...</option>
                            @foreach ($activeWallets as $w)
                                <option value="{{ $w->id }}" {{ old('wallet_id') == $w->id ? 'selected' : '' }}>
                                    {{ $w->name }} (Rp {{ number_format($w->balance, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3. Nominal Pengeluaran -->
                    <div class="form-control">
                        <label for="amount" class="label">
                            <span class="label-text font-bold text-xs uppercase tracking-wider">
                                Nominal Pembayaran (Rp) <span class="text-error">*</span>
                            </span>
                        </label>
                        <input 
                            type="number" 
                            id="amount" 
                            name="amount" 
                            value="{{ old('amount') }}" 
                            required 
                            min="1" 
                            step="1" 
                            placeholder="Contoh: 86900"
                            class="input input-bordered w-full tabular-nums"
                        >
                    </div>
                </div>

                <!-- 4. Pilihan Frekuensi & Parameter Tanggal -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-1">
                    <!-- Dropdown Frekuensi -->
                    <div class="form-control">
                        <label for="frequency" class="label">
                            <span class="label-text font-bold text-xs uppercase tracking-wider">
                                Siklus / Frekuensi Pembayaran <span class="text-error">*</span>
                            </span>
                        </label>
                        <select 
                            id="frequency" 
                            name="frequency" 
                            x-model="frequency" 
                            required
                            class="select select-bordered w-full"
                        >
                            <option value="daily">Harian (Setiap Hari)</option>
                            <option value="weekly">Mingguan (Hari Tertentu)</option>
                            <option value="monthly">Bulanan (Tanggal Tertentu)</option>
                            <option value="custom_date">Tanggal Khusus (Spesifik)</option>
                        </select>
                    </div>

                    <!-- Parameter Dinamis Berdasarkan Frekuensi -->
                    <div>
                        <!-- Case A: Harian -->
                        <div x-show="frequency === 'daily'">
                            <span class="block text-xs font-bold uppercase tracking-wider text-base-content/70 mb-1.5">
                                Keterangan Eksekusi
                            </span>
                            <div class="min-h-[44px] px-3.5 py-2.5 rounded-box bg-base-200/50 border border-base-300 text-xs text-base-content/70 flex items-center">
                                Tagihan berulang setiap hari secara otomatis.
                            </div>
                        </div>

                        <!-- Case B: Mingguan -->
                        <div x-show="frequency === 'weekly'" class="form-control">
                            <label for="weekly_day" class="label">
                                <span class="label-text font-bold text-xs uppercase tracking-wider">
                                    Pilih Hari dalam Seminggu <span class="text-error">*</span>
                                </span>
                            </label>
                            <select 
                                id="weekly_day" 
                                name="weekly_day" 
                                x-model="weeklyDay"
                                class="select select-bordered w-full"
                            >
                                <option value="monday">Setiap Hari Senin</option>
                                <option value="tuesday">Setiap Hari Selasa</option>
                                <option value="wednesday">Setiap Hari Rabu</option>
                                <option value="thursday">Setiap Hari Kamis</option>
                                <option value="friday">Setiap Hari Jumat</option>
                                <option value="saturday">Setiap Hari Sabtu</option>
                                <option value="sunday">Setiap Hari Minggu</option>
                            </select>
                        </div>

                        <!-- Case C: Bulanan -->
                        <div x-show="frequency === 'monthly'" class="form-control">
                            <label for="monthly_day" class="label">
                                <span class="label-text font-bold text-xs uppercase tracking-wider">
                                    Tanggal Pembayaran Bulanan (1 - 28) <span class="text-error">*</span>
                                </span>
                            </label>
                            <select 
                                id="monthly_day" 
                                name="monthly_day" 
                                x-model="monthlyDay"
                                class="select select-bordered w-full"
                            >
                                @for ($d = 1; $d <= 28; $d++)
                                    <option value="{{ $d }}">Tanggal {{ $d }} setiap bulan</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Case D: Tanggal Khusus -->
                        <div x-show="frequency === 'custom_date'" class="form-control">
                            <label for="due_date" class="label">
                                <span class="label-text font-bold text-xs uppercase tracking-wider">
                                    Pilih Tanggal Jatuh Tempo Khusus <span class="text-error">*</span>
                                </span>
                            </label>
                            <input 
                                type="date" 
                                id="due_date" 
                                name="due_date" 
                                x-model="dueDate"
                                class="input input-bordered w-full"
                            >
                        </div>
                    </div>
                </div>

                <!-- Tombol Simpan Tagihan (DaisyUI btn-primary) -->
                <div class="pt-2 flex justify-end">
                    <button type="submit" class="btn btn-primary">
                        Tambah Tagihan Rutin
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DAFTAR SELURUH TAGIHAN & BIAYA RUTIN -->
    <section class="space-y-4">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-base-content">
                Daftar Tagihan & Pengeluaran Berulang
            </h2>
            <p class="text-xs text-base-content/70 mt-0.5">
                Dikelompokkan berdasarkan status aktif dan jadwal eksekusi pembayaran.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($rules as $rule)
                @php
                    $dayNames = [
                        'monday' => 'Senin',
                        'tuesday' => 'Selasa',
                        'wednesday' => 'Rabu',
                        'thursday' => 'Kamis',
                        'friday' => 'Jumat',
                        'saturday' => 'Sabtu',
                        'sunday' => 'Minggu',
                    ];
                @endphp
                <div class="card bg-base-100 card-border shadow-sm flex flex-col justify-between {{ !$rule->is_active ? 'opacity-65 bg-base-200/50' : '' }}">
                    <div class="card-body p-6 justify-between">
                        <div>
                            <!-- Header Kartu: Frekuensi & Status (DaisyUI Badges) -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <!-- Badge Frekuensi -->
                                <span class="badge badge-neutral badge-soft badge-sm">
                                    @if ($rule->frequency === 'daily')
                                        Harian
                                    @elseif ($rule->frequency === 'weekly')
                                        Mingguan ({{ $dayNames[$rule->frequency_value] ?? $rule->frequency_value }})
                                    @elseif ($rule->frequency === 'monthly')
                                        Bulanan (Tgl {{ $rule->frequency_value ?? $rule->execution_day }})
                                    @elseif ($rule->frequency === 'custom_date')
                                        Tanggal Khusus ({{ $rule->due_date ? \Carbon\Carbon::parse($rule->due_date)->translatedFormat('d M Y') : $rule->frequency_value }})
                                    @else
                                        Bulanan
                                    @endif
                                </span>

                                <!-- Status Badge -->
                                <span class="badge badge-sm {{ $rule->is_active ? 'badge-success badge-soft' : 'badge-neutral badge-soft' }}">
                                    {{ $rule->is_active ? 'Aktif' : 'Dijeda' }}
                                </span>
                            </div>

                            <!-- Nama Tagihan -->
                            <h3 class="text-lg font-bold text-base-content">
                                {{ $rule->name }}
                            </h3>

                            <!-- Nominal Tagihan -->
                            <div class="text-xl font-extrabold tabular-nums text-base-content mt-3">
                                Rp {{ number_format($rule->amount, 0, ',', '.') }}
                            </div>

                            <!-- Dompet Pembayar -->
                            <p class="text-xs text-base-content/70 mt-1 flex items-center gap-1.5">
                                <span>Dompet:</span>
                                <span class="font-semibold text-base-content">{{ $rule->wallet->name ?? 'Semua Dompet' }}</span>
                            </p>
                        </div>

                        <!-- Aksi: Toggle Status & Hapus (DaisyUI Buttons) -->
                        <div class="mt-6 pt-4 border-t border-base-200 flex items-center justify-between gap-2">
                            <!-- Toggle Aktif/Jeda -->
                            <form method="POST" action="{{ route('recurring.toggle', $rule) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline btn-sm">
                                    {{ $rule->is_active ? 'Jeda Tagihan' : 'Aktifkan Kembali' }}
                                </button>
                            </form>

                            <!-- Hapus Tagihan -->
                            <form 
                                method="POST" 
                                action="{{ route('recurring.destroy', $rule) }}" 
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus tagihan {{ addslashes($rule->name) }}?');"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm text-error">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card bg-base-100 card-border p-8 col-span-3 text-center">
                    <p class="text-xs text-base-content/70">
                        Belum ada tagihan rutin yang didaftarkan. Gunakan formulir di atas untuk mencatat langganan Spotify, Netflix, tagihan listrik, atau pengeluaran harian/mingguan Anda.
                    </p>
                </div>
            @endforelse
        </div>
    </section>

</div>
@endsection
