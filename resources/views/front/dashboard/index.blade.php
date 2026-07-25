@extends('layouts.front')

@section('title', 'Dashboard Saya - EziTour')

@section('content')
<div class="bg-slate-50 min-h-[calc(100vh-4rem)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    Halo, {{ Auth::user()->name }}! 👋
                </h1>
                <p class="mt-1 text-sm text-slate-500">Selamat datang di dashboard perjalananmu.</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-button border-2 border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:border-slate-300 hover:bg-slate-50 shadow-soft transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>

        {{-- Section heading + filter tabs --}}
        <div class="mt-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <h2 class="font-display text-lg font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Riwayat Pesanan
            </h2>

            {{-- Payment Status Filter --}}
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.index') }}"
                   class="inline-flex items-center px-3.5 py-2 rounded-pill text-sm font-semibold border transition-all duration-150 {{ ($paymentFilter ?? 'all') === 'all' ? 'bg-blue-600 text-white border-blue-600 shadow-soft' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300' }}">
                    Semua
                    <span class="ml-1.5 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-pill text-xs {{ ($paymentFilter ?? 'all') === 'all' ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ $allCount ?? 0 }}
                    </span>
                </a>
                <a href="{{ route('dashboard.index', ['payment_status' => 'paid']) }}"
                   class="inline-flex items-center px-3.5 py-2 rounded-pill text-sm font-semibold border transition-all duration-150 {{ ($paymentFilter ?? 'all') === 'paid' ? 'bg-green-600 text-white border-green-600 shadow-soft' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300' }}">
                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Lunas
                    <span class="ml-1.5 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-pill text-xs {{ ($paymentFilter ?? 'all') === 'paid' ? 'bg-white/25 text-white' : 'bg-green-100 text-green-700' }}">
                        {{ $paidCount ?? 0 }}
                    </span>
                </a>
                <a href="{{ route('dashboard.index', ['payment_status' => 'unpaid']) }}"
                   class="inline-flex items-center px-3.5 py-2 rounded-pill text-sm font-semibold border transition-all duration-150 {{ ($paymentFilter ?? 'all') === 'unpaid' ? 'bg-yellow-500 text-white border-yellow-500 shadow-soft' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300' }}">
                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                    Belum Lunas
                    <span class="ml-1.5 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-pill text-xs {{ ($paymentFilter ?? 'all') === 'unpaid' ? 'bg-white/25 text-white' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $unpaidCount ?? 0 }}
                    </span>
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mt-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3.5 rounded-card flex items-start gap-3 shadow-soft">
                <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('warning'))
            <div class="mt-6 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3.5 rounded-card flex items-start gap-3 shadow-soft">
                <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <p class="text-sm font-medium">{{ session('warning') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mt-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3.5 rounded-card flex items-start gap-3 shadow-soft">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <div class="flex-1">
                    @if(session('error_title'))
                        <h3 class="text-sm font-bold text-red-900">{{ session('error_title') }}</h3>
                    @endif
                    <p class="text-sm font-medium {{ session('error_title') ? 'mt-0.5' : '' }}">{{ session('error') }}</p>
                    @if(session('error_suggestion'))
                        <p class="mt-1.5 text-sm text-red-600">{{ session('error_suggestion') }}</p>
                    @endif
                    @if(session('can_retry'))
                        <div class="mt-3">
                            <button onclick="window.location.reload()" class="inline-flex items-center px-3 py-1.5 border-2 border-red-200 text-sm font-semibold rounded-button text-red-700 bg-white hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Coba Lagi
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Bookings List --}}
        <div class="mt-6 space-y-4">
            @forelse($bookings as $booking)
                @include('front.dashboard.partials.booking-card', ['booking' => $booking])
            @empty
                <div class="bg-white rounded-card border border-slate-100 shadow-card p-10 sm:p-14 text-center">
                    <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-pill bg-blue-50 text-blue-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    @if(($paymentFilter ?? 'all') === 'paid')
                        <h3 class="mt-5 font-display text-lg font-bold text-slate-900">Belum ada pesanan yang lunas</h3>
                        <p class="mt-1.5 text-sm text-slate-500 max-w-sm mx-auto">Pesanan yang sudah dibayar penuh akan muncul di sini.</p>
                    @elseif(($paymentFilter ?? 'all') === 'unpaid')
                        <h3 class="mt-5 font-display text-lg font-bold text-slate-900">Tidak ada pesanan yang belum lunas</h3>
                        <p class="mt-1.5 text-sm text-slate-500 max-w-sm mx-auto">Semua pesananmu sudah dibayar. Mantap! 🎉</p>
                    @else
                        <h3 class="mt-5 font-display text-lg font-bold text-slate-900">Belum ada pesanan</h3>
                        <p class="mt-1.5 text-sm text-slate-500 max-w-sm mx-auto">Mulai petualanganmu dengan memesan paket wisata pertama.</p>
                    @endif
                    <div class="mt-6">
                        <a href="{{ route('front.packages.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-button text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-soft transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Cari Paket Wisata
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
