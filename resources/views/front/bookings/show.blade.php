@extends('layouts.front')

@section('title', __('dashboard.detail_title', ['code' => $booking->code ?? $booking->id]) . ' - EziTour')

@section('seo')
    <x-seo :title="__('dashboard.detail_breadcrumb', ['code' => $booking->code ?? $booking->id])" noindex />
@endsection

@section('content')
    @php
        $contact = app(App\Settings\ContactSettings::class);
    @endphp

    <div class="min-h-[calc(100vh-4rem)] bg-slate-50 py-8 sm:py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="mb-6 flex items-center gap-2 text-sm text-slate-500 flex-wrap">
                <a href="{{ route('front.home') }}" class="hover:text-blue-600 transition-colors">{{ __('front.nav_home') }}</a>
                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('dashboard.index') }}" class="hover:text-blue-600 transition-colors">{{ __('front.nav_dashboard') }}</a>
                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-700 font-medium">{{ __('dashboard.detail_breadcrumb', ['code' => $booking->code ?? $booking->id]) }}</span>
            </nav>

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-input flex items-start gap-2.5 text-sm">
                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-input flex items-start gap-2.5 text-sm">
                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Header gradient card --}}
            <div class="mb-6 relative overflow-hidden rounded-card bg-gradient-to-br from-blue-700 via-blue-700 to-blue-800 shadow-card">
                <div aria-hidden="true" class="pointer-events-none absolute inset-0">
                    <div class="absolute -top-16 -right-10 w-72 h-72 rounded-full bg-blue-500/30 blur-3xl"></div>
                    <div class="absolute -bottom-20 -left-10 w-72 h-72 rounded-full bg-sand-400/20 blur-3xl"></div>
                </div>
                <div class="relative p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <p class="text-blue-200 text-xs font-semibold uppercase tracking-wider">{{ __('dashboard.booking_code') }}</p>
                        <h1 class="mt-1 font-display text-2xl sm:text-3xl font-extrabold text-white tracking-tight">{{ $booking->code ?? '#'.$booking->id }}</h1>
                        <p class="mt-1 text-blue-100 text-sm">{{ $booking->package->name }}</p>
                    </div>
                    <div>
                        @php
                            $statusConfig = [
                                'pending' => [__('dashboard.status_pending'), 'bg-yellow-400 text-yellow-900'],
                                'paid' => [__('dashboard.status_paid'), 'bg-green-400 text-green-900'],
                                'cancelled' => [__('dashboard.status_cancelled'), 'bg-red-400 text-red-900'],
                                'completed' => [__('dashboard.status_completed'), 'bg-blue-400 text-white'],
                            ];
                            [$statusLabel, $statusClass] = $statusConfig[$booking->status] ?? [__('dashboard.status_unknown'), 'bg-slate-400 text-white'];
                        @endphp
                        <span class="inline-flex items-center px-4 py-2 rounded-pill text-sm font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>
                </div>
            </div>

            {{-- Main grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT: details --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Package summary --}}
                    <div class="bg-white rounded-card border border-slate-100 shadow-card p-6">
                        <h2 class="font-display text-lg font-bold text-slate-900 mb-4">{{ __('dashboard.package_section_title') }}</h2>
                        <div class="flex flex-col sm:flex-row gap-5">
                            <div class="shrink-0">
                                @if($booking->package->thumbnail_url)
                                    <img class="h-28 w-full sm:w-40 rounded-button object-cover" src="{{ $booking->package->thumbnail_url }}" alt="{{ $booking->package->name }}">
                                @else
                                    <div class="h-28 w-full sm:w-40 rounded-button bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                                        <svg class="w-10 h-10 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-display text-lg font-bold text-slate-900">{{ $booking->package->name }}</h3>
                                <p class="mt-2 text-sm text-slate-600 line-clamp-3 leading-relaxed">{{ $booking->package->description }}</p>
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                    <p class="flex items-center text-slate-600">
                                        <svg class="w-4 h-4 mr-2 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $booking->travel_date->format('d F Y') }}
                                    </p>
                                    <p class="flex items-center text-slate-600">
                                        <svg class="w-4 h-4 mr-2 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span class="truncate">{{ $booking->pickup_location }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Traveler info --}}
                    <div class="bg-white rounded-card border border-slate-100 shadow-card p-6">
                        <h2 class="font-display text-lg font-bold text-slate-900 mb-4">{{ __('dashboard.traveler_section_title') }}</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">{{ __('dashboard.traveler_name') }}</p>
                                <p class="mt-1 font-semibold text-slate-800">{{ $booking->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">{{ __('dashboard.traveler_email') }}</p>
                                <p class="mt-1 font-semibold text-slate-800 break-all">{{ $booking->user->email }}</p>
                            </div>
                            @if($booking->user->phone)
                            <div>
                                <p class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">{{ __('dashboard.traveler_phone') }}</p>
                                <p class="mt-1 font-semibold text-slate-800">{{ $booking->user->phone }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Payment history --}}
                    <div class="bg-white rounded-card border border-slate-100 shadow-card p-6">
                        <h2 class="font-display text-lg font-bold text-slate-900 mb-4">{{ __('dashboard.transactions_section_title') }}</h2>
                        @if($booking->transactions->isEmpty())
                            <p class="text-sm text-slate-500 py-4 text-center">{{ __('dashboard.transactions_empty') }}</p>
                        @else
                            <div class="space-y-3">
                                @foreach($booking->transactions as $txn)
                                    @php
                                        $txnStatusConfig = [
                                            'pending' => [__('dashboard.txn_status_pending'), 'bg-yellow-100 text-yellow-800'],
                                            'paid' => [__('dashboard.txn_status_paid'), 'bg-green-100 text-green-800'],
                                            'failed' => [__('dashboard.txn_status_failed'), 'bg-red-100 text-red-800'],
                                            'expired' => [__('dashboard.txn_status_expired'), 'bg-slate-100 text-slate-700'],
                                            'superseded' => [__('dashboard.txn_status_superseded'), 'bg-slate-100 text-slate-500'],
                                        ];
                                        [$txnLabel, $txnClass] = $txnStatusConfig[$txn->transaction_status] ?? ['-', 'bg-slate-100 text-slate-700'];
                                    @endphp
                                    <div class="flex items-center justify-between gap-3 p-3.5 rounded-button bg-slate-50 border border-slate-100">
                                        <div class="min-w-0">
                                            <p class="text-xs font-mono text-slate-500 truncate">{{ $txn->order_id }}</p>
                                            <p class="mt-0.5 text-sm text-slate-700">
                                                {{ $txn->payment_type ? ucwords(str_replace('_', ' ', $txn->payment_type)) : __('dashboard.transaction_pending_method') }}
                                                @if($txn->transaction_time)
                                                    <span class="text-slate-400">· {{ $txn->transaction_time->format('d M Y, H:i') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <p class="font-display font-bold text-slate-900 text-sm">Rp {{ number_format((float) $txn->gross_amount, 0, ',', '.') }}</p>
                                            <span class="mt-0.5 inline-block px-2 py-0.5 rounded-pill text-[11px] font-semibold {{ $txnClass }}">{{ $txnLabel }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- RIGHT: sticky summary + actions --}}
                <div class="lg:col-span-1">
                    <div class="lg:sticky lg:top-20 space-y-4">
                        {{-- Summary card --}}
                        <div class="bg-white rounded-card border border-slate-100 shadow-card overflow-hidden">
                            <div class="p-6">
                                <h2 class="font-display text-lg font-bold text-slate-900 mb-4">{{ __('dashboard.summary_section_title') }}</h2>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">{{ __('dashboard.summary_status') }}</span>
                                        <span class="font-semibold text-slate-800">{{ $statusLabel }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">{{ __('dashboard.summary_travel_date') }}</span>
                                        <span class="font-semibold text-slate-800">{{ $booking->travel_date->format('d/m/Y') }}</span>
                                    </div>
                                    @if($booking->hasDiscount() || $booking->hasCouponDiscount())
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">{{ __('dashboard.summary_subtotal') }}</span>
                                            <span class="font-semibold text-slate-600 line-through">Rp {{ number_format((float) $booking->base_subtotal, 0, ',', '.') }}</span>
                                        </div>
                                        @if($booking->hasDiscount())
                                            <div class="flex justify-between">
                                                <span class="text-green-600">{{ __('dashboard.summary_tier_discount') }}{{ $booking->applied_tier_label ? ' ('.$booking->applied_tier_label.')' : '' }}</span>
                                                <span class="font-bold text-green-600">- Rp {{ number_format((float) $booking->discount_amount, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                        @if($booking->hasCouponDiscount())
                                            <div class="flex justify-between">
                                                <span class="text-green-600">{{ __('dashboard.summary_coupon_discount') }}{{ $booking->coupon_code ? ' ('.$booking->coupon_code.')' : '' }}</span>
                                                <span class="font-bold text-green-600">- Rp {{ number_format((float) $booking->coupon_discount_amount, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                        <div class="pt-3 border-t border-slate-100 flex justify-between items-end">
                                            <span class="font-bold text-slate-900">{{ __('dashboard.summary_total') }}</span>
                                            <span class="font-display text-xl font-extrabold text-blue-600">Rp {{ number_format((float) $booking->total_amount, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        <div class="pt-3 border-t border-slate-100 flex justify-between items-end">
                                            <span class="text-slate-500">{{ __('dashboard.summary_total') }}</span>
                                            <span class="font-display text-xl font-extrabold text-blue-600">Rp {{ number_format((float) $booking->total_amount, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Action buttons (conditional) --}}
                        <div class="bg-white rounded-card border border-slate-100 shadow-card p-6">
                            @if($booking->status === 'pending')
                                @if($booking->canPay())
                                    <form method="POST" action="{{ route('payments.create', $booking) }}">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 py-3 px-4 rounded-button text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-soft transition-all mb-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            {{ __('dashboard.pay_now') }}
                                        </button>
                                    </form>
                                @endif
                                @if($booking->hasPendingPayment() && $booking->getPendingTransaction() && $booking->getPendingTransaction()->snap_token)
                                    <button type="button" onclick="continuePayment('{{ $booking->getPendingTransaction()->snap_token }}', '{{ $booking->getPendingTransaction()->order_id }}')" class="w-full inline-flex justify-center items-center gap-2 py-3 px-4 rounded-button text-sm font-bold text-white bg-yellow-500 hover:bg-yellow-600 shadow-soft transition-all mb-3">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ __('dashboard.continue_payment') }}
                                    </button>
                                @endif
                                @if($booking->canRetryPayment())
                                    <form method="POST" action="{{ route('payments.retry', $booking) }}">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 py-3 px-4 rounded-button text-sm font-bold text-white bg-red-600 hover:bg-red-700 shadow-soft transition-all mb-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            {{ __('dashboard.retry_payment_button') }}
                                        </button>
                                    </form>
                                @endif

                                {{-- Cancel (vanilla JS confirm) --}}
                                <form method="POST" action="{{ route('bookings.cancel', $booking) }}" onsubmit="return confirm('{{ __('dashboard.cancel_confirm') }}');">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex justify-center items-center gap-2 py-3 px-4 rounded-button text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        {{ __('dashboard.cancel_button') }}
                                    </button>
                                </form>
                            @elseif($booking->status === 'paid')
                                <a href="{{ route('bookings.ticket', $booking) }}" class="w-full inline-flex justify-center items-center gap-2 py-3 px-4 rounded-button text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-soft transition-all mb-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    {{ __('dashboard.download_ticket_button') }}
                                </a>
                                @if($contact->whatsapp)
                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $contact->whatsapp) }}" target="_blank" rel="noopener noreferrer" class="w-full inline-flex justify-center items-center gap-2 py-3 px-4 rounded-button text-sm font-semibold text-green-700 bg-green-50 hover:bg-green-100 border border-green-200 transition-all">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        {{ __('dashboard.contact_support_button') }}
                                    </a>
                                @endif
                            @elseif($booking->status === 'cancelled')
                                <div class="p-4 rounded-button bg-red-50 border border-red-100 text-center mb-3">
                                    <svg class="w-8 h-8 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-sm text-red-700 font-semibold">{{ __('dashboard.cancelled_notice') }}</p>
                                </div>
                                <a href="{{ route('front.packages.index') }}" class="w-full inline-flex justify-center items-center gap-2 py-3 px-4 rounded-button text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-soft transition-all">
                                    {{ __('dashboard.view_other_packages') }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @endif
                        </div>

                        <a href="{{ route('dashboard.index') }}" class="block text-center text-sm text-slate-500 hover:text-blue-600 transition-colors py-2">&larr; {{ __('front.button_back_dashboard') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
