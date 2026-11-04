@extends('layouts.app')

@section('title', 'Riwayat Bulanan — Finora')

@section('content')
<div class="space-y-8" x-data="{ showTableAmounts: true }">
    
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

        <!-- Tombol Aksi Dokumen PDF Seluruh Riwayat & Privasi Nilai -->
        <div class="flex flex-wrap items-center gap-2.5">
            <a 
                href="{{ route('history.pdf', ['period' => 'all', 'action' => 'view']) }}" 
                target="_blank"
                class="btn btn-primary btn-sm min-h-[44px] inline-flex items-center gap-1.5"
                title="Buka pratinjau dokumen PDF seluruh riwayat transaksi di tab baru"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <span>Lihat PDF Lengkap</span>
            </a>

            <a 
                href="{{ route('history.pdf', ['period' => 'all', 'action' => 'download']) }}" 
                class="btn btn-outline btn-sm min-h-[44px] inline-flex items-center gap-1.5"
                title="Unduh langsung dokumen PDF seluruh riwayat transaksi"
            >
                <svg class="w-4 h-4 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                <span>Unduh PDF</span>
            </a>

            <button 
                type="button" 
                @click="showTableAmounts = !showTableAmounts" 
                class="btn btn-ghost btn-sm min-h-[44px] border border-base-300 inline-flex items-center gap-2"
            >
                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span x-text="showTableAmounts ? 'Sembunyikan Angka' : 'Tampilkan Angka'"></span>
            </button>
        </div>
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

                            <!-- 6. Aksi Dokumen PDF Per Periode Bulan -->
                            <td class="py-4 px-5 text-right">
                                <div class="dropdown dropdown-end">
                                    <div tabindex="0" role="button" class="btn btn-outline btn-sm min-h-[44px] inline-flex items-center gap-1.5" aria-label="Menu Dokumen PDF Periode {{ $row['period_label'] }}">
                                        <svg class="w-4 h-4 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        <span>Dokumen PDF</span>
                                        <svg class="w-3.5 h-3.5 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                        </svg>
                                    </div>
                                    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-20 w-48 p-2 shadow-lg border border-base-200 text-xs">
                                        <li>
                                            <a href="{{ route('history.pdf', ['period' => $row['period_key'], 'action' => 'view']) }}" target="_blank" class="py-2.5 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                <span>Lihat Dokumen</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('history.pdf', ['period' => $row['period_key'], 'action' => 'download']) }}" class="py-2.5 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                                </svg>
                                                <span>Unduh File PDF</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
