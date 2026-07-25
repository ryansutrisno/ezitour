@extends('layouts.front')

@section('title', 'Pembayaran - EziTour')

@section('content')
<div class="bg-slate-50 min-h-[calc(100vh-4rem)]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center w-14 h-14 rounded-pill bg-blue-100 text-blue-600 mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <h1 class="font-display text-3xl font-extrabold tracking-tight text-slate-900">Pembayaran</h1>
            <p class="mt-2 text-slate-500">
                @if(isset($isRetry) && $isRetry)
                    Silakan coba pembayaran kembali
                @else
                    Selesaikan pembayaran untuk booking Anda
                @endif
            </p>
        </div>

        {{-- Booking summary card --}}
        <div class="bg-white rounded-card border border-slate-100 shadow-card overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h2 class="font-display text-base font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Ringkasan Booking
                </h2>
            </div>
            <div class="p-6">
                <dl class="space-y-3.5 text-sm">
                    <div class="flex justify-between items-center">
                        <dt class="text-slate-500">Order ID</dt>
                        <dd class="font-mono font-semibold text-slate-900">{{ $orderId }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-slate-500">Paket Wisata</dt>
                        <dd class="font-semibold text-slate-900">{{ $booking->package->name ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-slate-500">Tanggal Perjalanan</dt>
                        <dd class="font-semibold text-slate-900">{{ $booking->travel_date?->format('d M Y') ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-slate-500">Nama Pemesan</dt>
                        <dd class="font-semibold text-slate-900">{{ $booking->user->name ?? 'N/A' }}</dd>
                    </div>
                    <div class="border-t border-slate-100 pt-3.5 mt-1">
                        <div class="flex justify-between items-center">
                            <dt class="font-display text-base font-bold text-slate-900">Total Pembayaran</dt>
                            <dd class="font-display text-2xl font-extrabold text-blue-600">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</dd>
                        </div>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Info card --}}
        <div class="bg-blue-50 border border-blue-100 rounded-card p-5 mb-8">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-blue-900">Informasi Pembayaran</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="space-y-1.5">
                            <li class="flex items-start"><svg class="w-4 h-4 mr-2 mt-0.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>Klik "Bayar Sekarang" untuk memilih metode pembayaran</li>
                            <li class="flex items-start"><svg class="w-4 h-4 mr-2 mt-0.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>Bayar dengan kartu kredit, transfer bank, atau e-wallet</li>
                            <li class="flex items-start"><svg class="w-4 h-4 mr-2 mt-0.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>Diproses secara aman melalui Midtrans</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pay action --}}
        <div class="text-center">
            <button id="pay-button" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold rounded-button shadow-hover transition-all duration-200 transform hover:scale-[1.02]">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Bayar Sekarang
            </button>
            <p class="mt-4 text-sm text-slate-500">Klik tombol di atas untuk memilih metode pembayaran</p>
        </div>

        {{-- Trust badge --}}
        <div class="mt-8 flex items-center justify-center gap-2 text-slate-400">
            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span class="text-sm">Pembayaran aman dengan Midtrans</span>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('dashboard.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 text-sm font-semibold">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const payButton = document.getElementById('pay-button');
    const snapToken = '{{ $snapToken }}';
    const orderId = '{{ $orderId }}';

    if (!payButton) return;

    // Error message helper — classes aligned with the new alert style
    function showError(title, message, suggestion) {
        const errorHtml = `
            <div class="bg-red-50 border border-red-200 rounded-card p-4 mb-6 shadow-soft">
                <div class="flex">
                    <div class="shrink-0">
                        <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-bold text-red-900">${title}</h3>
                        <p class="mt-0.5 text-sm font-medium text-red-700">${message}</p>
                        ${suggestion ? `<p class="mt-1.5 text-sm text-red-600">${suggestion}</p>` : ''}
                        <div class="mt-3">
                            <button onclick="window.location.reload()" class="inline-flex items-center px-3 py-1.5 border-2 border-red-200 text-sm font-semibold rounded-button text-red-700 bg-white hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Coba Lagi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const container = payButton.closest('.text-center');
        if (container) {
            container.insertAdjacentHTML('beforebegin', errorHtml);
        }
    }

    // Reset button state
    function resetButton() {
        payButton.disabled = false;
        payButton.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg> Bayar Sekarang';
    }

    // Set loading state
    function setLoading() {
        payButton.disabled = true;
        payButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';
    }

    payButton.addEventListener('click', function() {
        // Check if Snap.js is loaded
        if (typeof window.snap === 'undefined') {
            showError(
                'Layanan Tidak Tersedia',
                'Layanan pembayaran tidak dapat dimuat.',
                'Silakan refresh halaman atau coba lagi dalam beberapa saat.'
            );
            return;
        }

        setLoading();

        // Set timeout for Snap popup (30 seconds)
        const timeoutId = setTimeout(function() {
            resetButton();
            showError(
                'Koneksi Timeout',
                'Koneksi ke layanan pembayaran timeout.',
                'Periksa koneksi internet Anda dan coba lagi.'
            );
        }, 30000);

        try {
            window.snap.pay(snapToken, {
                onSuccess: function(result) {
                    clearTimeout(timeoutId);
                    window.location.href = '{{ route("payments.finish") }}?order_id=' + result.order_id + '&transaction_status=' + result.transaction_status;
                },
                onPending: function(result) {
                    clearTimeout(timeoutId);
                    window.location.href = '{{ route("payments.finish") }}?order_id=' + result.order_id + '&transaction_status=' + result.transaction_status;
                },
                onError: function(result) {
                    clearTimeout(timeoutId);
                    window.location.href = '{{ route("payments.error") }}?order_id=' + (result.order_id || orderId);
                },
                onClose: function() {
                    clearTimeout(timeoutId);
                    resetButton();
                    window.location.href = '{{ route("payments.unfinish") }}?order_id=' + orderId;
                }
            });
        } catch (error) {
            clearTimeout(timeoutId);
            resetButton();
            showError(
                'Terjadi Kesalahan',
                'Gagal membuka halaman pembayaran.',
                'Silakan refresh halaman dan coba lagi.'
            );
            console.error('Snap.pay error:', error);
        }
    });
});
</script>
@endpush
