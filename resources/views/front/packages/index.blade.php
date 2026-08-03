@extends('layouts.front')

@section('title', 'Cari Paket Wisata - EziTour')

@section('seo')
    <x-seo
        title="Paket Wisata"
        description="Jelajahi paket wisata terbaik di Indonesia bersama EziTour. Temukan liburan impianmu sekarang."
        type="website"
    />
@endsection

@section('content')
    {{-- Header band --}}
    <div class="bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto py-14 sm:py-20 px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <span class="text-blue-600 font-semibold text-sm tracking-wide uppercase">Jelajahi</span>
                <h1 class="mt-2 font-display text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900">Temukan Petualanganmu</h1>
                <p class="mt-4 max-w-2xl mx-auto text-lg text-slate-500 leading-relaxed">Pilih dari beragam paket wisata menarik yang kami siapkan untuk pengalaman tak terlupakan.</p>
            </div>

            {{-- Search bar (preserves keyword param) --}}
            <div class="mt-9 max-w-xl mx-auto">
                <form action="{{ route('front.packages.index') }}" method="GET" id="search-form">
                    {{-- Preserve facet filters when searching --}}
                    @if(filled($filters['region'] ?? null))<input type="hidden" name="region" value="{{ $filters['region'] }}">@endif
                    @if(filled($filters['category'] ?? null))<input type="hidden" name="category" value="{{ $filters['category'] }}">@endif
                    @if(filled($filters['duration_min'] ?? null))<input type="hidden" name="duration_min" value="{{ $filters['duration_min'] }}">@endif
                    @if(filled($filters['duration_max'] ?? null))<input type="hidden" name="duration_max" value="{{ $filters['duration_max'] }}">@endif
                    <div class="flex flex-col sm:flex-row gap-2.5 p-2 bg-white rounded-card border-2 border-slate-100 shadow-card">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="keyword" value="{{ $filters['keyword'] ?? request('keyword') }}" class="block w-full pl-11 pr-3 py-3 rounded-input border-0 bg-transparent focus:outline-none focus:ring-0 sm:text-sm text-slate-800 placeholder-slate-400" placeholder="Cari paket atau destinasi...">
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
        <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">
            {{-- Facet sidebar --}}
            <aside class="lg:sticky lg:top-24 self-start">
                <form action="{{ route('front.packages.index') }}" method="GET" id="facet-form" class="bg-white rounded-card border border-slate-100 shadow-card overflow-hidden">
                    {{-- Preserve current keyword in facet submissions --}}
                    @if(filled($filters['keyword'] ?? null))<input type="hidden" name="keyword" value="{{ $filters['keyword'] }}">@endif

                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            <h2 class="font-display text-base font-bold text-white">Filter Paket</h2>
                        </div>
                    </div>

                    <div class="p-5 space-y-6">
                        {{-- Region facet --}}
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Wilayah</h3>
                                @if(filled($filters['region'] ?? null))
                                    <button type="button" data-clear-facet="region" class="text-[11px] font-semibold text-blue-600 hover:text-blue-700">Bersihkan</button>
                                @endif
                            </div>
                            @if($regions->isNotEmpty())
                                <div class="space-y-1.5">
                                    <label class="flex items-center gap-2.5 cursor-pointer group">
                                        <input type="radio" name="region" value="" class="region-radio text-blue-600 focus:ring-blue-500 border-slate-300" @if(blank($filters['region'] ?? null)) checked @endif>
                                        <span class="text-sm text-slate-600 group-hover:text-slate-900">Semua</span>
                                    </label>
                                    @foreach($regions as $value => $label)
                                        <label class="flex items-center gap-2.5 cursor-pointer group">
                                            <input type="radio" name="region" value="{{ $value }}" class="region-radio text-blue-600 focus:ring-blue-500 border-slate-300" @if(($filters['region'] ?? null) === $value) checked @endif>
                                            <span class="text-sm text-slate-600 group-hover:text-slate-900">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic">Belum ada wilayah terdaftar.</p>
                            @endif
                        </div>

                        <div class="border-t border-slate-100"></div>

                        {{-- Category facet --}}
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Kategori</h3>
                                @if(filled($filters['category'] ?? null))
                                    <button type="button" data-clear-facet="category" class="text-[11px] font-semibold text-blue-600 hover:text-blue-700">Bersihkan</button>
                                @endif
                            </div>
                            @if($categories->isNotEmpty())
                                <div class="space-y-1.5">
                                    <label class="flex items-center gap-2.5 cursor-pointer group">
                                        <input type="radio" name="category" value="" class="category-radio text-blue-600 focus:ring-blue-500 border-slate-300" @if(blank($filters['category'] ?? null)) checked @endif>
                                        <span class="text-sm text-slate-600 group-hover:text-slate-900">Semua</span>
                                    </label>
                                    @foreach($categories as $value => $label)
                                        <label class="flex items-center gap-2.5 cursor-pointer group">
                                            <input type="radio" name="category" value="{{ $value }}" class="category-radio text-blue-600 focus:ring-blue-500 border-slate-300" @if(($filters['category'] ?? null) === $value) checked @endif>
                                            <span class="text-sm text-slate-600 group-hover:text-slate-900">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic">Belum ada kategori terdaftar.</p>
                            @endif
                        </div>

                        <div class="border-t border-slate-100"></div>

                        {{-- Duration facet --}}
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wide">Durasi</h3>
                                @if(filled($filters['duration_min'] ?? null) || filled($filters['duration_max'] ?? null))
                                    <button type="button" data-clear-duration class="text-[11px] font-semibold text-blue-600 hover:text-blue-700">Bersihkan</button>
                                @endif
                            </div>
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="radio" name="duration_bucket" value="" class="duration-radio text-blue-600 focus:ring-blue-500 border-slate-300" @if(blank($filters['duration_min'] ?? null) && blank($filters['duration_max'] ?? null)) checked @endif>
                                    <span class="text-sm text-slate-600 group-hover:text-slate-900">Semua</span>
                                </label>
                                @foreach($durationBuckets as $bucket)
                                    @php
                                        $isActive = ((int) ($filters['duration_min'] ?? 0) === (int) $bucket['min']) && ((int) ($filters['duration_max'] ?? 0) === (int) $bucket['max']);
                                    @endphp
                                    <label class="flex items-center gap-2.5 cursor-pointer group">
                                        <input type="radio" name="duration_bucket" value="{{ $bucket['min'] }}-{{ $bucket['max'] }}" class="duration-radio text-blue-600 focus:ring-blue-500 border-slate-300" @if($isActive) checked @endif>
                                        <span class="text-sm text-slate-600 group-hover:text-slate-900">{{ $bucket['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Active filters summary --}}
                        @php
                            $activeFilters = [];
                            if (filled($filters['region'] ?? null)) { $activeFilters['region'] = $filters['region']; }
                            if (filled($filters['category'] ?? null)) { $activeFilters['category'] = $filters['category']; }
                            if (filled($filters['duration_min'] ?? null) || filled($filters['duration_max'] ?? null)) {
                                $activeFilters['duration'] = collect($durationBuckets)->firstWhere('min', (int) ($filters['duration_min'] ?? 0))['label']
                                    ?? (($filters['duration_min'] ?? '?').' - '.($filters['duration_max'] ?? '?').' hari');
                            }
                        @endphp
                        @if(! empty($activeFilters))
                            <div class="border-t border-slate-100 pt-4">
                                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wide mb-2.5">Filter Aktif</h3>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($activeFilters as $key => $label)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-pill bg-blue-50 text-blue-700 text-xs font-semibold">
                                            {{ $label }}
                                        </span>
                                    @endforeach
                                </div>
                                <a href="{{ route('front.packages.index', filled($filters['keyword'] ?? null) ? ['keyword' => $filters['keyword']] : []) }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-red-600 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Hapus Filter
                                </a>
                            </div>
                        @endif
                    </div>
                </form>
            </aside>

            {{-- Results grid --}}
            <div>
                @if($packages->count() > 0)
                    <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach($packages as $package)
                            <div class="group relative">
                                {{-- Heart toggle button (outside the <a> so HTML stays valid).
                                     Initial favorited state resolved via Package::is_favorited accessor. --}}
                                <button type="button"
                                    data-wishlist-toggle
                                    data-package-slug="{{ $package->slug }}"
                                    @class([
                                        'absolute top-3 right-3 z-20 inline-flex items-center justify-center w-9 h-9 rounded-full shadow-soft transition-all hover:scale-110',
                                        'bg-white text-red-500 hover:bg-white' => $package->is_favorited,
                                        'bg-white/80 backdrop-blur text-slate-600 hover:text-red-500 hover:bg-white' => ! $package->is_favorited,
                                    ])
                                    title="{{ $package->is_favorited ? 'Hapus dari Wishlist' : 'Simpan ke Wishlist' }}"
                                    aria-label="{{ $package->is_favorited ? 'Hapus dari Wishlist' : 'Simpan ke Wishlist' }}"
                                    aria-pressed="{{ $package->is_favorited ? 'true' : 'false' }}">
                                    @if($package->is_favorited)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"/></svg>
                                    @endif
                                </button>

                                <a href="{{ route('front.packages.show', $package->slug) }}" class="flex flex-col rounded-card bg-white border border-slate-100 shadow-card hover:shadow-hover overflow-hidden transition-all duration-200 hover:-translate-y-1">
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
                                        @if(filled($package->duration_days))
                                            <span class="absolute bottom-3 right-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-pill bg-blue-600/90 backdrop-blur text-[11px] font-bold text-white shadow-soft">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ $package->duration_days }} hari
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex flex-1 flex-col p-6">
                                        <div class="flex-1">
                                            <h3 class="font-display text-lg font-bold text-slate-900 group-hover:text-blue-700 transition-colors">{{ $package->name }}</h3>
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
                                                <p class="text-[11px] text-slate-400 font-medium">Mulai dari</p>
                                                <p class="font-display text-xl font-extrabold text-blue-600">Rp {{ number_format($package->total_price, 0, ',', '.') }}</p>
                                            </div>
                                            <span class="inline-flex items-center gap-1 text-sm font-semibold text-white bg-blue-600 group-hover:bg-blue-700 px-3.5 py-2 rounded-button shadow-soft transition-colors">Detail</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
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
                        <h3 class="mt-5 font-display text-lg font-bold text-slate-900">Tidak ada paket yang cocok</h3>
                        <p class="mt-1.5 text-sm text-slate-500">Maaf, tidak ada paket yang cocok dengan pencarianmu. Coba ubah atau hapus filter.</p>
                        <div class="mt-6">
                            <a href="{{ route('front.packages.index', filled($filters['keyword'] ?? null) ? ['keyword' => $filters['keyword']] : []) }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-button text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-soft transition-all">
                                Lihat semua paket
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                var form = document.getElementById('facet-form');
                if (! form) return;

                // Map duration buckets to min/max hidden inputs
                var bucketRadios = form.querySelectorAll('input.duration-radio');
                var minInput = document.createElement('input');
                minInput.type = 'hidden';
                minInput.name = 'duration_min';
                form.appendChild(minInput);
                var maxInput = document.createElement('input');
                maxInput.type = 'hidden';
                maxInput.name = 'duration_max';
                form.appendChild(maxInput);

                function syncDuration() {
                    var checked = form.querySelector('input.duration-radio:checked');
                    var value = checked ? checked.value : '';
                    if (! value) {
                        minInput.value = '';
                        maxInput.value = '';
                    } else {
                        var parts = value.split('-');
                        minInput.value = parts[0] || '';
                        maxInput.value = parts[1] || '';
                    }
                }

                syncDuration();

                // Auto-submit when any facet changes
                form.addEventListener('change', function (event) {
                    if (! event.target.matches('input.region-radio, input.category-radio, input.duration-radio')) return;
                    syncDuration();
                    form.submit();
                });

                // "Clear" buttons for region/category
                form.querySelectorAll('[data-clear-facet]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var facet = btn.getAttribute('data-clear-facet');
                        var radio = form.querySelector('input[name="' + facet + '"][value=""]');
                        if (radio) {
                            radio.checked = true;
                            form.submit();
                        }
                    });
                });

                // "Clear" button for duration
                var clearDuration = form.querySelector('[data-clear-duration]');
                if (clearDuration) {
                    clearDuration.addEventListener('click', function () {
                        var radio = form.querySelector('input.duration-radio[value=""]');
                        if (radio) {
                            radio.checked = true;
                            form.submit();
                        }
                    });
                }
            })();
        </script>
    @endpush

    @push('scripts')
        @include('front.wishlist.partials._toggle-script')
    @endpush
@endsection
