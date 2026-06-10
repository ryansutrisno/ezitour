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
@endphp

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-6">
        <div class="flex items-start justify-between">
            {{-- Package Info --}}
            <div class="flex items-start space-x-4">
                <div class="shrink-0">
                    <img class="h-16 w-16 rounded-lg object-cover" 
                         src="{{ $booking->package->thumbnail_url ?? '/images/placeholder.jpg' }}" 
                         alt="{{ $booking->package->name ?? 'Package' }}">
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900">
                        {{ $booking->package->name ?? 'Paket Wisata' }}
                    </h4>
                    <p class="text-sm text-gray-500">
                        ID Booking: #{{ $booking->id }}
                    </p>
                    <p class="text-sm text-gray-500">
                        <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ \Carbon\Carbon::parse($booking->travel_date)->format('d M Y') }}
                    </p>
                </div>
            </div>

            {{-- Payment Status Badge --}}
            <div class="text-right">
                @if($isPaid)
                    {{-- Paid Badge - Requirements: 6.3 --}}
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Lunas
                    </span>
                    @if($booking->payment_date)
                        <p class="mt-1 text-xs text-gray-500">
                            Dibayar: {{ $booking->payment_date->format('d M Y H:i') }}
                        </p>
                    @endif
                @elseif($hasPendingPayment)
                    {{-- Pending Payment Badge --}}
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <svg class="w-4 h-4 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        Menunggu Pembayaran
                    </span>
                    {{-- Time Remaining - Requirements: 9.4 --}}
                    @if($timeRemaining)
                        <p class="mt-1 text-xs text-yellow-600">
                            <svg class="inline-block w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Sisa waktu: {{ $timeRemaining }}
                        </p>
                    @endif
                @elseif($hasFailedPayment)
                    {{-- Failed Payment Badge - Requirements: 6.4 --}}
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        Pembayaran Gagal
                    </span>
                @else
                    {{-- Pending Status Badge --}}
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        Belum Dibayar
                    </span>
                @endif
            </div>
        </div>

        {{-- Booking Details --}}
        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Total Harga</span>
                <p class="font-semibold text-gray-900">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
            </div>
            <div>
                <span class="text-gray-500">Driver</span>
                <p class="font-medium text-gray-900">
                    {{ $booking->driver->name ?? '-' }}
                </p>
            </div>
            <div>
                <span class="text-gray-500">Mobil</span>
                <p class="font-medium text-gray-900">
                    {{ $booking->car->name ?? '-' }}
                </p>
            </div>
            {{-- Payment Method Display - Requirements: 6.5 --}}
            <div>
                <span class="text-gray-500">Metode Pembayaran</span>
                <p class="font-medium text-gray-900">
                    @if($latestTransaction && $latestTransaction->payment_type)
                        {{ ucwords(str_replace('_', ' ', $latestTransaction->payment_type)) }}
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                {{-- Status Info --}}
                @if($isPaid && $latestTransaction)
                    <span class="text-xs text-gray-500">
                        Order ID: {{ $latestTransaction->order_id }}
                    </span>
                @endif
            </div>

            <div class="flex items-center space-x-3">
                {{-- Pay Now Button - Requirements: 1.2, 6.2 --}}
                @if($canPay && !$hasPendingPayment)
                    <a href="{{ route('payments.create', $booking) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Bayar Sekarang
                    </a>
                @endif

                {{-- Continue Payment Button (for pending transactions with snap token) --}}
                @if($hasPendingPayment && $pendingTransaction && $pendingTransaction->snap_token)
                    <button type="button" 
                            onclick="continuePayment('{{ $pendingTransaction->snap_token }}', '{{ $pendingTransaction->order_id }}')"
                            class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-md shadow-sm transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Lanjutkan Pembayaran
                    </button>
                @endif

                {{-- Retry Payment Button - Requirements: 6.4 --}}
                @if($canRetry)
                    <a href="{{ route('payments.retry', $booking) }}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md shadow-sm transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Coba Lagi
                    </a>
                @endif

                {{-- View Details (for paid bookings) --}}
                @if($isPaid)
                    <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 text-sm font-medium rounded-md">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Pembayaran Selesai
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
