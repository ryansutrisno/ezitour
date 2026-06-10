@extends('layouts.front')

@section('title', 'Cari Paket Wisata - EziTour')

@section('content')
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">Temukan Petualanganmu</h1>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">Jelajahi berbagai paket wisata menarik yang telah kami siapkan untuk pengalaman tak terlupakan.</p>
            </div>
            
            <!-- Search Bar -->
            <div class="mt-10 max-w-xl mx-auto">
                <form action="{{ route('front.packages.index') }}" method="GET" class="flex gap-2">
                    <input type="text" name="keyword" value="{{ request('keyword') }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md py-3 px-4 bg-gray-50" placeholder="Cari paket atau destinasi...">
                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cari
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($packages->count() > 0)
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
            
            <div class="mt-8">
                {{ $packages->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 text-lg">Maaf, paket wisata yang kamu cari tidak ditemukan.</p>
                <a href="{{ route('front.packages.index') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">Lihat semua paket</a>
            </div>
        @endif
    </div>
@endsection
