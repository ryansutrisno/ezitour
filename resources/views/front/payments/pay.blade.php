@extends('layouts.front')

@section('title', 'Pembayaran - EziTour')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Pembayaran</h1>
        <p class="mt-2 text-gray-600">
            @if(isset($isRetry) && $isRetry)
                Silakan coba pembayaran kembali
            @else
                Selesaikan pembayaran untuk booking Anda
            @endif
        </p>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 bg-blue-600">
            <h2 class="text-lg font-semibold text-white">Ringkasan Booking</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Order ID</span>
                    <span class="font-mono font-medium text-gray-900 text-sm">{{ $orderId }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Paket Wisata</span>
                    <span class="font-medium text-gray-900">{{ $booking->package->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Tanggal Perjalanan</span>
                    <span class="font-medium text-gray-900">{{ $booking->travel_date?->format('d M Y') ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Nama Pemesan</span>
                    <span class="font-medium text-gray-900">{{ $booking->user->name ?? 'N/A' }}</span>
                </div>
                <hr class="my-4">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-semibold text-gray-900">Total Pembayaran</span>
                    <span class="text-xl font-bold text-blue-600">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-blue-50 rounded-lg p-4 mb-8">
        <div class="flex">
            <div class="shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Informasi Pembayaran</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Klik tombol "Bayar Sekarang" untuk memilih metode pembayaran</li>
                        <li>Anda dapat membayar dengan kartu kredit, transfer bank, atau e-wallet</li>
                        <li>Pembayaran akan diproses secara aman melalui Midtrans</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <button id="pay-button" class="inline-flex items-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
            Bayar Sekarang
        </button>
        <p class="mt-4 text-sm text-gray-500">Klik tombol di atas untuk memilih metode pembayaran</p>
    </div>

    <div class="mt-8 flex items-center justify-center space-x-2 text-gray-400">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span class="text-sm">Pembayaran aman dengan Midtrans</span>
    </div>

    <div class="mt-8 text-center">
        <a href="{{ route('dashboard.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            ← Kembali ke Dashboard
        </a>
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

    // Error message helper
    function showError(title, message, suggestion) {
        const errorHtml = `
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg mb-6">
                <div class="flex">
                    <div class="shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">${title}</h3>
                        <p class="mt-1 text-sm text-red-700">${message}</p>
                        ${suggestion ? `<p class="mt-2 text-sm text-red-600">${suggestion}</p>` : ''}
                        <div class="mt-3">
                            <button onclick="window.location.reload()" class="inline-flex items-center px-3 py-1.5 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100">
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
