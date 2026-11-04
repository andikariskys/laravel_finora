<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $periodLabel }} — Finora</title>
    <style>
        @page {
            margin: 28px 28px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #059669;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .brand-title {
            font-size: 18px;
            font-weight: bold;
            color: #059669;
            margin: 0;
        }
        .brand-subtitle {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }
        .doc-title {
            text-align: right;
        }
        .doc-title h2 {
            font-size: 13px;
            margin: 0;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-title p {
            font-size: 9px;
            color: #64748b;
            margin: 2px 0 0 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 14px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 10px;
        }
        .meta-label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        .meta-val {
            font-size: 10px;
            font-weight: 600;
            color: #0f172a;
        }
        .summary-cards {
            width: 100%;
            margin-bottom: 16px;
            border-spacing: 6px 0;
        }
        .summary-card {
            width: 33.3%;
            border-radius: 4px;
            padding: 8px 10px;
            vertical-align: top;
        }
        .summary-card.income {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
        }
        .summary-card.expense {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
        }
        .summary-card.net {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
        }
        .card-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            color: #475569;
        }
        .card-val {
            font-size: 13px;
            font-weight: bold;
            margin-top: 2px;
        }
        .text-income { color: #059669; }
        .text-expense { color: #dc2626; }
        .text-net { color: #2563eb; }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .items-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 6px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .items-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
            vertical-align: middle;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-income { background-color: #d1fae5; color: #065f46; }
        .badge-expense { background-color: #fee2e2; color: #991b1b; }
        .badge-transfer { background-color: #e0e7ff; color: #3730a3; }
        .badge-other { background-color: #f1f5f9; color: #475569; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .tabular { font-family: 'Courier New', Courier, monospace; font-size: 9.5px; }

        .section-title {
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 12px 0 6px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #cbd5e1;
        }

        .footer {
            margin-top: 18px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- KOP LAPORAN -->
    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <h1 class="brand-title">Finora</h1>
                <div class="brand-subtitle">Financial Organizer & Record Assistant</div>
            </td>
            <td class="doc-title" style="width: 45%; vertical-align: top;">
                <h2>Laporan Mutasi Keuangan</h2>
                <p>Periode: <strong>{{ $periodLabel }}</strong></p>
                <p>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </td>
        </tr>
    </table>

    <!-- INFO PENGGUNA & RINGKASAN -->
    <table class="meta-table">
        <tr>
            <td style="width: 35%;">
                <div class="meta-label">Nama Pemilik Akun</div>
                <div class="meta-val">{{ $user->name }}</div>
            </td>
            <td style="width: 35%;">
                <div class="meta-label">Alamat Email</div>
                <div class="meta-val">{{ $user->email }}</div>
            </td>
            <td style="width: 30%;">
                <div class="meta-label">Total Transaksi</div>
                <div class="meta-val">{{ $transactions->count() }} Transaksi Tercatat</div>
            </td>
        </tr>
    </table>

    <!-- KARTU RINGKASAN ARUS KAS -->
    <table class="summary-cards">
        <tr>
            <td class="summary-card income">
                <div class="card-label">Total Pemasukan</div>
                <div class="card-val text-income">+ Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </td>
            <td class="summary-card expense">
                <div class="card-label">Total Pengeluaran</div>
                <div class="card-val text-expense">- Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </td>
            <td class="summary-card net">
                <div class="card-label">Arus Kas Bersih (Net)</div>
                <div class="card-val text-net">
                    {{ $netCashflow >= 0 ? '+' : '-' }} Rp {{ number_format(abs($netCashflow), 0, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- INFORMASI DOMPET & REKENING TERKAIT -->
    <div class="section-title">Informasi Dompet &amp; Rekening</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 25%;">Nama Dompet</th>
                <th style="width: 20%;">Jenis Dompet</th>
                <th style="width: 15%;" class="text-center">Status</th>
                <th style="width: 15%;" class="text-center">Aktivitas Periode Ini</th>
                <th style="width: 20%;" class="text-right">Saldo Saat Ini</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($wallets as $wIndex => $w)
                @php
                    $walletTypeLabels = [
                        'cash' => 'Uang Tunai',
                        'bank' => 'Rekening Bank',
                        'ewallet' => 'Dompet Digital',
                        'investment' => 'Investasi',
                    ];
                    $wLabel = $walletTypeLabels[$w->type] ?? ucfirst($w->type);
                    $wTxCount = $transactions->where('wallet_id', $w->id)->count();
                @endphp
                <tr>
                    <td class="text-center">{{ $wIndex + 1 }}</td>
                    <td><strong>{{ $w->name }}</strong></td>
                    <td>{{ $wLabel }}</td>
                    <td class="text-center">
                        @if ($w->is_active)
                            <span class="badge badge-income">Aktif</span>
                        @else
                            <span class="badge badge-other">Non-Aktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $wTxCount }} transaksi
                    </td>
                    <td class="text-right tabular">
                        Rp {{ number_format($w->balance, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 10px; color: #94a3b8;">
                        Tidak ada dompet terdaftar.
                    </td>
                </tr>
            @endforelse
            @if ($wallets->isNotEmpty())
                <tr style="background-color: #f1f5f9; border-top: 2px solid #cbd5e1;">
                    <td colspan="5" class="text-right" style="font-weight: bold; font-size: 8.5px; text-transform: uppercase; color: #475569;">
                        Total Saldo Semua Dompet
                    </td>
                    <td class="text-right tabular" style="font-weight: bold; font-size: 9.5px; color: #0f172a;">
                        Rp {{ number_format($totalWalletBalance ?? $wallets->sum('balance'), 0, ',', '.') }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- TABEL RINCIAN MUTASI TRANSAKSI -->
    <div class="section-title">Rincian Mutasi Transaksi</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 14%;">Tanggal</th>
                <th style="width: 12%;">Dompet</th>
                <th style="width: 12%;">Tipe</th>
                <th style="width: 31%;">Keterangan / Sumber</th>
                <th style="width: 14%;" class="text-right">Nominal</th>
                <th style="width: 12%;" class="text-right">Biaya Admin</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $index => $tx)
                @php
                    $isIncome = ($tx->type === 'income');
                    $isExpense = in_array($tx->type, ['expense', 'recurring_expense']);
                    $isTransfer = in_array($tx->type, ['transfer_in', 'transfer_out']);
                    
                    $typeLabels = [
                        'income' => 'Pemasukan',
                        'expense' => 'Pengeluaran',
                        'recurring_expense' => 'Tagihan Rutin',
                        'transfer_in' => 'Transfer Masuk',
                        'transfer_out' => 'Transfer Keluar',
                        'initial_balance' => 'Saldo Awal',
                        'balance_adjustment' => 'Penyesuaian',
                    ];
                    $typeLabel = $typeLabels[$tx->type] ?? ucfirst($tx->type);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($tx->transaction_date)->format('d/m/Y') }}</td>
                    <td><strong>{{ $tx->wallet->name ?? '-' }}</strong></td>
                    <td>
                        @if ($isIncome)
                            <span class="badge badge-income">{{ $typeLabel }}</span>
                        @elseif ($isExpense)
                            <span class="badge badge-expense">{{ $typeLabel }}</span>
                        @elseif ($isTransfer)
                            <span class="badge badge-transfer">{{ $typeLabel }}</span>
                        @else
                            <span class="badge badge-other">{{ $typeLabel }}</span>
                        @endif
                    </td>
                    <td>{{ $tx->description ?: ($tx->source ?: 'Mutasi Transaksi') }}</td>
                    <td class="text-right tabular {{ $isIncome ? 'text-income' : ($isExpense ? 'text-expense' : '') }}">
                        {{ $isIncome ? '+' : ($isExpense ? '-' : '') }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </td>
                    <td class="text-right tabular">
                        @if ($tx->fee > 0)
                            Rp {{ number_format($tx->fee, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 16px; color: #94a3b8;">
                        Tidak ada transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini diterbitkan secara otomatis oleh sistem Finora Financial Organizer. Halaman laporan resmi riwayat keuangan pengguna.
    </div>

</body>
</html>
