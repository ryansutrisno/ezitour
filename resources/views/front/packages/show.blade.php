@extends('layouts.front')

@section('title', $package->name . ' - EziTour')

@section('content')
    <div class="bg-white">
        {{-- Hero header --}}
        <div class="relative h-80 sm:h-96">
            @if($package->thumbnail_url)
                <img class="w-full h-full object-cover" src="{{ $package->thumbnail_url }}" alt="{{ $package->name }}">
            @else
                <div class="w-full h-full bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/40 to-slate-900/30 flex items-end">
                <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pb-10">
                    <nav class="mb-4" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1.5 text-xs">
                            <li><a href="{{ route('front.home') }}" class="text-blue-100 hover:text-white transition-colors">Home</a></li>
                            <li class="text-blue-200">/</li>
                            <li><a href="{{ route('front.packages.index') }}" class="text-blue-100 hover:text-white transition-colors">Paket Wisata</a></li>
                        </ol>
                    </nav>
                    <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white">{{ $package->name }}</h1>
                    @if($package->description)
                        <p class="mt-3 text-base sm:text-lg text-slate-200 max-w-3xl leading-relaxed">{{ $package->description }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 lg:gap-12">
                {{-- Main Content: Itinerary --}}
                <div class="lg:col-span-2">
                    <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 mb-8">Itinerary Perjalanan</h2>

                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach($package->items as $index => $item)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-blue-100" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-4">
                                        <div>
                                            <span class="flex items-center justify-center h-10 w-10 rounded-pill bg-gradient-to-br from-blue-500 to-blue-700 text-white font-display font-bold text-sm shadow-soft ring-4 ring-white">
                                                {{ $loop->iteration }}
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1 flex justify-between space-x-4 gap-4">
                                            <div>
                                                <h3 class="font-display text-lg font-bold text-slate-900">{{ $item->destination->name }}</h3>
                                                <p class="text-sm text-slate-500 mt-1 leading-relaxed">{{ $item->destination->description }}</p>
                                                <span class="mt-2 inline-flex items-center gap-1 bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-pill">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    {{ $item->destination->avg_duration }} menit
                                                </span>
                                            </div>
                                            <div class="shrink-0">
                                                @if($item->destination->image_url)
                                                    <img class="h-20 w-20 rounded-button object-cover" src="{{ $item->destination->image_url }}" alt="{{ $item->destination->name }}">
                                                @else
                                                    <div class="h-20 w-20 rounded-button bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                                        <svg class="w-8 h-8 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Facilities --}}
                    <div class="mt-12 bg-gradient-to-br from-blue-50 to-slate-50 rounded-card p-6 sm:p-7 border border-blue-100">
                        <h3 class="font-display text-lg font-bold text-blue-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Fasilitas Termasuk
                        </h3>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <li class="flex items-center text-blue-900 text-sm bg-white/60 rounded-button px-3 py-2">
                                <svg class="h-4 w-4 mr-2 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Mobil Ber-AC (Avanza/Innova/Hiace)
                            </li>
                            <li class="flex items-center text-blue-900 text-sm bg-white/60 rounded-button px-3 py-2">
                                <svg class="h-4 w-4 mr-2 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Supir Berpengalaman + BBM
                            </li>
                            <li class="flex items-center text-blue-900 text-sm bg-white/60 rounded-button px-3 py-2">
                                <svg class="h-4 w-4 mr-2 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Tiket Masuk Wisata (Sesuai Itinerary)
                            </li>
                            <li class="flex items-center text-blue-900 text-sm bg-white/60 rounded-button px-3 py-2">
                                <svg class="h-4 w-4 mr-2 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Air Mineral
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Booking Card --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-card border border-slate-100 shadow-card p-6 sticky top-20">
                        <h3 class="font-display text-xl font-bold text-slate-900 mb-4">Mulai Liburanmu!</h3>

                        <div class="flex items-baseline mb-5">
                            <span class="font-display text-3xl font-extrabold text-blue-600">Rp {{ number_format($package->total_price, 0, ',', '.') }}</span>
                            <span class="ml-2 text-sm text-slate-500">/ pax</span>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div class="flex items-center text-slate-600 text-sm">
                                <svg class="h-5 w-5 mr-2.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Durasi: {{ $package->items->sum(fn($item) => $item->destination->avg_duration) }} menit
                            </div>
                            <div class="flex items-center text-slate-600 text-sm">
                                <svg class="h-5 w-5 mr-2.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $package->items->count() }} destinasi wisata
                            </div>
                        </div>

                        <a href="{{ route('front.checkout.show', $package->slug) }}" class="w-full flex justify-center items-center gap-2 py-3.5 rounded-button text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-soft transition-all">
                            Pesan Sekarang
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <p class="mt-4 text-xs text-center text-slate-400">Pembayaran aman &amp; terpercaya via Midtrans</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
