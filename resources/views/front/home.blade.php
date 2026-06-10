@extends('layouts.front')

@section('title', 'EziTour - Liburan Tanpa Ribet')

@section('content')
    <!-- Hero Section -->
    <div class="relative bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                            <span class="block xl:inline">Liburan Impian</span>
                            <span class="block text-blue-600 xl:inline">Tanpa Ribet</span>
                        </h1>
                        <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            Pilih paket wisata favoritmu, kami urus sisanya. Mulai dari transportasi, supir, hingga tiket masuk wisata. Kamu tinggal duduk manis!
                        </p>
                        
                        <!-- Search Box -->
                        <div class="mt-8 sm:mt-10 sm:flex sm:justify-center lg:justify-start">
                            <form action="{{ route('front.packages.index') }}" method="GET" class="w-full max-w-lg">
                                <div class="flex rounded-md shadow-sm">
                                    <input type="text" name="keyword" class="focus:ring-blue-500 focus:border-blue-500 block w-full rounded-l-md sm:text-sm border-gray-300 py-3 px-4 bg-gray-50" placeholder="Cari destinasi (misal: Jogja, Borobudur)">
                                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-r-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Cari
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
            <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=2021&auto=format&fit=crop" alt="Travel">
        </div>
    </div>

    <!-- Featured Packages -->
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">Paket Terpopuler</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Pilihan Liburan Terbaik
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                    Hemat waktu dan biaya dengan paket bundling eksklusif kami.
                </p>
            </div>

            <div class="mt-10">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($packages as $package)
                    <div class="flex flex-col rounded-lg shadow-lg overflow-hidden bg-white hover:shadow-xl transition-shadow duration-300">
                        <div class="flex-shrink-0">
                            <img class="h-48 w-full object-cover" src="{{ $package->thumbnail_url }}" alt="{{ $package->name }}">
                        </div>
                        <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                            <div class="flex-1">
                                <a href="{{ route('front.packages.show', $package->slug) }}" class="block mt-2">
                                    <p class="text-xl font-semibold text-gray-900">{{ $package->name }}</p>
                                    <p class="mt-3 text-base text-gray-500 line-clamp-3">{{ $package->description }}</p>
                                </a>
                            </div>
                            <div class="mt-6 flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="text-2xl font-bold text-blue-600">Rp {{ number_format($package->total_price, 0, ',', '.') }}</span>
                                </div>
                                <a href="{{ route('front.packages.show', $package->slug) }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="mt-10 text-center">
                <a href="{{ route('front.packages.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">Lihat Semua Paket &rarr;</a>
            </div>
        </div>
    </div>
@endsection
