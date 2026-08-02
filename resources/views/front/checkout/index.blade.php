@extends('layouts.front')

@section('title', 'Checkout - ' . $package->name . ' - EziTour')

@section('seo')
    <x-seo :title="'Checkout - ' . $package->name" noindex />
@endsection

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('front.home') }}" class="text-gray-500 hover:text-blue-600 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1.5" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <a href="{{ route('front.packages.index') }}" class="ml-1 text-gray-500 hover:text-blue-600 text-sm">Paket Wisata</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="ml-1 text-gray-700 text-sm font-medium">Checkout</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Alert Messages -->
        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-r-lg flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 text-blue-700 px-4 py-3 rounded-r-lg flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                {{ session('info') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-r-lg flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Booking Form & Auth Section -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Booking Form Section -->
                @include('front.checkout.partials.booking-form')

                <!-- Auth Section (only for guests) -->
                @if(!$isAuthenticated)
                    @include('front.checkout.partials.auth-section')
                @endif
            </div>

            <!-- Right Column: Package Summary -->
            <div class="lg:col-span-1">
                @include('front.checkout.partials.package-summary')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Price calculation variables
    const basePricePerPax = {{ $package->total_price }};
    // Active tiers sorted by sort_order (ASC). Empty array = linear pricing.
    const tiers = {{ $package->priceTiers()->orderBy('sort_order')->get(['name','min_pax','max_pax','price_per_pax'])->toJson() }};

    // Resolve the first matching tier for a participant count.
    function resolveTier(participants) {
        for (const tier of tiers) {
            const minOk = tier.min_pax <= participants;
            const maxOk = tier.max_pax === null || tier.max_pax >= participants;
            if (minOk && maxOk) return tier;
        }
        return null;
    }

    // Real-time price calculation
    function updateTotalPrice() {
        const participants = parseInt(document.getElementById('participants').value) || 1;
        const tier = resolveTier(participants);
        const pricePerPax = tier ? parseFloat(tier.price_per_pax) : basePricePerPax;
        const subtotal = pricePerPax * participants;
        const baseSubtotal = basePricePerPax * participants;
        const discountAmount = Math.max(0, baseSubtotal - subtotal);
        const discountPercent = baseSubtotal > 0 ? Math.round((discountAmount / baseSubtotal) * 100) : 0;

        // Core price displays
        document.getElementById('display-participants').textContent = participants;
        document.getElementById('display-total-price').textContent = formatRupiah(subtotal);
        document.getElementById('summary-total-price').textContent = formatRupiah(subtotal);

        // Per-pax price display (strike-through original if tier applied)
        const paxPriceEls = document.querySelectorAll('[data-role="per-pax-price"]');
        paxPriceEls.forEach(function(el) {
            if (tier && discountAmount > 0) {
                el.innerHTML = '<span class="line-through text-gray-400 mr-1.5">' + formatRupiah(basePricePerPax) + '</span><span class="text-blue-600">' + formatRupiah(pricePerPax) + '</span>';
            } else {
                el.textContent = formatRupiah(basePricePerPax);
            }
        });

        // Discount badge — show/hide
        const badge = document.getElementById('discount-badge');
        const summaryBadge = document.getElementById('summary-discount-row');
        if (badge) {
            if (tier && discountAmount > 0) {
                badge.classList.remove('hidden');
                badge.innerHTML = 'Hemat ' + formatRupiah(discountAmount) + ' (' + discountPercent + '%) — ' + tier.name;
            } else {
                badge.classList.add('hidden');
            }
        }
        if (summaryBadge) {
            if (tier && discountAmount > 0) {
                summaryBadge.classList.remove('hidden');
                summaryBadge.querySelector('[data-role="discount-amount"]').textContent = '- ' + formatRupiah(discountAmount);
            } else {
                summaryBadge.classList.add('hidden');
            }
        }
    }

    // Format number to Rupiah
    function formatRupiah(number) {
        return 'Rp ' + Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const participantsInput = document.getElementById('participants');
        if (participantsInput) {
            participantsInput.addEventListener('input', updateTotalPrice);
            updateTotalPrice(); // Initial calculation
        }
    });
</script>
@endpush
