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
    <div class="pt-4 flex justify-center ajax-pagination">
        {{ $transactions->links() }}
    </div>
@endif
