<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('front.site_title'))</title>

    {{-- Per-page SEO meta tags (description, OG, Twitter card, canonical).
         Pages override via @section('seo'); defaults from GeneralSettings
         are rendered when the section is absent. --}}
    @hasSection('seo')
        @yield('seo')
    @else
        <x-seo />
    @endif
    
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
                           'text-slate-900 border-blue-500' => request()->routeIs('*.front.home'),
                           'text-slate-500 hover:text-slate-900 border-transparent hover:border-blue-400' => !request()->routeIs('*.front.home'),
                       ])">
                        {{ __('front.nav_home') }}
                    </a>
                    <a href="{{ route('front.packages.index') }}"
                       @class([
                           'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors',
                           'text-slate-900 border-blue-500' => request()->routeIs('*.front.packages.*'),
                           'text-slate-500 hover:text-slate-900 border-transparent hover:border-blue-400' => !request()->routeIs('*.front.packages.*'),
                       ])">
                        {{ __('front.nav_packages') }}
                    </a>
                    <a href="{{ route('front.about') }}"
                       @class([
                          'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors',
                          'text-slate-900 border-blue-500' => request()->routeIs('*.front.about'),
                          'text-slate-500 hover:text-slate-900 border-transparent hover:border-blue-400' => !request()->routeIs('*.front.about'),
                       ])">
                        {{ __('front.nav_about') }}
                    </a>
                    <a href="{{ route('front.faq') }}"
                       @class([
                          'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors',
                          'text-slate-900 border-blue-500' => request()->routeIs('*.front.faq'),
                          'text-slate-500 hover:text-slate-900 border-transparent hover:border-blue-400' => !request()->routeIs('*.front.faq'),
                       ])">
                        {{ __('front.nav_faq') }}
                    </a>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Locale toggle (ID | EN pill switcher). Persists to session
                         for guests and to users.locale for authenticated users. --}}
                    @php($currentLocale = app()->getLocale())
                    <div class="flex items-center gap-0.5 p-0.5 rounded-pill bg-slate-100 border border-slate-200" role="group" aria-label="{{ __('front.locale_label') }}">
                        <a href="{{ route('front.locale.switch', ['locale' => 'id']) }}"
                           @class([
                               'inline-flex items-center justify-center min-w-[28px] h-6 px-2 rounded-pill text-[11px] font-bold tracking-wide transition-colors',
                               'bg-blue-600 text-white shadow-soft' => $currentLocale === 'id',
                               'text-slate-500 hover:text-slate-800' => $currentLocale !== 'id',
                           ])
                           aria-pressed="{{ $currentLocale === 'id' ? 'true' : 'false' }}">
                            ID
                        </a>
                        <a href="{{ route('front.locale.switch', ['locale' => 'en']) }}"
                           @class([
                               'inline-flex items-center justify-center min-w-[28px] h-6 px-2 rounded-pill text-[11px] font-bold tracking-wide transition-colors',
                               'bg-blue-600 text-white shadow-soft' => $currentLocale === 'en',
                               'text-slate-500 hover:text-slate-800' => $currentLocale !== 'en',
                           ])
                           aria-pressed="{{ $currentLocale === 'en' ? 'true' : 'false' }}">
                            EN
                        </a>
                    </div>
                    @auth
                        @php($user = auth()->user())
                        <div class="relative" id="user-dropdown">
                            <button type="button" onclick="toggleUserMenu()"
                                class="flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors cursor-pointer"
                                aria-haspopup="true" aria-expanded="false" id="user-menu-button">
                                @if($user->avatar_url)
                                    <img class="w-8 h-8 rounded-full object-cover ring-2 ring-blue-100"
                                         src="{{ str_starts_with($user->avatar_url, 'http') ? $user->avatar_url : \Illuminate\Support\Facades\Storage::url($user->avatar_url) }}"
                                         alt="{{ $user->name }}">
                                @else
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white font-display text-xs font-bold shadow-soft">
                                        {{ $user->initials }}
                                    </span>
                                @endif
                                <svg id="user-menu-chevron" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div id="user-dropdown-menu"
                                 class="hidden absolute right-0 mt-2.5 w-52 bg-white rounded-card shadow-hover border border-slate-100 overflow-hidden z-50">
                                <div class="px-4 py-3 border-b border-slate-50">
                                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-400 truncate mt-0.5">{{ $user->email }}</p>
                                </div>
                                <div class="py-1">
                                    <a href="{{ route('dashboard.index') }}"
                                       class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                        {{ __('front.nav_dashboard') }}
                                    </a>
                                    <a href="{{ route('front.wishlist.index') }}"
                                       class="flex items-center justify-between px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                        <span class="flex items-center gap-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                            {{ __('front.nav_wishlist') }}
                                        </span>
                                        @php($favCount = $user->favorites()->count())
                                        @if($favCount > 0)
                                            <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold rounded-pill bg-red-500 text-white">{{ $favCount }}</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('front.profile.edit') }}"
                                       class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        {{ __('front.nav_profile') }}
                                    </a>
                                </div>
                                <div class="border-t border-slate-100">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            {{ __('front.nav_logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">{{ __('front.nav_login') }}</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 px-4 py-2 rounded-button shadow-soft transition-all">{{ __('front.nav_register') }}</a>
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
    @php($general = app(App\Settings\GeneralSettings::class))
    @php($contact = app(App\Settings\ContactSettings::class))
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
                        {{ app()->getLocale() === 'en' && $general->footerTagline_en ? $general->footerTagline_en : $general->footerTagline }}
                    </p>
                    <div class="flex gap-3 mt-5">
                        @if($contact->instagramUrl)
                            <a href="{{ $contact->instagramUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="flex items-center justify-center w-9 h-9 rounded-button bg-white/5 border border-white/10 text-white/60 hover:text-white hover:bg-blue-600 hover:border-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163C8.741 0 8.332.014 7.052.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            </a>
                        @endif
                        @if($contact->facebookUrl)
                            <a href="{{ $contact->facebookUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="flex items-center justify-center w-9 h-9 rounded-button bg-white/5 border border-white/10 text-white/60 hover:text-white hover:bg-blue-600 hover:border-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                        @endif
                        @if($contact->twitterUrl)
                            <a href="{{ $contact->twitterUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Twitter / X" class="flex items-center justify-center w-9 h-9 rounded-button bg-white/5 border border-white/10 text-white/60 hover:text-white hover:bg-blue-600 hover:border-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ __('front.footer_services') }}</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('front.packages.index') }}" class="text-slate-400 hover:text-blue-400 text-sm transition-colors">{{ __('front.nav_packages') }}</a></li>
                        <li><a href="{{ route('front.about') }}" class="text-slate-400 hover:text-blue-400 text-sm transition-colors">{{ __('front.nav_about') }}</a></li>
                        <li><a href="{{ route('front.faq') }}" class="text-slate-400 hover:text-blue-400 text-sm transition-colors">{{ __('front.nav_faq') }}</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-blue-400 text-sm transition-colors">{{ __('front.footer_car_rental') }}</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-blue-400 text-sm transition-colors">{{ __('front.footer_custom_trip') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">{{ __('front.footer_contact') }}</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center text-slate-400 text-sm">
                            <svg class="w-4 h-4 mr-2.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> {{ $contact->email }}
                        </li>
                        <li class="flex items-center text-slate-400 text-sm">
                            <svg class="w-4 h-4 mr-2.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> {{ $contact->phone }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} {{ __('front.footer_copyright') }}</p>
                <p class="text-slate-500 text-sm">{{ __('front.footer_tagline_brand') }}</p>
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

    {{-- User dropdown menu toggle --}}
    <script>
        function toggleUserMenu() {
            const menu = document.getElementById('user-dropdown-menu');
            const chevron = document.getElementById('user-menu-chevron');
            const button = document.getElementById('user-menu-button');
            const isOpen = !menu.classList.contains('hidden');
            menu.classList.toggle('hidden', isOpen);
            if (chevron) chevron.classList.toggle('rotate-180', !isOpen);
            button.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('user-dropdown');
            if (dropdown && !dropdown.contains(e.target)) {
                const menu = document.getElementById('user-dropdown-menu');
                const chevron = document.getElementById('user-menu-chevron');
                const button = document.getElementById('user-menu-button');
                if (!menu.classList.contains('hidden')) {
                    menu.classList.add('hidden');
                    if (chevron) chevron.classList.remove('rotate-180');
                    button.setAttribute('aria-expanded', 'false');
                }
            }
        });
    </script>

    @stack('scripts')

</body>
</html>
