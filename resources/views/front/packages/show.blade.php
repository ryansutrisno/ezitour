@extends('layouts.front')

@section('title', $package->name . ' - EziTour')

@section('content')
    <div class="bg-white">
        <!-- Hero Header -->
        <div class="relative h-96">
            <img class="w-full h-full object-cover" src="{{ $package->thumbnail_url }}" alt="{{ $package->name }}">
            <div class="absolute inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
                <div class="text-center px-4">
                    <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">{{ $package->name }}</h1>
                    <p class="mt-4 text-xl text-gray-200 max-w-3xl mx-auto">{{ $package->description }}</p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Main Content: Itinerary -->
                <div class="lg:col-span-2">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8">Itinerary Perjalanan</h2>
                    
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach($package->items as $index => $item)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <span class="text-white font-bold text-sm">{{ $loop->iteration }}</span>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <h3 class="text-lg font-medium text-gray-900">{{ $item->destination->name }}</h3>
                                                <p class="text-sm text-gray-500 mt-1">{{ $item->destination->description }}</p>
                                                <div class="mt-2 flex items-center gap-2 text-sm text-gray-500">
                                                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                                        Durasi: {{ $item->destination->avg_duration }} menit
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <img class="h-20 w-20 rounded-lg object-cover" src="{{ $item->destination->image_url }}" alt="{{ $item->destination->name }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Additional Info -->
                    <div class="mt-12 bg-blue-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-blue-900 mb-4">Fasilitas Termasuk</h3>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <li class="flex items-center text-blue-800">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Mobil Ber-AC (Avanza/Innova/Hiace)
                            </li>
                            <li class="flex items-center text-blue-800">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Supir Berpengalaman + BBM
                            </li>
                            <li class="flex items-center text-blue-800">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Tiket Masuk Wisata (Sesuai Itinerary)
                            </li>
                            <li class="flex items-center text-blue-800">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Air Mineral
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Booking Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24 border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Mulai Liburanmu!</h3>
                        
                        <div class="flex items-baseline mb-6">
                            <span class="text-3xl font-extrabold text-blue-600">Rp {{ number_format($package->total_price, 0, ',', '.') }}</span>
                            <span class="ml-2 text-gray-500">/ pax</span>
                        </div>

                        <div class="space-y-4 mb-6">
                            <div class="flex items-center text-gray-600">
                                <svg class="h-5 w-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm">Durasi: {{ $package->items->sum(fn($item) => $item->destination->avg_duration) }} menit</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <svg class="h-5 w-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-sm">{{ $package->items->count() }} destinasi wisata</span>
                            </div>
                        </div>

                        <a href="{{ route('front.checkout.show', $package->slug) }}" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Pesan Sekarang
                        </a>
                        <p class="mt-4 text-xs text-center text-gray-500">Pembayaran aman & terpercaya</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
