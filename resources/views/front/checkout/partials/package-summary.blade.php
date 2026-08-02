{{-- Package Summary Section --}}
{{-- Requirements: 1.1, 1.4 --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden sticky top-24">
    <!-- Header with gradient -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
        <h3 class="text-lg font-bold text-white flex items-center">
            <svg class="w-5 h-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            Ringkasan Pesanan
        </h3>
    </div>

    <div class="p-6">
        <!-- Package Image -->
        @if($package->thumbnail_url)
            <div class="mb-4 rounded-xl overflow-hidden shadow-md">
                <img 
                    src="{{ $package->thumbnail_url }}" 
                    alt="{{ $package->name }}" 
                    class="w-full h-44 object-cover hover:scale-105 transition-transform duration-300"
                >
            </div>
        @endif

        <!-- Package Name & Description -->
        <div class="mb-5">
            <h4 class="font-bold text-gray-900 text-lg mb-2">{{ $package->name }}</h4>
            @if($package->description)
                <p class="text-sm text-gray-500 line-clamp-2">{{ $package->description }}</p>
            @endif
        </div>

        <!-- Destinations List - Improved Design -->
        @if($package->items && $package->items->count() > 0)
            <div class="mb-5">
                <h5 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-blue-600" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Destinasi Wisata
                </h5>
                <div class="space-y-2">
                    @foreach($package->items->take(4) as $index => $item)
                        <div class="flex items-center bg-gray-50 rounded-lg px-3 py-2.5 hover:bg-blue-50 transition-colors">
                            <span class="flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-600 rounded-full text-xs font-bold mr-3 flex-shrink-0">
                                {{ $index + 1 }}
                            </span>
                            <span class="text-sm text-gray-700 font-medium">{{ $item->destination->name }}</span>
                        </div>
                    @endforeach
                    @if($package->items->count() > 4)
                        <div class="text-center py-2">
                            <span class="text-xs text-blue-600 font-medium bg-blue-50 px-3 py-1 rounded-full">
                                +{{ $package->items->count() - 4 }} destinasi lainnya
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Price Summary - Card Style -->
        <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl p-4 mb-5">
            <div class="space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Harga per orang</span>
                    <span class="text-gray-900 font-semibold" data-role="per-pax-price">Rp {{ number_format($package->total_price, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">Jumlah peserta</span>
                    <span class="text-gray-900 font-semibold" id="summary-participants">{{ old('participants', $pendingBooking['participants'] ?? 1) }}</span>
                </div>

                {{-- Discount row (hidden by default, shown via JS when tier applies) --}}
                <div id="summary-discount-row" class="hidden flex justify-between items-center text-sm">
                    <span class="text-green-700 font-medium">Diskon</span>
                    <span class="text-green-700 font-bold" data-role="discount-amount">- Rp 0</span>
                </div>

                <div class="border-t border-gray-200 pt-3 mt-3">
                    <div class="flex justify-between items-center">
                        <span class="text-base font-bold text-gray-900">Total Pembayaran</span>
                        <span class="text-2xl font-bold text-blue-600" id="summary-total-price">
                            Rp {{ number_format($package->total_price * (old('participants', $pendingBooking['participants'] ?? 1)), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trust Badges - Horizontal Pills -->
        <div class="flex flex-wrap gap-2 mb-5">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-100">
                <svg class="w-3.5 h-3.5 mr-1.5" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                Aman & Terenkripsi
            </span>
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                <svg class="w-3.5 h-3.5 mr-1.5" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Konfirmasi Instan
            </span>
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                <svg class="w-3.5 h-3.5 mr-1.5" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Support 24/7
            </span>
        </div>

        <!-- Back to Package Link -->
        <a 
            href="{{ route('front.packages.show', $package->slug) }}" 
            class="flex items-center justify-center w-full py-3 px-4 border-2 border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200"
        >
            <svg class="w-4 h-4 mr-2" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Detail Paket
        </a>
    </div>
</div>

@push('scripts')
<script>
    // Update summary participants when booking form changes
    document.addEventListener('DOMContentLoaded', function() {
        const participantsInput = document.getElementById('participants');
        const summaryParticipants = document.getElementById('summary-participants');
        
        if (participantsInput && summaryParticipants) {
            participantsInput.addEventListener('input', function() {
                summaryParticipants.textContent = this.value || 1;
            });
        }
    });
</script>
@endpush
