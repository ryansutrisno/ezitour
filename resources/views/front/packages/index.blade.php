@extends('layouts.front')

@section('title', 'Cari Paket Wisata - EziTour')

@section('content')
    {{-- Header band --}}
    <div class="bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto py-14 sm:py-20 px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <span class="text-blue-600 font-semibold text-sm tracking-wide uppercase">Jelajahi</span>
                <h1 class="mt-2 font-display text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900">Temukan Petualanganmu</h1>
                <p class="mt-4 max-w-2xl mx-auto text-lg text-slate-500 leading-relaxed">Pilih dari beragam paket wisata menarik yang kami siapkan untuk pengalaman tak terlupakan.</p>
            </div>

            {{-- Search bar --}}
            <div class="mt-9 max-w-xl mx-auto">
                <form action="{{ route('front.packages.index') }}" method="GET">
                    <div class="flex flex-col sm:flex-row gap-2.5 p-2 bg-white rounded-card border-2 border-slate-100 shadow-card">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="block w-full pl-11 pr-3 py-3 rounded-input border-0 bg-transparent focus:outline-none focus:ring-0 sm:text-sm text-slate-800 placeholder-slate-400" placeholder="Cari paket atau destinasi...">
                        </div>
                        <button type="submit" class="inline-flex justify-center items-center gap-2 px-6 py-3 rounded-button text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-soft transition-all">
                            Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($packages->count() > 0)
            <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($packages as $package)
                    <a href="{{ route('front.packages.show', $package->slug) }}" class="group flex flex-col rounded-card bg-white border border-slate-100 shadow-card hover:shadow-hover overflow-hidden transition-all duration-200 hover:-translate-y-1">
                        <div class="relative h-48 overflow-hidden">
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
                        </div>
                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex-1">
                                <h3 class="font-display text-lg font-bold text-slate-900 group-hover:text-blue-700 transition-colors">{{ $package->name }}</h3>
                                <p class="mt-2 text-sm text-slate-600 line-clamp-3 leading-relaxed">{{ $package->description }}</p>
                            </div>
                            <div class="mt-5 pt-5 border-t border-slate-100 flex items-end justify-between gap-3">
                                <div>
                                    <p class="text-[11px] text-slate-400 font-medium">Mulai dari</p>
                                    <p class="font-display text-xl font-extrabold text-blue-600">Rp {{ number_format($package->total_price, 0, ',', '.') }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1 text-sm font-semibold text-white bg-blue-600 group-hover:bg-blue-700 px-3.5 py-2 rounded-button shadow-soft transition-colors">Detail</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $packages->links() }}
            </div>
        @else
            <div class="bg-white rounded-card border border-slate-100 shadow-card p-12 sm:p-16 text-center">
                <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-pill bg-slate-100 text-slate-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="mt-5 font-display text-lg font-bold text-slate-900">Paket tidak ditemukan</h3>
                <p class="mt-1.5 text-sm text-slate-500">Maaf, tidak ada paket yang cocok dengan pencarianmu. Coba kata kunci lain.</p>
                <div class="mt-6">
                    <a href="{{ route('front.packages.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-button text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-soft transition-all">
                        Lihat semua paket
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
