@extends('layouts.app')

@section('title', 'Riwayat Bulanan — Finora')

@section('content')
<div class="space-y-8" x-data="{ showDemoPdfNotice: false, selectedMonth: '', showTableAmounts: true }">
    
    <!-- HEADER HALAMAN RIWAYAT -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-base-content">
                Riwayat Laporan Keuangan Bulanan
            </h1>
            <p class="text-xs sm:text-sm text-base-content/70 mt-1">
                Rekapitulasi berkala arus kas, pemasukan, dan pengeluaran dari bulan-bulan sebelumnya.
            </p>
        </div>

        <!-- Tombol Privasi Nilai Tabel -->
        <button 
            type="button" 
            @click="showTableAmounts = !showTableAmounts" 
            class="btn btn-outline btn-sm min-h-[44px] inline-flex items-center gap-2 self-start sm:self-auto"
        >
            <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            <span x-text="showTableAmounts ? 'Sembunyikan Angka' : 'Tampilkan Angka'"></span>
        </button>
    </div>

    <!-- MODAL INFORMASI PENGEMBANGAN FITUR UNDUH PDF (DAISYUI MODAL) -->
    <div 
        class="modal" 
        :class="{ 'modal-open': showDemoPdfNotice }" 
        role="dialog" 
        aria-modal="true" 
        x-show="showDemoPdfNotice" 
        x-cloak
    >
        <div class="modal-box bg-base-100 card-border shadow-2xl max-w-md text-center p-6 sm:p-7" @click.away="showDemoPdfNotice = false">
            <div class="w-14 h-14 rounded-2xl bg-warning/15 text-warning mx-auto flex items-center justify-center mb-4">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </div>
            <h3 class="text-base sm:text-lg font-bold text-base-content">
                Fitur Masih Dalam Tahap Pengembangan
            </h3>
            <p class="text-xs sm:text-sm text-base-content/70 mt-2 leading-relaxed">
                Unduh dokumen laporan PDF untuk periode <strong class="text-base-content" x-text="selectedMonth"></strong> saat ini masih dalam proses pengembangan. Generator dokumen rekap mutasi dan neraca saldo periode ini akan aktif pada pembaruan mendatang.
            </p>
            <div class="modal-action justify-center mt-6">
                <button 
                    type="button" 
                    @click="showDemoPdfNotice = false" 
                    class="btn btn-primary min-h-[44px] px-6 text-xs sm:text-sm font-semibold"
                >
                    Mengerti
                </button>
            </div>
        </div>
        <div class="modal-backdrop" @click="showDemoPdfNotice = false"></div>
    </div>

    <!-- TABEL REKAPITULASI BULANAN -->
    <div class="card bg-base-100 card-border shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full text-left">
                <thead>
                    <tr class="bg-base-200/50 text-[11px] font-bold uppercase tracking-wider text-base-content/70">
                        <th class="py-4 px-5">Periode Bulan</th>
                        <th class="py-4 px-5">Total Pemasukan</th>
                        <th class="py-4 px-5">Total Pengeluaran</th>
                        <th class="py-4 px-5">Arus Kas Bersih</th>
                        <th class="py-4 px-5 text-center">Jumlah Mutasi</th>
                        <th class="py-4 px-5 text-right">Aksi Dokumen</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach ($historyData as $row)
                        <tr class="hover">
                            <!-- 1. Periode -->
                            <td class="py-4 px-5 font-bold text-base-content">
                                <div class="flex items-center gap-2">
                                    <span>{{ $row['period_label'] }}</span>
                                    @if ($row['is_current'])
                                        <span class="badge badge-sm badge-success badge-soft font-extrabold uppercase">
                                            Bulan Ini
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- 2. Pemasukan -->
                            <td class="py-4 px-5 font-semibold tabular-nums text-success">
                                <span x-show="showTableAmounts">+ Rp {{ number_format($row['income'], 0, ',', '.') }}</span>
                                <span x-show="!showTableAmounts">+ Rp ********</span>
                            </td>

                            <!-- 3. Pengeluaran -->
                            <td class="py-4 px-5 font-semibold tabular-nums text-error">
                                <span x-show="showTableAmounts">- Rp {{ number_format($row['expense'], 0, ',', '.') }}</span>
                                <span x-show="!showTableAmounts">- Rp ********</span>
                            </td>

                            <!-- 4. Arus Kas Bersih -->
                            <td class="py-4 px-5 font-bold tabular-nums {{ $row['net'] >= 0 ? 'text-success' : 'text-error' }}">
                                <span x-show="showTableAmounts">
                                    {{ $row['net'] >= 0 ? '+' : '-' }} Rp {{ number_format(abs($row['net']), 0, ',', '.') }}
                                </span>
                                <span x-show="!showTableAmounts">
                                    {{ $row['net'] >= 0 ? '+' : '-' }} Rp ********
                                </span>
                            </td>

                            <!-- 5. Jumlah Mutasi -->
                            <td class="py-4 px-5 text-center text-base-content/80 font-medium">
                                {{ $row['tx_count'] }} transaksi
                            </td>

                            <!-- 6. Tombol Unduh PDF (Demo) -->
                            <td class="py-4 px-5 text-right">
                                <button 
                                    type="button" 
                                    @click="selectedMonth = '{{ $row['period_label'] }}'; showDemoPdfNotice = true"
                                    class="btn btn-outline btn-sm min-h-[44px] inline-flex items-center gap-1.5"
                                >
                                    <svg class="w-4 h-4 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    <span>Unduh PDF</span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
