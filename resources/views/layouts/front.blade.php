<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'EziTour - Worry-Free Traveling')</title>
    
    <!-- Fonts: Instrument Sans (body) + Plus Jakarta Sans (display) via Bunny Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|plus-jakarta-sans:600,700,800" rel="stylesheet" />

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
<body class="antialiased font-sans bg-slate-50 text-slate-800">

    <!-- Navbar -->
    <nav class="bg-white/90 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('front.home') }}" class="shrink-0 flex items-center gap-2.5 group">
                        <span class="flex items-center justify-center w-9 h-9 rounded-button bg-gradient-to-br from-blue-600 to-blue-700 shadow-soft transition-transform group-hover:scale-105">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </span>
                        <span class="text-xl font-bold font-display text-slate-900 tracking-tight">Ezi<span class="text-blue-600">Tour</span></span>
                    </a>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center sm:gap-8">
                    <a href="{{ route('front.home') }}"
                       @class([
                           'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors',
                           'text-slate-900 border-blue-500' => request()->routeIs('front.home'),
                           'text-slate-500 hover:text-slate-900 border-transparent hover:border-blue-400' => !request()->routeIs('front.home'),
                       ])">
                        Beranda
                    </a>
                    <a href="{{ route('front.packages.index') }}"
                       @class([
                           'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors',
                           'text-slate-900 border-blue-500' => request()->routeIs('front.packages.*'),
                           'text-slate-500 hover:text-slate-900 border-transparent hover:border-blue-400' => !request()->routeIs('front.packages.*'),
                       ])">
                        Paket Wisata
                    </a>
                    <a href="{{ route('front.about') }}"
                       @class([
                           'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors',
                           'text-slate-900 border-blue-500' => request()->routeIs('front.about'),
                           'text-slate-500 hover:text-slate-900 border-transparent hover:border-blue-400' => !request()->routeIs('front.about'),
                       ])">
                        Tentang Kami
                    </a>
                </div>
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-button shadow-soft transition-colors">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">Login</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 px-4 py-2 rounded-button shadow-soft transition-all">Daftar</a>
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
    <footer class="bg-slate-900 text-slate-300 mt-16">
        <div class="max-w-7xl mx-auto py-14 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2.5 mb-4">
                        <span class="flex items-center justify-center w-9 h-9 rounded-button bg-gradient-to-br from-blue-500 to-blue-700">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </span>
                        <span class="text-xl font-bold font-display text-white tracking-tight">Ezi<span class="text-blue-400">Tour</span></span>
                    </div>
                    <p class="text-slate-400 text-sm max-w-md leading-relaxed">
                        Platform perjalanan wisata tanpa ribet. Pilih paket, bayar, dan berangkat! Kami urus transportasi, supir, dan tiket masuk — kamu tinggal duduk manis.
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Layanan</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('front.packages.index') }}" class="text-slate-400 hover:text-blue-400 text-sm transition-colors">Paket Wisata</a></li>
                        <li><a href="{{ route('front.about') }}" class="text-slate-400 hover:text-blue-400 text-sm transition-colors">Tentang Kami</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-blue-400 text-sm transition-colors">Sewa Mobil</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-blue-400 text-sm transition-colors">Custom Trip</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Hubungi Kami</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center text-slate-400 text-sm">
                            <svg class="w-4 h-4 mr-2.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> support@ezitour.com
                        </li>
                        <li class="flex items-center text-slate-400 text-sm">
                            <svg class="w-4 h-4 mr-2.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> +62 812 3456 7890
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} EziTour. All rights reserved.</p>
                <p class="text-slate-500 text-sm">Dibuat dengan rasa di Indonesia.</p>
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
