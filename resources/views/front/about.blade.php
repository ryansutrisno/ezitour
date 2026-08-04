@extends('layouts.front')

@section('title', __('front.about_title') . ' - EziTour')

@section('seo')
    <x-seo
        :title="__('front.about_title')"
        :description="app(App\Settings\AboutSettings::class)->visionText"
        type="website"
    />
@endsection

@section('content')
    @php($home = app(App\Settings\HomeSettings::class))
    @php($about = app(App\Settings\AboutSettings::class))

    {{-- ============================================================
    (1) HERO — gradient mesh, konsisten dengan home
    ============================================================ --}}
    <section class="relative overflow-hidden bg-slate-50">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="absolute -top-24 -right-24 w-[40rem] h-[40rem] rounded-full bg-blue-200/40 blur-3xl"></div>
            <div class="absolute top-40 -left-32 w-[34rem] h-[34rem] rounded-full bg-sand-200/40 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgb(15_23_42/0.05)_1px,transparent_0)] [background-size:22px_22px]"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-24 text-center">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-pill bg-white shadow-soft text-blue-700 text-xs font-semibold border border-blue-100">
                <span class="flex h-2 w-2 rounded-full bg-sand-500"></span>
                {{ __('front.about_eyebrow') }}
            </span>
            <h1 class="mt-6 mx-auto max-w-4xl font-display font-extrabold tracking-tight text-slate-900 text-4xl sm:text-5xl lg:text-6xl leading-[1.05]">
                {{ __('front.about_hero_title_1') }}
                <span class="block bg-gradient-to-r from-blue-600 to-blue-500 bg-clip-text text-transparent">{{ __('front.about_hero_title_2') }}</span>
            </h1>
            <p class="mt-6 mx-auto max-w-2xl text-base sm:text-lg text-slate-600 leading-relaxed">
                {{ __('front.about_hero_body') }}
            </p>
            <div class="mt-8 flex flex-col sm:flex-row justify-center gap-3">
                <a href="{{ route('front.packages.index') }}" class="inline-flex justify-center items-center gap-2 px-7 py-3.5 rounded-button text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-soft transition-all">
                    {{ __('front.about_hero_cta_primary') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="#{{ app()->getLocale() === 'en' ? 'our-story' : 'cerita-kami' }}" class="inline-flex justify-center items-center gap-2 px-7 py-3.5 rounded-button text-sm font-semibold text-slate-700 bg-white border-2 border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition-all">
                    {{ __('front.about_hero_cta_secondary') }}
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================================
    (2) STATS BAR — angka sama dengan home (konsistensi)
    ============================================================ --}}
    <section class="relative -mt-px bg-gradient-to-r from-blue-700 via-blue-700 to-blue-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <dl class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <dt class="sr-only">{{ __('front.about_stats_destinations') }}</dt>
                    <dd class="font-display text-3xl sm:text-4xl font-extrabold text-white">{{ $home->statDestinations }}</dd>
                    <p class="mt-1 text-sm text-blue-200">{{ __('front.about_stats_destinations') }}</p>
                </div>
                <div class="text-center">
                    <dt class="sr-only">{{ __('front.about_stats_travelers') }}</dt>
                    <dd class="font-display text-3xl sm:text-4xl font-extrabold text-white">{{ $home->statTravelers }}</dd>
                    <p class="mt-1 text-sm text-blue-200">{{ __('front.about_stats_travelers') }}</p>
                </div>
                <div class="text-center">
                    <dt class="sr-only">{{ __('front.about_stats_rating') }}</dt>
                    <dd class="font-display text-3xl sm:text-4xl font-extrabold text-sand-300">{{ $home->statRating }}</dd>
                    <p class="mt-1 text-sm text-blue-200">{{ __('front.about_stats_rating') }}</p>
                </div>
                <div class="text-center">
                    <dt class="sr-only">{{ __('front.about_stats_support') }}</dt>
                    <dd class="font-display text-3xl sm:text-4xl font-extrabold text-white">{{ $home->statSupport }}</dd>
                    <p class="mt-1 text-sm text-blue-200">{{ __('front.about_stats_support') }}</p>
                </div>
            </dl>
        </div>
    </section>

    {{-- ============================================================
    (3) CERITA KAMI — narasi + decorative visual card
    ============================================================ --}}
    <section id="{{ app()->getLocale() === 'en' ? 'our-story' : 'cerita-kami' }}" class="py-20 bg-white scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                {{-- Narrative --}}
                <div>
                    <span class="text-blue-600 font-semibold text-sm tracking-wide uppercase">{{ __('front.about_story_eyebrow') }}</span>
                    <h2 class="mt-2 font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">{{ __('front.about_story_title') }}</h2>
                    <div class="mt-5 space-y-4 text-slate-600 leading-relaxed">
                        <p>{{ __('front.about_story_p1') }}</p>
                        <p>{{ __('front.about_story_p2') }}</p>
                        <p>{{ __('front.about_story_p3') }}</p>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-6">
                        <div>
                            <p class="font-display text-2xl font-extrabold text-blue-600">{{ $about->foundedYear }}</p>
                            <p class="text-sm text-slate-500">{{ __('front.about_founded') }}</p>
                        </div>
                        <div class="border-l border-slate-200 pl-6">
                            <p class="font-display text-2xl font-extrabold text-blue-600">{{ $about->provincesCovered }}</p>
                            <p class="text-sm text-slate-500">{{ __('front.about_provinces') }}</p>
                        </div>
                        <div class="border-l border-slate-200 pl-6">
                            <p class="font-display text-2xl font-extrabold text-blue-600">{{ $about->partnersCount }}</p>
                            <p class="text-sm text-slate-500">{{ __('front.about_partners') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Decorative visual: gradient card with archipelago route motif --}}
                <div class="relative">
                    <div class="relative aspect-square sm:aspect-[4/3] rounded-card bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 shadow-hover overflow-hidden">
                        <div aria-hidden="true" class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-sand-300/40 blur-2xl"></div>

                        <svg class="absolute inset-0 w-full h-full" viewBox="0 0 400 400" fill="none" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                            {{-- dotted travel route across archipelago --}}
                            <path d="M40 120 Q120 80 180 140 T340 110" stroke="#ffffff" stroke-width="2.5" stroke-dasharray="4 8" stroke-linecap="round" opacity="0.5" fill="none"/>
                            <path d="M60 240 Q140 280 220 240 T360 270" stroke="#ffffff" stroke-width="2.5" stroke-dasharray="4 8" stroke-linecap="round" opacity="0.35" fill="none"/>
                            {{-- stylized island blobs --}}
                            <path d="M70 150 q20 -25 50 -15 q30 5 25 35 q-5 25 -35 25 q-35 0 -40 -25 z" fill="#1e40af" opacity="0.55"/>
                            <path d="M200 180 q25 -15 50 0 q20 20 0 40 q-25 15 -50 0 q-15 -25 0 -40 z" fill="#1d4ed8" opacity="0.6"/>
                            <path d="M150 270 q15 -20 45 -10 q25 10 15 35 q-10 25 -40 20 q-30 -10 -20 -45 z" fill="#1e3a8a" opacity="0.65"/>
                            <path d="M290 250 q20 -10 35 5 q10 20 -10 30 q-25 10 -30 -15 z" fill="#1d4ed8" opacity="0.5"/>
                            {{-- sun glow --}}
                            <circle cx="320" cy="80" r="28" fill="#fcd34d" opacity="0.85"/>
                        </svg>

                        {{-- Location pins --}}
                        <div class="absolute top-[28%] left-[16%] flex items-center gap-1.5 px-2.5 py-1 rounded-pill bg-white/95 shadow-card text-[11px] font-semibold text-slate-700 backdrop-blur">
                            <span class="flex h-2 w-2 rounded-full bg-sand-500"></span> Yogyakarta
                        </div>
                        <div class="absolute top-[55%] left-[42%] flex items-center gap-1.5 px-2.5 py-1 rounded-pill bg-white/95 shadow-card text-[11px] font-semibold text-slate-700 backdrop-blur">
                            <span class="flex h-2 w-2 rounded-full bg-blue-400"></span> Bali
                        </div>
                        <div class="absolute top-[68%] right-[18%] flex items-center gap-1.5 px-2.5 py-1 rounded-pill bg-white/95 shadow-card text-[11px] font-semibold text-slate-700 backdrop-blur">
                            <span class="flex h-2 w-2 rounded-full bg-blue-300"></span> Lombok
                        </div>
                    </div>

                    {{-- Floating badge --}}
                    <div class="absolute -bottom-5 -right-3 sm:-right-5 flex items-center gap-3 bg-white rounded-card shadow-hover border border-slate-100 px-4 py-3">
                        <span class="flex items-center justify-center w-10 h-10 rounded-pill bg-sand-100 text-sand-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        </span>
                        <div>
                            <p class="text-xs text-slate-500">{{ __('front.hero_rating_label') }}</p>
                            <p class="font-display text-sm font-bold text-slate-900">{{ __('front.hero_rating_value') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
    (4) MISI & VISI — 2 kartu side-by-side
    ============================================================ --}}
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center">
                <span class="text-blue-600 font-semibold text-sm tracking-wide uppercase">{{ __('front.about_direction_eyebrow') }}</span>
                <h2 class="mt-2 font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">{{ __('front.about_direction_title') }}</h2>
                <p class="mt-4 text-slate-600 leading-relaxed">{{ __('front.about_direction_intro') }}</p>
            </div>

            <div class="mt-12 grid md:grid-cols-2 gap-6">
                {{-- Visi --}}
                <div class="rounded-card bg-white border border-slate-100 shadow-card p-8">
                    <div class="flex items-center justify-center w-12 h-12 rounded-button bg-blue-50 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <h3 class="mt-5 font-display text-xl font-bold text-slate-900">{{ __('front.about_vision_title') }}</h3>
                    <p class="mt-3 text-slate-600 leading-relaxed">
                        {{ app()->getLocale() === 'en' && $about->visionText_en ? $about->visionText_en : $about->visionText }}
                    </p>
                </div>

                {{-- Misi --}}
                <div class="rounded-card bg-white border border-slate-100 shadow-card p-8">
                    <div class="flex items-center justify-center w-12 h-12 rounded-button bg-sand-100 text-sand-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                    </div>
                    <h3 class="mt-5 font-display text-xl font-bold text-slate-900">{{ __('front.about_mission_title') }}</h3>
                    <ul class="mt-3 space-y-2.5 text-slate-600 leading-relaxed">
                        @foreach((app()->getLocale() === 'en' && !empty($about->missionPoints_en) ? $about->missionPoints_en : $about->missionPoints) as $missionPoint)
                            <li class="flex items-start"><svg class="w-5 h-5 mr-2.5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>{{ $missionPoint['point'] }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
    (5) NILAI-NILAI — 4 kartu dengan ikon
    ============================================================ --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center">
                <span class="text-blue-600 font-semibold text-sm tracking-wide uppercase">{{ __('front.about_values_eyebrow') }}</span>
                <h2 class="mt-2 font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">{{ __('front.about_values_title') }}</h2>
                <p class="mt-4 text-slate-600 leading-relaxed">{{ __('front.about_values_intro') }}</p>
            </div>

            <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Value 1: Sistematis --}}
                <div class="group rounded-card border border-slate-100 bg-white p-6 shadow-soft hover:shadow-hover hover:-translate-y-1 transition-all duration-200">
                    <div class="flex items-center justify-center w-12 h-12 rounded-button bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                    </div>
                    <h3 class="mt-5 font-display text-lg font-bold text-slate-900">{{ __('front.about_value_systematic_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ __('front.about_value_systematic_body') }}</p>
                </div>
                {{-- Value 2: Transparan --}}
                <div class="group rounded-card border border-slate-100 bg-white p-6 shadow-soft hover:shadow-hover hover:-translate-y-1 transition-all duration-200">
                    <div class="flex items-center justify-center w-12 h-12 rounded-button bg-sand-100 text-sand-600 group-hover:bg-sand-500 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <h3 class="mt-5 font-display text-lg font-bold text-slate-900">{{ __('front.about_value_transparent_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ __('front.about_value_transparent_body') }}</p>
                </div>
                {{-- Value 3: Berkelanjutan --}}
                <div class="group rounded-card border border-slate-100 bg-white p-6 shadow-soft hover:shadow-hover hover:-translate-y-1 transition-all duration-200">
                    <div class="flex items-center justify-center w-12 h-12 rounded-button bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    </div>
                    <h3 class="mt-5 font-display text-lg font-bold text-slate-900">{{ __('front.about_value_sustainable_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ __('front.about_value_sustainable_body') }}</p>
                </div>
                {{-- Value 4: Pelanggan First --}}
                <div class="group rounded-card border border-slate-100 bg-white p-6 shadow-soft hover:shadow-hover hover:-translate-y-1 transition-all duration-200">
                    <div class="flex items-center justify-center w-12 h-12 rounded-button bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <h3 class="mt-5 font-display text-lg font-bold text-slate-900">{{ __('front.about_value_customer_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ __('front.about_value_customer_body') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
    (6) TIM KAMI — 4 kartu founder dengan initial-circle avatar
    ============================================================ --}}
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center">
                <span class="text-blue-600 font-semibold text-sm tracking-wide uppercase">{{ __('front.about_team_eyebrow') }}</span>
                <h2 class="mt-2 font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">{{ __('front.about_team_title') }}</h2>
                <p class="mt-4 text-slate-600 leading-relaxed">{{ __('front.about_team_intro') }}</p>
            </div>

            <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Member 1 --}}
                <div class="rounded-card bg-white border border-slate-100 shadow-card p-7 text-center">
                    <span class="mx-auto flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white font-display text-2xl font-extrabold shadow-soft">RA</span>
                    <h3 class="mt-5 font-display text-lg font-bold text-slate-900">Rizky Aditya</h3>
                    <p class="text-sm font-semibold text-blue-600">{{ __('front.about_member_ra_role') }}</p>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ __('front.about_member_ra_bio') }}</p>
                </div>
                {{-- Member 2 --}}
                <div class="rounded-card bg-white border border-slate-100 shadow-card p-7 text-center">
                    <span class="mx-auto flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-sand-400 to-sand-600 text-white font-display text-2xl font-extrabold shadow-soft">MP</span>
                    <h3 class="mt-5 font-display text-lg font-bold text-slate-900">Maharani Putri</h3>
                    <p class="text-sm font-semibold text-blue-600">{{ __('front.about_member_mp_role') }}</p>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ __('front.about_member_mp_bio') }}</p>
                </div>
                {{-- Member 3 --}}
                <div class="rounded-card bg-white border border-slate-100 shadow-card p-7 text-center">
                    <span class="mx-auto flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-blue-700 to-blue-900 text-white font-display text-2xl font-extrabold shadow-soft">BP</span>
                    <h3 class="mt-5 font-display text-lg font-bold text-slate-900">Bagus Pratama</h3>
                    <p class="text-sm font-semibold text-blue-600">{{ __('front.about_member_bp_role') }}</p>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ __('front.about_member_bp_bio') }}</p>
                </div>
                {{-- Member 4 --}}
                <div class="rounded-card bg-white border border-slate-100 shadow-card p-7 text-center">
                    <span class="mx-auto flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-sand-500 to-sand-700 text-white font-display text-2xl font-extrabold shadow-soft">SD</span>
                    <h3 class="mt-5 font-display text-lg font-bold text-slate-900">Sinta Dewi</h3>
                    <p class="text-sm font-semibold text-blue-600">{{ __('front.about_member_sd_role') }}</p>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ __('front.about_member_sd_bio') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
    (7) CTA BANNER
    ============================================================ --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-card bg-gradient-to-br from-blue-700 via-blue-700 to-blue-800 px-8 py-14 sm:px-14 sm:py-16 shadow-hover">
                <div aria-hidden="true" class="pointer-events-none absolute inset-0">
                    <div class="absolute -top-16 -right-10 w-72 h-72 rounded-full bg-blue-500/30 blur-3xl"></div>
                    <div class="absolute -bottom-20 -left-10 w-72 h-72 rounded-full bg-sand-400/20 blur-3xl"></div>
                </div>
                <div class="relative max-w-2xl">
                    <h2 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-white">{{ __('front.about_cta_title') }}</h2>
                    <p class="mt-4 text-blue-100 text-lg leading-relaxed">{{ __('front.about_cta_body') }}</p>
                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('front.packages.index') }}" class="inline-flex justify-center items-center gap-2 px-7 py-3.5 rounded-button text-sm font-bold text-blue-700 bg-white hover:bg-sand-50 shadow-soft transition-colors">
                            {{ __('front.hero_cta_explore') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="mailto:hallo@trazmedia.com" class="inline-flex justify-center items-center px-7 py-3.5 rounded-button text-sm font-semibold text-white border-2 border-white/30 hover:bg-white/10 transition-colors">{{ __('front.about_cta_contact') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
