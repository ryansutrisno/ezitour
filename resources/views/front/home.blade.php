@extends('layouts.front')

@section('title', __('front.site_title'))

@section('seo')
    <x-seo
        :title="__('front.site_title')"
        :description="app(App\Settings\HomeSettings::class)->heroSubheadline"
        type="website"
    />
@endsection

@section('content')
    @php($home = app(App\Settings\HomeSettings::class))

    {{-- ============================================================
    (a) HERO — gradient mesh + inline SVG travel scene (no external images)
    ============================================================ --}}
    <section class="relative overflow-hidden bg-slate-50">
        {{-- Decorative gradient mesh background --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="absolute -top-24 -right-24 w-[40rem] h-[40rem] rounded-full bg-blue-200/40 blur-3xl"></div>
            <div class="absolute top-40 -left-32 w-[34rem] h-[34rem] rounded-full bg-sand-200/40 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgb(15_23_42/0.05)_1px,transparent_0)] [background-size:22px_22px]"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-28">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                {{-- Left: copy + search + CTA --}}
                <div class="text-center lg:text-left">
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-pill bg-white shadow-soft text-blue-700 text-xs font-semibold border border-blue-100">
                        <span class="flex h-2 w-2 rounded-full bg-sand-500"></span>
                        {{ app()->getLocale() === 'en' && $home->heroBadge_en ? $home->heroBadge_en : $home->heroBadge }}
                    </span>

                    <h1 class="mt-5 font-display font-extrabold tracking-tight text-slate-900 text-4xl sm:text-5xl lg:text-6xl leading-[1.05]">
                        {{ app()->getLocale() === 'en' && $home->heroHeadline_en ? $home->heroHeadline_en : $home->heroHeadline }}
                        <span class="block bg-gradient-to-r from-blue-600 to-blue-500 bg-clip-text text-transparent">{{ app()->getLocale() === 'en' && $home->heroHeadlineAccent_en ? $home->heroHeadlineAccent_en : $home->heroHeadlineAccent }}</span>
                    </h1>

                    <p class="mt-5 text-base sm:text-lg text-slate-600 sm:max-w-xl lg:max-w-lg mx-auto lg:mx-0 leading-relaxed">
                        {{ app()->getLocale() === 'en' && $home->heroSubheadline_en ? $home->heroSubheadline_en : $home->heroSubheadline }}
                    </p>

                    {{-- Search box --}}
                    <form action="{{ route('front.packages.index') }}" method="GET" class="mt-8 max-w-lg mx-auto lg:mx-0">
                        <div class="flex flex-col sm:flex-row gap-2.5 p-2 bg-white rounded-card border-2 border-slate-100 shadow-card">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" name="keyword" class="block w-full pl-11 pr-3 py-3 rounded-input border-0 bg-transparent focus:outline-none focus:ring-0 sm:text-sm text-slate-800 placeholder-slate-400" placeholder="{{ __('front.hero_search_placeholder') }}">
                            </div>
                            <button type="submit" class="inline-flex justify-center items-center gap-2 px-6 py-3 rounded-button text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-soft transition-all">
                                {{ __('front.hero_search_button') }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </form>

                    {{-- Trust row --}}
                    <div class="mt-7 flex items-center justify-center lg:justify-start gap-5 text-xs text-slate-500">
                        <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> {{ __('front.hero_trust_secure_payment') }}</span>
                        <span class="hidden sm:inline-flex items-center gap-1.5"><svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> {{ __('front.hero_trust_verified_partners') }}</span>
                        <span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg> {{ __('front.hero_trust_instant_confirmation') }}</span>
                    </div>
                </div>

                {{-- Right: inline SVG travel scene --}}
                <div class="relative">
                    <div class="relative aspect-[4/3] rounded-card bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 shadow-hover overflow-hidden">
                        {{-- glow + sun --}}
                        <div aria-hidden="true" class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-sand-300/40 blur-2xl"></div>
                        <div aria-hidden="true" class="absolute top-8 right-10 w-20 h-20 rounded-full bg-sand-300/80 blur-[2px]"></div>

                        <svg class="absolute inset-0 w-full h-full" viewBox="0 0 400 300" fill="none" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                            {{-- sky clouds --}}
                            <path d="M40 60 Q40 46 54 46 Q60 34 76 38 Q92 34 94 50 Q108 50 108 62 Q108 74 94 74 L48 74 Q40 74 40 62 Z" fill="#ffffff" opacity="0.18"/>
                            <path d="M270 50 Q270 40 282 40 Q286 32 298 34 Q310 32 312 44 Q322 44 322 52 Q322 62 312 62 L276 62 Q270 62 270 52 Z" fill="#ffffff" opacity="0.14"/>
                            {{-- dashed travel route --}}
                            <path d="M20 250 Q120 160 200 200 T388 120" stroke="#ffffff" stroke-width="2.5" stroke-dasharray="6 8" stroke-linecap="round" opacity="0.55" fill="none"/>
                            {{-- sun glow circle --}}
                            <circle cx="320" cy="70" r="34" fill="#fcd34d" opacity="0.85"/>
                            <circle cx="320" cy="70" r="34" fill="url(#sunGlow)" opacity="0.6"/>
                            {{-- back mountains --}}
                            <path d="M0 230 L70 150 L130 210 L180 160 L260 230 Z" fill="#1e3a8a" opacity="0.55"/>
                            <path d="M120 230 L210 130 L300 230 Z" fill="#1e40af" opacity="0.7"/>
                            <path d="M220 230 L300 170 L400 240 L400 300 L0 300 L0 230 Z" fill="#0f172a" opacity="0.35"/>
                            {{-- water reflection lines --}}
                            <path d="M0 260 Q60 256 120 260 T240 260 T400 260" stroke="#bfdbfe" stroke-width="2" opacity="0.4" fill="none"/>
                            <path d="M0 276 Q70 272 140 276 T280 276 T400 276" stroke="#bfdbfe" stroke-width="2" opacity="0.3" fill="none"/>
                            <defs>
                                <radialGradient id="sunGlow" cx="50%" cy="50%" r="50%">
                                    <stop offset="0%" stop-color="#fde68a" stop-opacity="0.9"/>
                                    <stop offset="100%" stop-color="#fde68a" stop-opacity="0"/>
                                </radialGradient>
                            </defs>
                        </svg>

                        {{-- Floating route pins --}}
                        <div class="absolute top-[42%] left-[8%] flex items-center gap-1.5 px-2.5 py-1 rounded-pill bg-white/95 shadow-card text-[11px] font-semibold text-slate-700 backdrop-blur">
                            <span class="flex h-2 w-2 rounded-full bg-sand-500"></span> Yogyakarta
                        </div>
                        <div class="absolute top-[20%] right-[12%] flex items-center gap-1.5 px-2.5 py-1 rounded-pill bg-white/95 shadow-card text-[11px] font-semibold text-slate-700 backdrop-blur">
                            <span class="flex h-2 w-2 rounded-full bg-blue-500"></span> Bali
                        </div>
                    </div>

                    {{-- Floating rating chip --}}
                    <div class="absolute -bottom-5 -left-3 sm:-left-6 flex items-center gap-3 bg-white rounded-card shadow-hover border border-slate-100 px-4 py-3">
                        <div class="flex -space-x-2">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white text-[11px] font-bold border-2 border-white">AR</span>
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-sand-500 text-white text-[11px] font-bold border-2 border-white">SN</span>
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-700 text-white text-[11px] font-bold border-2 border-white">DK</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-0.5 text-sand-500">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.964a1 1 0 00.95.69h4.165c.969 0 1.371 1.24.588 1.81l-3.37 2.45a1 1 0 00-.364 1.118l1.287 3.964c.3.922-.755 1.688-1.539 1.118l-3.37-2.45a1 1 0 00-1.176 0l-3.37 2.45c-.784.57-1.838-.196-1.539-1.118l1.287-3.964a1 1 0 00-.364-1.118l-3.37-2.45c-.783-.57-.38-1.81.588-1.81h4.166a1 1 0 00.95-.69l1.286-3.964z"/></svg>
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.964a1 1 0 00.95.69h4.165c.969 0 1.371 1.24.588 1.81l-3.37 2.45a1 1 0 00-.364 1.118l1.287 3.964c.3.922-.755 1.688-1.539 1.118l-3.37-2.45a1 1 0 00-1.176 0l-3.37 2.45c-.784.57-1.838-.196-1.539-1.118l1.287-3.964a1 1 0 00-.364-1.118l-3.37-2.45c-.783-.57-.38-1.81.588-1.81h4.166a1 1 0 00.95-.69l1.286-3.964z"/></svg>
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.964a1 1 0 00.95.69h4.165c.969 0 1.371 1.24.588 1.81l-3.37 2.45a1 1 0 00-.364 1.118l1.287 3.964c.3.922-.755 1.688-1.539 1.118l-3.37-2.45a1 1 0 00-1.176 0l-3.37 2.45c-.784.57-1.838-.196-1.539-1.118l1.287-3.964a1 1 0 00-.364-1.118l-3.37-2.45c-.783-.57-.38-1.81.588-1.81h4.166a1 1 0 00.95-.69l1.286-3.964z"/></svg>
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.964a1 1 0 00.95.69h4.165c.969 0 1.371 1.24.588 1.81l-3.37 2.45a1 1 0 00-.364 1.118l1.287 3.964c.3.922-.755 1.688-1.539 1.118l-3.37-2.45a1 1 0 00-1.176 0l-3.37 2.45c-.784.57-1.838-.196-1.539-1.118l1.287-3.964a1 1 0 00-.364-1.118l-3.37-2.45c-.783-.57-.38-1.81.588-1.81h4.166a1 1 0 00.95-.69l1.286-3.964z"/></svg>
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.964a1 1 0 00.95.69h4.165c.969 0 1.371 1.24.588 1.81l-3.37 2.45a1 1 0 00-.364 1.118l1.287 3.964c.3.922-.755 1.688-1.539 1.118l-3.37-2.45a1 1 0 00-1.176 0l-3.37 2.45c-.784.57-1.838-.196-1.539-1.118l1.287-3.964a1 1 0 00-.364-1.118l-3.37-2.45c-.783-.57-.38-1.81.588-1.81h4.166a1 1 0 00.95-.69l1.286-3.964z"/></svg>
                            </div>
                            <p class="text-[11px] font-semibold text-slate-900">{{ __('front.hero_travelers_count') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
    (b) STATS BAR — trust numbers
    ============================================================ --}}
    <section class="relative -mt-px bg-gradient-to-r from-blue-700 via-blue-700 to-blue-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <dl class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <dt class="sr-only">{{ __('front.hero_stat_destinations') }}</dt>
                    <dd class="font-display text-3xl sm:text-4xl font-extrabold text-white">{{ $home->statDestinations }}</dd>
                    <p class="mt-1 text-sm text-blue-200">{{ __('front.hero_stat_destinations') }}</p>
                </div>
                <div class="text-center">
                    <dt class="sr-only">{{ __('front.hero_stat_travelers') }}</dt>
                    <dd class="font-display text-3xl sm:text-4xl font-extrabold text-white">{{ $home->statTravelers }}</dd>
                    <p class="mt-1 text-sm text-blue-200">{{ __('front.hero_stat_travelers') }}</p>
                </div>
                <div class="text-center">
                    <dt class="sr-only">{{ __('front.hero_stat_rating') }}</dt>
                    <dd class="font-display text-3xl sm:text-4xl font-extrabold text-sand-300">{{ $home->statRating }}</dd>
                    <p class="mt-1 text-sm text-blue-200">{{ __('front.hero_stat_rating') }}</p>
                </div>
                <div class="text-center">
                    <dt class="sr-only">{{ __('front.hero_stat_support') }}</dt>
                    <dd class="font-display text-3xl sm:text-4xl font-extrabold text-white">{{ $home->statSupport }}</dd>
                    <p class="mt-1 text-sm text-blue-200">{{ __('front.hero_stat_support') }}</p>
                </div>
            </dl>
        </div>
    </section>

    {{-- ============================================================
    (c) USP — Kenapa EziTour
    ============================================================ --}}
    <section id="why-ezitour" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center">
                <span class="text-blue-600 font-semibold text-sm tracking-wide uppercase">{{ __('front.hero_usp_eyebrow') }}</span>
                <h2 class="mt-2 font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">{{ __('front.hero_usp_title') }}</h2>
                <p class="mt-4 text-slate-600 leading-relaxed">{{ __('front.hero_usp_intro') }}</p>
            </div>

            <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Feature 1 --}}
                <div class="group rounded-card border border-slate-100 bg-white p-6 shadow-soft hover:shadow-hover hover:-translate-y-1 transition-all duration-200">
                    <div class="flex items-center justify-center w-12 h-12 rounded-button bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h12M3 12h6"/></svg>
                    </div>
                    <h3 class="mt-5 font-display text-lg font-bold text-slate-900">{{ __('front.hero_usp_easy_booking_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ __('front.hero_usp_easy_booking_body') }}</p>
                </div>
                {{-- Feature 2 --}}
                <div class="group rounded-card border border-slate-100 bg-white p-6 shadow-soft hover:shadow-hover hover:-translate-y-1 transition-all duration-200">
                    <div class="flex items-center justify-center w-12 h-12 rounded-button bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="mt-5 font-display text-lg font-bold text-slate-900">{{ __('front.hero_usp_verified_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ __('front.hero_usp_verified_body') }}</p>
                </div>
                {{-- Feature 3 --}}
                <div class="group rounded-card border border-slate-100 bg-white p-6 shadow-soft hover:shadow-hover hover:-translate-y-1 transition-all duration-200">
                    <div class="flex items-center justify-center w-12 h-12 rounded-button bg-sand-100 text-sand-600 group-hover:bg-sand-500 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="mt-5 font-display text-lg font-bold text-slate-900">{{ __('front.hero_usp_best_price_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ __('front.hero_usp_best_price_body') }}</p>
                </div>
                {{-- Feature 4 --}}
                <div class="group rounded-card border border-slate-100 bg-white p-6 shadow-soft hover:shadow-hover hover:-translate-y-1 transition-all duration-200">
                    <div class="flex items-center justify-center w-12 h-12 rounded-button bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="mt-5 font-display text-lg font-bold text-slate-900">{{ __('front.hero_usp_support_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ __('front.hero_usp_support_body') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
    (d) FEATURED PACKAGES — polished cards
    ============================================================ --}}
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <span class="text-blue-600 font-semibold text-sm tracking-wide uppercase">{{ __('front.hero_popular_eyebrow') }}</span>
                    <h2 class="mt-2 font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">{{ __('front.hero_popular_title') }}</h2>
                    <p class="mt-3 text-slate-600 max-w-2xl">{{ __('front.hero_popular_intro') }}</p>
                </div>
                <a href="{{ route('front.packages.index') }}" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-700 group">
                    {{ __('front.hero_popular_view_all') }}
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-7 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($packages as $package)
                    <a href="{{ route('front.packages.show', $package->slug) }}" class="group flex flex-col rounded-card bg-white border border-slate-100 shadow-card hover:shadow-hover overflow-hidden transition-all duration-200 hover:-translate-y-1">
                        <div class="relative h-52 overflow-hidden">
                            @if($package->thumbnail_url)
                                <img class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" src="{{ $package->thumbnail_url }}" alt="{{ $package->name }}">
                            @else
                                <div class="h-full w-full bg-gradient-to-br from-blue-500 via-blue-600 to-blue-800 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 via-transparent to-transparent"></div>
                            <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-pill bg-white/95 backdrop-blur text-[11px] font-bold text-slate-700 shadow-soft">
                                <svg class="w-3.5 h-3.5 text-sand-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.964a1 1 0 00.95.69h4.165c.969 0 1.371 1.24.588 1.81l-3.37 2.45a1 1 0 00-.364 1.118l1.287 3.964c.3.922-.755 1.688-1.539 1.118l-3.37-2.45a1 1 0 00-1.176 0l-3.37 2.45c-.784.57-1.838-.196-1.539-1.118l1.287-3.964a1 1 0 00-.364-1.118l-3.37-2.45c-.783-.57-.38-1.81.588-1.81h4.166a1 1 0 00.95-.69l1.286-3.964z"/></svg>
                                4.9
                            </span>
                            <span class="absolute top-3 right-3 inline-flex items-center px-2.5 py-1 rounded-pill bg-blue-600/95 backdrop-blur text-[11px] font-semibold text-white shadow-soft">{{ __('front.hero_popular_badge') }}</span>
                        </div>
                        <div class="flex flex-1 flex-col p-6">
                            <h3 class="font-display text-lg font-bold text-slate-900 group-hover:text-blue-700 transition-colors">{{ $package->name }}</h3>
                            <p class="mt-2 text-sm text-slate-600 line-clamp-3 leading-relaxed">{{ $package->description }}</p>
                            <div class="mt-5 pt-5 border-t border-slate-100 flex items-end justify-between gap-3">
                                <div>
                                    <p class="text-[11px] text-slate-400 font-medium">{{ __('front.hero_price_from') }}</p>
                                    <p class="font-display text-xl font-extrabold text-blue-600">Rp {{ number_format($package->total_price, 0, ',', '.') }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1 text-sm font-semibold text-white bg-blue-600 group-hover:bg-blue-700 px-3.5 py-2 rounded-button shadow-soft transition-colors">
                                    {{ __('front.hero_price_detail') }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10 text-center sm:hidden">
                <a href="{{ route('front.packages.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-button text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 shadow-soft">{{ __('front.hero_popular_mobile_cta') }}</a>
            </div>
        </div>
    </section>

    {{-- ============================================================
    (e) HOW IT WORKS — 3 steps
    ============================================================ --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center">
                <span class="text-blue-600 font-semibold text-sm tracking-wide uppercase">{{ __('front.hero_how_eyebrow') }}</span>
                <h2 class="mt-2 font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">{{ __('front.hero_how_title') }}</h2>
                <p class="mt-4 text-slate-600 leading-relaxed">{{ __('front.hero_how_intro') }}</p>
            </div>

            <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                {{-- connecting line (desktop) --}}
                <div aria-hidden="true" class="hidden md:block absolute top-7 left-[16%] right-[16%] h-0.5 border-t-2 border-dashed border-blue-200"></div>

                <div class="relative text-center">
                    <div class="relative mx-auto flex items-center justify-center w-14 h-14 rounded-pill bg-white border-2 border-blue-200 shadow-card">
                        <span class="font-display text-xl font-extrabold text-blue-600">1</span>
                    </div>
                    <h3 class="mt-5 font-display text-lg font-bold text-slate-900">{{ __('front.hero_how_step1_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed max-w-xs mx-auto">{{ __('front.hero_how_step1_body') }}</p>
                </div>
                <div class="relative text-center">
                    <div class="relative mx-auto flex items-center justify-center w-14 h-14 rounded-pill bg-white border-2 border-blue-200 shadow-card">
                        <span class="font-display text-xl font-extrabold text-blue-600">2</span>
                    </div>
                    <h3 class="mt-5 font-display text-lg font-bold text-slate-900">{{ __('front.hero_how_step2_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed max-w-xs mx-auto">{{ __('front.hero_how_step2_body') }}</p>
                </div>
                <div class="relative text-center">
                    <div class="relative mx-auto flex items-center justify-center w-14 h-14 rounded-pill bg-blue-600 border-2 border-blue-600 shadow-card">
                        <span class="font-display text-xl font-extrabold text-white">3</span>
                    </div>
                    <h3 class="mt-5 font-display text-lg font-bold text-slate-900">{{ __('front.hero_how_step3_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed max-w-xs mx-auto">{{ __('front.hero_how_step3_body') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
    (f) TESTIMONIALS
    ============================================================ --}}
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center">
                <span class="text-blue-600 font-semibold text-sm tracking-wide uppercase">{{ __('front.hero_testimonials_eyebrow') }}</span>
                <h2 class="mt-2 font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">{{ __('front.hero_testimonials_title') }}</h2>
                <p class="mt-4 text-slate-600 leading-relaxed">{{ __('front.hero_testimonials_intro') }}</p>
            </div>

            @php($avatarStyles = ['bg-blue-100 text-blue-700', 'bg-sand-200 text-sand-700', 'bg-blue-700 text-white'])

            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($testimonials as $testimonial)
                    @php($initials = strtoupper(implode('', array_map(fn ($w) => mb_substr($w, 0, 1), array_slice(explode(' ', $testimonial->name), 0, 2)))))
                    <figure class="relative rounded-card bg-white border border-slate-100 shadow-card p-7">
                        <svg class="absolute top-6 right-6 w-10 h-10 text-blue-100" fill="currentColor" viewBox="0 0 24 24"><path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z"/></svg>
                        <div class="flex items-center gap-0.5 text-sand-500">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.964a1 1 0 00.95.69h4.165c.969 0 1.371 1.24.588 1.81l-3.37 2.45a1 1 0 00-.364 1.118l1.287 3.964c.3.922-.755 1.688-1.539 1.118l-3.37-2.45a1 1 0 00-1.176 0l-3.37 2.45c-.784.57-1.838-.196-1.539-1.118l1.287-3.964a1 1 0 00-.364-1.118l-3.37-2.45c-.783-.57-.38-1.81.588-1.81h4.166a1 1 0 00.95-.69l1.286-3.964z" fill-opacity="{{ $i <= $testimonial->rating ? '1' : '0.25' }}"/></svg>
                            @endfor
                        </div>
                        <blockquote class="mt-4 text-slate-700 leading-relaxed">"{{ $testimonial->quote }}"</blockquote>
                        <figcaption class="mt-6 flex items-center gap-3 pt-5 border-t border-slate-100">
                            <span class="flex items-center justify-center w-11 h-11 rounded-full {{ $avatarStyles[$loop->iteration % count($avatarStyles)] }} font-display font-bold">{{ $initials }}</span>
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $testimonial->name }}</p>
                                <p class="text-xs text-slate-500">{{ $testimonial->location }}</p>
                            </div>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================
    (g) CTA BANNER
    ============================================================ --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-card bg-gradient-to-br from-blue-700 via-blue-700 to-blue-800 px-8 py-14 sm:px-14 sm:py-16 shadow-hover">
                <div aria-hidden="true" class="pointer-events-none absolute inset-0">
                    <div class="absolute -top-16 -right-10 w-72 h-72 rounded-full bg-blue-500/30 blur-3xl"></div>
                    <div class="absolute -bottom-20 -left-10 w-72 h-72 rounded-full bg-sand-400/20 blur-3xl"></div>
                </div>
                <div class="relative max-w-2xl">
                    <h2 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-white">{{ __('front.hero_cta_title') }}</h2>
                    <p class="mt-4 text-blue-100 text-lg leading-relaxed">{{ __('front.hero_cta_body') }}</p>
                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('front.packages.index') }}" class="inline-flex justify-center items-center gap-2 px-7 py-3.5 rounded-button text-sm font-bold text-blue-700 bg-white hover:bg-sand-50 shadow-soft transition-colors">
                            {{ __('front.hero_cta_explore') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        @auth
                            <a href="{{ route('dashboard.index') }}" class="inline-flex justify-center items-center px-7 py-3.5 rounded-button text-sm font-semibold text-white border-2 border-white/30 hover:bg-white/10 transition-colors">{{ __('front.hero_cta_my_orders') }}</a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex justify-center items-center px-7 py-3.5 rounded-button text-sm font-semibold text-white border-2 border-white/30 hover:bg-white/10 transition-colors">{{ __('front.hero_cta_register') }}</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
