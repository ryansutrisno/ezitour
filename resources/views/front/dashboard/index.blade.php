@extends('layouts.front')

@section('title', 'Dashboard Saya - EziTour')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Halo, {{ Auth::user()->name }}! 👋
            </h2>
            <p class="mt-1 text-sm text-gray-500">Selamat datang di dashboard perjalananmu.</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Keluar
                </button>
            </form>
        </div>
    </div>

    <div class="mt-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Riwayat Pesanan</h3>
            
            {{-- Payment Status Filter - Task 16 --}}
            <div class="mt-3 sm:mt-0 flex flex-wrap gap-2">
                <a href="{{ route('dashboard.index') }}" 
                   class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition-colors duration-150 {{ ($paymentFilter ?? 'all') === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Semua
                    <span class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs {{ ($paymentFilter ?? 'all') === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-600' }}">
                        {{ $allCount ?? 0 }}
                    </span>
                </a>
                <a href="{{ route('dashboard.index', ['payment_status' => 'paid']) }}" 
                   class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition-colors duration-150 {{ ($paymentFilter ?? 'all') === 'paid' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Lunas
                    <span class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs {{ ($paymentFilter ?? 'all') === 'paid' ? 'bg-green-500 text-white' : 'bg-green-100 text-green-600' }}">
                        {{ $paidCount ?? 0 }}
                    </span>
                </a>
                <a href="{{ route('dashboard.index', ['payment_status' => 'unpaid']) }}" 
                   class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition-colors duration-150 {{ ($paymentFilter ?? 'all') === 'unpaid' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Belum Lunas
                    <span class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs {{ ($paymentFilter ?? 'all') === 'unpaid' ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-600' }}">
                        {{ $unpaidCount ?? 0 }}
                    </span>
                </a>
            </div>
        </div>
        
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mt-4 bg-green-50 border-l-4 border-green-400 p-4">
                <div class="flex">
                    <div class="shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mt-4 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        @if(session('error_title'))
                            <h3 class="text-sm font-medium text-red-800">{{ session('error_title') }}</h3>
                        @endif
                        <p class="text-sm text-red-700 {{ session('error_title') ? 'mt-1' : '' }}">{{ session('error') }}</p>
                        @if(session('error_suggestion'))
                            <p class="mt-2 text-sm text-red-600">{{ session('error_suggestion') }}</p>
                        @endif
                        @if(session('can_retry'))
                            <div class="mt-3">
                                <button onclick="window.location.reload()" class="inline-flex items-center px-3 py-1.5 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Coba Lagi
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Bookings List --}}
        <div class="mt-4 space-y-4">
            @forelse($bookings as $booking)
                @include('front.dashboard.partials.booking-card', ['booking' => $booking])
            @empty
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    @if(($paymentFilter ?? 'all') === 'paid')
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pesanan yang lunas</h3>
                        <p class="mt-1 text-sm text-gray-500">Pesanan yang sudah dibayar akan muncul di sini.</p>
                    @elseif(($paymentFilter ?? 'all') === 'unpaid')
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pesanan yang belum lunas</h3>
                        <p class="mt-1 text-sm text-gray-500">Semua pesananmu sudah dibayar. Mantap! 🎉</p>
                    @else
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pesanan</h3>
                        <p class="mt-1 text-sm text-gray-500">Mulai petualanganmu dengan memesan paket wisata.</p>
                    @endif
                    <div class="mt-6">
                        <a href="{{ route('front.packages.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Cari Paket Wisata
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
