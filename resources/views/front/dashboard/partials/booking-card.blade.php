{{-- Booking Card Component --}}
{{-- Requirements: 1.2, 6.1, 6.2, 6.3, 6.4, 6.5, 9.4 --}}

@php
    $latestTransaction = $booking->latestTransaction;
    $pendingTransaction = $booking->getPendingTransaction();
    $isPaid = $booking->isPaid();
    $hasPendingPayment = $booking->hasPendingPayment();
    $hasFailedPayment = $booking->hasFailedPayment();
    $canPay = $booking->canPay();
    $canRetry = $booking->canRetryPayment();

    // Get time remaining for pending payments
    $timeRemaining = $pendingTransaction?->getTimeRemainingFormatted();
    $packageName = $booking->package->name ?? __('front.nav_packages');
    $thumbnail = $booking->package->thumbnail_url ?? null;
@endphp

<div class="bg-white rounded-card border border-slate-100 shadow-card hover:shadow-hover transition-shadow duration-200 overflow-hidden">
    <div class="p-5 sm:p-6">
        <div class="flex items-start gap-4">
            {{-- Thumbnail (with graceful fallback for null) --}}
            <div class="shrink-0">
                @if($thumbnail)
                    <img class="h-16 w-16 sm:h-20 sm:w-20 rounded-button object-cover" src="{{ $thumbnail }}" alt="{{ $packageName }}">
                @else
                    <div class="h-16 w-16 sm:h-20 sm:w-20 rounded-button bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                @endif
            </div>

            {{-- Package Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div class="min-w-0">
                        <h4 class="font-display text-base sm:text-lg font-bold text-slate-900 truncate">{{ $packageName }}</h4>
                        <p class="mt-0.5 text-xs text-slate-400 font-mono">{{ __('dashboard.booking_id', ['id' => $booking->id]) }}</p>
                    </div>

                    {{-- Payment Status Badge --}}
                    <div class="text-left sm:text-right">
                        @if($isPaid)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-pill text-xs font-semibold bg-green-100 text-green-800">
                                <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                {{ __('dashboard.status_paid') }}
                            </span>
                            @if($booking->payment_date)
                                <p class="mt-1 text-[11px] text-slate-400">{{ __('dashboard.paid_at', ['date' => $booking->payment_date->format('d M Y, H:i')]) }}</p>
                            @endif
                        @elseif($hasPendingPayment)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-pill text-xs font-semibold bg-yellow-100 text-yellow-800">
                                <svg class="w-3.5 h-3.5 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                {{ __('dashboard.status_pending_payment') }}
                            </span>
                            @if($timeRemaining)
                                <p class="mt-1 text-[11px] text-yellow-600 font-medium">
                                    <svg class="inline-block w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ __('dashboard.time_remaining', ['time' => $timeRemaining]) }}
                                </p>
                            @endif
                        @elseif($hasFailedPayment)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-pill text-xs font-semibold bg-red-100 text-red-800">
                                <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                {{ __('dashboard.status_failed') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-pill text-xs font-semibold bg-slate-100 text-slate-700">
                                <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                {{ __('dashboard.status_unpaid') }}
                            </span>
                        @endif
                    </div>
                </div>

                <p class="mt-1.5 text-sm text-slate-500 flex items-center">
                    <svg class="inline-block w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ \Carbon\Carbon::parse($booking->travel_date)->format('d M Y') }}
                </p>
            </div>
        </div>

        {{-- Booking Details grid --}}
        <div class="mt-5 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm bg-slate-50 rounded-button p-4 border border-slate-100">
            <div>
                <span class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">{{ __('dashboard.total_label') }}</span>
                <p class="font-display font-bold text-slate-900">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
                <div class="mt-0.5 flex flex-wrap gap-1">
                    @if($booking->hasDiscount())
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-pill text-[10px] font-bold bg-green-100 text-green-700">
                            {{ __('dashboard.discount_percent', ['percent' => $booking->base_subtotal ? round(($booking->discount_amount / $booking->base_subtotal) * 100) : 0]) }}
                        </span>
                    @endif
                    @if($booking->hasCouponDiscount())
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-pill text-[10px] font-bold bg-blue-100 text-blue-700">
                            {{ __('dashboard.promo_badge') }}
                        </span>
                    @endif
                </div>
            </div>
            <div>
                <span class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">{{ __('dashboard.driver_label') }}</span>
                <p class="font-semibold text-slate-800">{{ $booking->driver->name ?? '-' }}</p>
            </div>
            <div>
                <span class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">{{ __('dashboard.car_label') }}</span>
                <p class="font-semibold text-slate-800">{{ $booking->car->name ?? '-' }}</p>
            </div>
            <div>
                <span class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">{{ __('dashboard.payment_method_label') }}</span>
                <p class="font-semibold text-slate-800">
                    @if($latestTransaction && $latestTransaction->payment_type)
                        {{ ucwords(str_replace('_', ' ', $latestTransaction->payment_type)) }}
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center">
                @if($isPaid && $latestTransaction)
                    <span class="inline-flex items-center text-xs text-slate-400 font-mono">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        {{ $latestTransaction->order_id }}
                    </span>
                @else
                    <span class="inline-flex items-center text-xs text-slate-400">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('dashboard.need_help') }}
                    </span>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Detail link (always visible) --}}
                <a href="{{ route('bookings.show', $booking) }}" class="inline-flex items-center px-4 py-2.5 bg-white border border-slate-200 hover:border-blue-300 hover:text-blue-700 text-slate-700 text-sm font-semibold rounded-button shadow-soft transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    {{ __('dashboard.view_details') }}
                </a>

                {{-- Pay Now Button (POST form: state-mutating endpoint, must not be GET) --}}
                @if($canPay && !$hasPendingPayment)
                    <form method="POST" action="{{ route('payments.create', $booking) }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-semibold rounded-button shadow-soft transition-all">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            {{ __('dashboard.pay_now') }}
                        </button>
                    </form>
                @endif

                {{-- Continue Payment Button (pending transaction with snap token) --}}
                @if($hasPendingPayment && $pendingTransaction && $pendingTransaction->snap_token)
                    <button type="button"
                            onclick="continuePayment('{{ $pendingTransaction->snap_token }}', '{{ $pendingTransaction->order_id }}')"
                            class="inline-flex items-center px-4 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-button shadow-soft transition-all">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('dashboard.continue_payment') }}
                    </button>
                @endif

                {{-- Retry Payment Button (POST form: state-mutating endpoint, must not be GET) --}}
                @if($canRetry)
                    <form method="POST" action="{{ route('payments.retry', $booking) }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-button shadow-soft transition-all">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            {{ __('dashboard.retry_button') }}
                        </button>
                    </form>
                @endif

                {{-- Completed badge (paid) --}}
                @if($isPaid)
                    <span class="inline-flex items-center px-4 py-2.5 bg-green-50 text-green-700 text-sm font-semibold rounded-button border border-green-100">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ __('dashboard.status_completed') }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
