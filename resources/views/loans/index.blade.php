@extends('layouts.app')

@section('title', 'Hutang Piutang — Finora')

@section('content')
<div class="space-y-8" x-data="{ activeRepayLoan: null, repayAmount: '', repayWallet: '' }">
    
    <!-- HEADER HALAMAN HUTANG PIUTANG -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-base-content">
                Pencatatan Hutang & Piutang
            </h1>
            <p class="text-xs sm:text-sm text-base-content/70 mt-1">
                Pantau piutang yang Anda pinjamkan ke rekan dan kewajiban hutang pribadi secara transparan.
            </p>
        </div>
    </div>

    <!-- WIDGET RINGKASAN: TOTAL PIUTANG & TOTAL HUTANG -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- 1. Total Piutang (Saya Pinjamkan) -->
        <div x-data="{ show: true }" class="card bg-base-100 card-border shadow-xs">
            <div class="card-body p-6 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-primary">
                        Total Piutang (Uang yang Dipinjamkan)
                    </span>
                    <button 
                        type="button" 
                        @click="show = !show" 
                        class="btn btn-ghost btn-circle btn-sm min-h-[44px] min-w-[44px]"
                        :aria-label="show ? 'Sembunyikan Total Piutang' : 'Tampilkan Total Piutang'"
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
                <div class="text-2xl sm:text-3xl font-extrabold tabular-nums text-primary mt-2">
                    <span x-show="show">Rp {{ number_format($totalReceivable, 0, ',', '.') }}</span>
                    <span x-show="!show" class="text-base-content/40">Rp ********</span>
                </div>
                <p class="text-[11px] text-base-content/60 mt-2">
                    Dana yang masih harus dikembalikan oleh rekan Anda
                </p>
            </div>
        </div>

        <!-- 2. Total Hutang (Kewajiban Saya) -->
        <div x-data="{ show: true }" class="card bg-base-100 card-border shadow-xs">
            <div class="card-body p-6 flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-warning">
                        Total Hutang (Kewajiban Saya)
                    </span>
                    <button 
                        type="button" 
                        @click="show = !show" 
                        class="btn btn-ghost btn-circle btn-sm min-h-[44px] min-w-[44px]"
                        :aria-label="show ? 'Sembunyikan Total Hutang' : 'Tampilkan Total Hutang'"
                    >
                        <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg x-show="!show" class="w-4 h-4 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold tabular-nums text-warning mt-2">
                    <span x-show="show">Rp {{ number_format($totalDebt, 0, ',', '.') }}</span>
                    <span x-show="!show" class="text-base-content/40">Rp ********</span>
                </div>
                <p class="text-[11px] text-base-content/60 mt-2">
                    Kewajiban pelunasan yang masih harus Anda bayarkan
                </p>
            </div>
        </div>
    </div>

    <!-- FORMULIR CATAT PINJAMAN BARU -->
    <div 
        x-data="{ loanType: 'loan_given' }" 
        class="card bg-base-100 card-border shadow-xs"
    >
        <div class="card-body p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 border-b border-base-200 gap-4 mb-6">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-base-content">
                        Catat Hutang atau Piutang Baru
                    </h2>
                    <p class="text-xs text-base-content/70 mt-0.5">
                        Saldo dompet yang dipilih akan otomatis disesuaikan.
                    </p>
                </div>

                <!-- Switcher Tipe DaisyUI Join -->
                <div class="join bg-base-200 p-1 rounded-xl">
                    <button 
                        type="button" 
                        @click="loanType = 'loan_given'"
                        :class="loanType === 'loan_given' ? 'btn-primary' : 'btn-ghost'"
                        class="btn btn-sm join-item min-h-[38px] text-xs font-bold"
                    >
                        Piutang (Pinjamkan)
                    </button>
                    <button 
                        type="button" 
                        @click="loanType = 'debt_taken'"
                        :class="loanType === 'debt_taken' ? 'btn-warning text-warning-content' : 'btn-ghost'"
                        class="btn btn-sm join-item min-h-[38px] text-xs font-bold"
                    >
                        Hutang (Kewajiban)
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('loans.store') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="type" :value="loanType">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- 1. Nama Rekan -->
                    <div>
                        <label for="person_name" class="label text-xs font-bold uppercase tracking-wider text-base-content">
                            <span><span x-text="loanType === 'loan_given' ? 'Peminjam (Rekan)' : 'Pemberi Pinjaman'"></span> <span class="text-error">*</span></span>
                        </label>
                        <input 
                            type="text" 
                            id="person_name" 
                            name="person_name" 
                            value="{{ old('person_name') }}" 
                            required 
                            placeholder="Contoh: Dimas Saputra"
                            class="input input-bordered w-full min-h-[44px]"
                        >
                    </div>

                    <!-- 2. Nominal Pinjaman -->
                    <div>
                        <label for="total_amount" class="label text-xs font-bold uppercase tracking-wider text-base-content">
                            <span>Nominal Pinjaman (Rp) <span class="text-error">*</span></span>
                        </label>
                        <input 
                            type="number" 
                            id="total_amount" 
                            name="total_amount" 
                            value="{{ old('total_amount') }}" 
                            min="1" 
                            required 
                            placeholder="Contoh: 500000"
                            class="input input-bordered w-full min-h-[44px] tabular-nums"
                        >
                    </div>

                    <!-- 3. Dompet Terkait -->
                    <div>
                        <label for="wallet_id" class="label text-xs font-bold uppercase tracking-wider text-base-content">
                            <span><span x-text="loanType === 'loan_given' ? 'Dompet Sumber Dana' : 'Dompet Penerima Dana'"></span> <span class="text-error">*</span></span>
                        </label>
                        <select 
                            id="wallet_id" 
                            name="wallet_id" 
                            required
                            class="select select-bordered w-full min-h-[44px]"
                        >
                            <option value="">Pilih Dompet</option>
                            @foreach ($wallets as $wallet)
                                <option value="{{ $wallet->id }}">
                                    {{ $wallet->name }} (Rp {{ number_format($wallet->balance, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 4. Tanggal Jatuh Tempo -->
                    <div>
                        <label for="due_date" class="label text-xs font-bold uppercase tracking-wider text-base-content">
                            <span>Jatuh Tempo (Opsional)</span>
                        </label>
                        <input 
                            type="date" 
                            id="due_date" 
                            name="due_date" 
                            value="{{ old('due_date') }}" 
                            class="input input-bordered w-full min-h-[44px]"
                        >
                    </div>
                </div>

                <!-- Catatan Tambahan -->
                <div>
                    <label for="notes" class="label text-xs font-bold uppercase tracking-wider text-base-content">
                        <span>Catatan / Keperluan</span>
                    </label>
                    <input 
                        type="text" 
                        id="notes" 
                        name="notes" 
                        value="{{ old('notes') }}" 
                        placeholder="Contoh: Talangan beli tiket, patungan kado kantor"
                        class="input input-bordered w-full min-h-[44px]"
                    >
                </div>

                <div class="pt-2 flex justify-end">
                    <button 
                        type="submit" 
                        class="btn btn-primary min-h-[44px] px-6 text-sm font-semibold"
                    >
                        Simpan Catatan Pinjaman
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DAFTAR PINJAMAN & PELUNASAN -->
    <section class="space-y-4">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-base-content">
                Daftar Catatan Pinjaman & Hutang
            </h2>
            <p class="text-xs text-base-content/70 mt-0.5">
                Klik tombol cicil/lunas untuk memperbarui sisa pokok pinjaman.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse ($loans as $loan)
                <div 
                    x-data="{ show: true }" 
                    class="card bg-base-100 card-border shadow-xs flex flex-col justify-between {{ $loan->status === 'paid' ? 'opacity-60 bg-base-200/50' : '' }}"
                >
                    <div class="card-body p-6">
                        <!-- Header Baris: Tipe & Status -->
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="badge badge-sm font-extrabold uppercase {{ $loan->type === 'loan_given' ? 'badge-primary badge-soft' : 'badge-warning badge-soft' }}">
                                {{ $loan->type === 'loan_given' ? 'Piutang' : 'Hutang' }}
                            </span>

                            <span class="badge badge-sm font-bold {{ $loan->status === 'paid' ? 'badge-success text-success-content' : ($loan->status === 'partial' ? 'badge-warning badge-soft' : 'badge-error badge-soft') }}">
                                {{ $loan->status === 'paid' ? 'Lunas' : ($loan->status === 'partial' ? 'Lunas Sebagian' : 'Belum Lunas') }}
                            </span>
                        </div>

                        <h3 class="text-base font-bold text-base-content">
                            {{ $loan->person_name }}
                        </h3>
                        <p class="text-xs text-base-content/70 mt-1">
                            {{ $loan->notes ?: 'Tanpa keterangan tambahan' }}
                        </p>

                        <!-- Sisa Pokok & Total Pinjaman -->
                        <div class="mt-4 pt-3 border-t border-base-200 flex items-center justify-between">
                            <div>
                                <span class="text-[11px] text-base-content/60 block mb-0.5">Sisa Pokok</span>
                                <div class="text-lg font-bold tabular-nums text-base-content">
                                    <span x-show="show">Rp {{ number_format($loan->remaining_amount, 0, ',', '.') }}</span>
                                    <span x-show="!show" class="text-base-content/40">Rp ********</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[11px] text-base-content/60 block mb-0.5">Total Nominal</span>
                                <span class="text-xs font-bold tabular-nums text-base-content/80">
                                    Rp {{ number_format($loan->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Info Jatuh Tempo & Dompet -->
                        <div class="mt-3 text-xs text-base-content/70 flex flex-wrap items-center gap-2">
                            <span>Dompet: <strong class="text-base-content">{{ $loan->wallet->name ?? '-' }}</strong></span>
                            @if ($loan->due_date)
                                <span>/</span>
                                <span>Jatuh tempo: <strong class="text-base-content">{{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}</strong></span>
                            @endif
                        </div>

                        <!-- Tombol Pelunasan / Cicilan -->
                        @if ($loan->status !== 'paid')
                            <div class="mt-6 pt-4 border-t border-base-200 flex justify-end">
                                <button 
                                    type="button" 
                                    @click="activeRepayLoan = {{ $loan->id }}; repayAmount = '{{ $loan->remaining_amount }}'; repayWallet = '{{ $loan->wallet_id }}'"
                                    class="btn btn-primary btn-sm min-h-[44px]"
                                >
                                    Catat Pelunasan / Cicilan
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-2 card bg-base-100 card-border shadow-xs">
                    <div class="card-body p-8 text-center">
                        <p class="text-xs text-base-content/70">
                            Belum ada catatan hutang atau piutang. Gunakan formulir di atas untuk mencatat.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    <!-- MODAL PELUNASAN / CICILAN -->
    @foreach ($loans as $loan)
        @if ($loan->status !== 'paid')
            <div 
                class="modal"
                :class="{ 'modal-open': activeRepayLoan === {{ $loan->id }} }"
                role="dialog"
                aria-modal="true"
                x-show="activeRepayLoan === {{ $loan->id }}"
                x-cloak
            >
                <div 
                    @click.away="activeRepayLoan = null" 
                    class="modal-box bg-base-100 card-border shadow-xl max-w-md w-full"
                >
                    <h3 class="text-base font-bold text-base-content mb-1">
                        Catat Pembayaran: {{ $loan->person_name }}
                    </h3>
                    <p class="text-xs text-base-content/70 mb-5">
                        Sisa pokok saat ini: <strong class="text-base-content">Rp {{ number_format($loan->remaining_amount, 0, ',', '.') }}</strong>
                    </p>

                    <form method="POST" action="{{ route('loans.repay', $loan) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="label text-xs font-bold uppercase tracking-wider text-base-content">
                                <span>Nominal Pembayaran (Rp) <span class="text-error">*</span></span>
                            </label>
                            <input 
                                type="number" 
                                name="amount" 
                                max="{{ $loan->remaining_amount }}" 
                                min="1" 
                                required 
                                x-model="repayAmount"
                                class="input input-bordered w-full min-h-[44px] tabular-nums"
                            >
                        </div>

                        <div>
                            <label class="label text-xs font-bold uppercase tracking-wider text-base-content">
                                <span>Dompet <span class="text-error">*</span></span>
                            </label>
                            <select 
                                name="wallet_id" 
                                required 
                                x-model="repayWallet"
                                class="select select-bordered w-full min-h-[44px]"
                            >
                                @foreach ($wallets as $wallet)
                                    <option value="{{ $wallet->id }}">
                                        {{ $wallet->name }} (Rp {{ number_format($wallet->balance, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="label text-xs font-bold uppercase tracking-wider text-base-content">
                                <span>Tanggal Pembayaran <span class="text-error">*</span></span>
                            </label>
                            <input 
                                type="date" 
                                name="repayment_date" 
                                value="{{ now()->toDateString() }}" 
                                required 
                                class="input input-bordered w-full min-h-[44px]"
                            >
                        </div>

                        <div class="modal-action pt-3 flex justify-end gap-3">
                            <button 
                                type="button" 
                                @click="activeRepayLoan = null" 
                                class="btn btn-ghost min-h-[44px]"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit" 
                                class="btn btn-primary min-h-[44px] px-5"
                            >
                                Simpan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
                <div class="modal-backdrop" @click="activeRepayLoan = null"></div>
            </div>
        @endif
    @endforeach

</div>
@endsection
