<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'EziTour - Worry-Free Traveling')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Midtrans Snap JS for payment continuation --}}
    @php
        $snapUrl = config('midtrans.is_production') 
            ? 'https://app.midtrans.com/snap/snap.js' 
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
        $clientKey = config('midtrans.client_key');
    @endphp
    @if($clientKey)
        <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
    @endif
</head>
<body class="antialiased font-sans bg-gray-50 text-slate-800">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('front.home') }}" class="flex-shrink-0 flex items-center gap-2">
                        <span class="text-2xl font-bold text-blue-600">EziTour</span>
                    </a>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-8">
                    <a href="{{ route('front.home') }}" class="text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-blue-500 text-sm font-medium">
                        Home
                    </a>
                    <a href="{{ route('front.packages.index') }}" class="text-gray-500 hover:text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-blue-500 text-sm font-medium">
                        Paket Wisata
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-blue-500 text-sm font-medium">
                        Tentang Kami
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-900">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">Login</a>
                        <a href="{{ route('register') }}" class="text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-bold text-blue-600 mb-4">EziTour</h3>
                    <p class="text-gray-500 text-sm">
                        Platform perjalanan wisata tanpa ribet. Pilih paket, bayar, dan berangkat!
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Layanan</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-500 hover:text-blue-600 text-sm">Paket Wisata</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-blue-600 text-sm">Sewa Mobil</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-blue-600 text-sm">Custom Trip</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Hubungi Kami</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center text-gray-500 text-sm">
                            <span class="mr-2">📧</span> support@ezitour.com
                        </li>
                        <li class="flex items-center text-gray-500 text-sm">
                            <span class="mr-2">📞</span> +62 812 3456 7890
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-100 pt-8 text-center">
                <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} EziTour. All rights reserved.</p>
            </div>
        </div>
    </footer>

    {{-- Midtrans Snap Payment Continuation Script --}}
    <script>
        /**
         * Continue payment for pending transactions
         * Requirements: 1.4 - Handle Snap popup for payment
         * 
         * @param {string} snapToken - The Snap token from Midtrans
         * @param {string} orderId - The order ID for the transaction
         */
        function continuePayment(snapToken, orderId) {
            if (typeof window.snap === 'undefined') {
                alert('Payment service is not available. Please refresh the page and try again.');
                return;
            }

            window.snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    window.location.href = '{{ route("payments.finish") }}?' + new URLSearchParams({
                        order_id: result.order_id,
                        transaction_status: result.transaction_status
                    }).toString();
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    window.location.href = '{{ route("payments.finish") }}?' + new URLSearchParams({
                        order_id: result.order_id,
                        transaction_status: result.transaction_status
                    }).toString();
                },
                onError: function(result) {
                    console.log('Payment error:', result);
                    window.location.href = '{{ route("payments.error") }}?' + new URLSearchParams({
                        order_id: result.order_id || orderId,
                        status_code: result.status_code || '',
                        status_message: result.status_message || 'Payment failed'
                    }).toString();
                },
                onClose: function() {
                    console.log('Payment popup closed');
                    window.location.href = '{{ route("payments.unfinish") }}?' + new URLSearchParams({
                        order_id: orderId
                    }).toString();
                }
            });
        }
    </script>

    @stack('scripts')

</body>
</html>
