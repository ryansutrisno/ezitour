@extends('layouts.front')

@section('title', __('front.wishlist_title') . ' - EziTour')

@section('seo')
    <x-seo :title="__('front.wishlist_seo_title')" :description="__('front.wishlist_seo_description')" noindex />
@endsection

@section('content')
    {{-- Gradient header band (Ocean & Sand, mirrors packages/index hero) --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800">
        <div class="absolute inset-0 opacity-20" aria-hidden="true">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-pill bg-white/10 blur-3xl"></div>
            <div class="absolute -bottom-32 -left-16 w-96 h-96 rounded-pill bg-blue-300/20 blur-3xl"></div>
        </div>
        <div class="relative max-w-7xl mx-auto py-14 sm:py-20 px-4 sm:px-6 lg:px-8">
            <nav class="mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1.5 text-xs">
                    <li><a href="{{ route('front.home') }}" class="text-blue-100 hover:text-white transition-colors">{{ __('front.nav_home') }}</a></li>
                    <li class="text-blue-200">/</li>
                    <li class="text-white font-semibold" aria-current="page">{{ __('front.wishlist_breadcrumb') }}</li>
                </ol>
            </nav>
            <h1 class="font-display text-4xl sm:text-5xl font-extrabold tracking-tight text-white">{{ __('front.wishlist_h1') }}</h1>
            <p class="mt-4 max-w-2xl text-lg text-blue-100 leading-relaxed">{{ __('front.wishlist_intro') }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if(session('success'))
            <div class="mb-8 rounded-button bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 font-medium flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if($packages->isNotEmpty())
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-slate-500">
                    {{ __('front.wishlist_count', ['first' => $packages->firstItem(), 'last' => $packages->lastItem(), 'total' => $packages->total()]) }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 xl:grid-cols-3">
                @foreach($packages as $package)
                    <div class="group relative flex flex-col rounded-card bg-white border border-slate-100 shadow-card hover:shadow-hover overflow-hidden transition-all duration-200 hover:-translate-y-1">
                        <div class="relative h-48 overflow-hidden">
                            <a href="{{ route('front.packages.show', $package->slug) }}" class="block h-full w-full">
                                @if($package->thumbnail_url)
                                    <img class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" src="{{ $package->thumbnail_url }}" alt="{{ $package->name }}">
                                @else
                                    <div class="h-full w-full bg-gradient-to-br from-blue-500 via-blue-600 to-blue-800 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                @endif
                            </a>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 via-transparent to-transparent pointer-events-none"></div>

                            {{-- Heart toggle (favorited state — filled red) --}}
                            <button type="button"
                                data-wishlist-toggle
                                data-package-slug="{{ $package->slug }}"
                                class="favorited absolute top-3 right-3 z-10 inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/90 backdrop-blur text-red-500 shadow-soft hover:bg-white hover:scale-110 transition-all"
                                title="{{ __('front.button_remove_wishlist') }}"
                                aria-label="{{ __('front.button_remove_wishlist') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                            </button>

                            @if(filled($package->duration_days))
                                <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-pill bg-blue-600/90 backdrop-blur text-[11px] font-bold text-white shadow-soft pointer-events-none">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $package->duration_days }} {{ __('front.days_unit') }}
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex-1">
                                <a href="{{ route('front.packages.show', $package->slug) }}">
                                    <h3 class="font-display text-lg font-bold text-slate-900 group-hover:text-blue-700 transition-colors">{{ $package->name }}</h3>
                                </a>
                                @if(filled($package->region) || filled($package->category))
                                    <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                        @if(filled($package->region))
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-pill bg-blue-50 text-blue-700 text-[11px] font-semibold">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                {{ $package->region }}
                                            </span>
                                        @endif
                                        @if(filled($package->category))
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-pill bg-sand-100 text-sand-700 text-[11px] font-semibold">
                                                {{ $package->category }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                                <p class="mt-2 text-sm text-slate-600 line-clamp-3 leading-relaxed">{{ $package->description }}</p>
                            </div>

                            <div class="mt-5 pt-5 border-t border-slate-100 flex items-end justify-between gap-3">
                                <div>
                                    <p class="text-[11px] text-slate-400 font-medium">{{ __('front.hero_price_from') }}</p>
                                    <p class="font-display text-xl font-extrabold text-blue-600">Rp {{ number_format($package->total_price, 0, ',', '.') }}</p>
                                </div>
                                <a href="{{ route('front.packages.show', $package->slug) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 px-3.5 py-2 rounded-button shadow-soft transition-colors">
                                    {{ __('front.button_view_details') }}
                                </a>
                            </div>

                            {{-- One-click remove via DELETE form (works even without JS) --}}
                            <form action="{{ route('front.wishlist.destroy', $package->slug) }}" method="POST" class="mt-3">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-red-600 py-1.5 rounded-button transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    {{ __('front.button_remove') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $packages->links() }}
            </div>
        @else
            {{-- Empty state --}}
            <div class="bg-white rounded-card border border-slate-100 shadow-card p-12 sm:p-16 text-center">
                <div class="mx-auto flex items-center justify-center w-20 h-20 rounded-pill bg-gradient-to-br from-blue-50 to-blue-100 text-blue-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"/></svg>
                </div>
                <h3 class="mt-6 font-display text-xl font-bold text-slate-900">{{ __('front.wishlist_empty_title') }}</h3>
                <p class="mt-2 text-sm text-slate-500 max-w-sm mx-auto">{{ __('front.wishlist_empty_body') }}</p>
                <div class="mt-7">
                    <a href="{{ route('front.packages.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-button text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-soft transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        {{ __('front.wishlist_empty_cta') }}
                    </a>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        @include('front.wishlist.partials._toggle-script')
    @endpush
@endsection
