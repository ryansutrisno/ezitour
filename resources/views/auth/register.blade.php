@extends('layouts.front')

@section('title', 'Daftar - EziTour')

@section('content')
<div class="min-h-[calc(100vh-4rem)] grid lg:grid-cols-2">

    {{-- Left: branding panel --}}
    <div class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-gradient-to-br from-blue-700 via-blue-700 to-blue-800 p-12 text-white">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-blue-500/30 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-sand-400/20 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgb(255_255_255/0.08)_1px,transparent_0)] [background-size:24px_24px]"></div>
        </div>

        <a href="{{ route('front.home') }}" class="relative flex items-center gap-2.5">
            <span class="flex items-center justify-center w-9 h-9 rounded-button bg-white/15 backdrop-blur">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </span>
            <span class="text-xl font-bold font-display tracking-tight">EziTour</span>
        </a>

        <div class="relative">
            <h2 class="font-display text-4xl font-extrabold leading-tight">Gabung &amp; mulai<br>petualanganmu.</h2>
            <p class="mt-4 text-blue-100 max-w-md leading-relaxed">Buat akun gratis untuk memesan paket, mengelola pesanan, dan mendapatkan penawaran eksklusif.</p>

            <ul class="mt-8 space-y-3.5">
                <li class="flex items-center gap-3 text-blue-50">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-white/15"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                    <span class="text-sm">Pendaftaran gratis, tanpa biaya</span>
                </li>
                <li class="flex items-center gap-3 text-blue-50">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-white/15"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                    <span class="text-sm">Akses ke 500+ destinasi wisata</span>
                </li>
                <li class="flex items-center gap-3 text-blue-50">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-white/15"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></span>
                    <span class="text-sm">Pembayaran aman lewat Midtrans</span>
                </li>
            </ul>
        </div>

        <p class="relative text-sm text-blue-200">&copy; {{ date('Y') }} EziTour. Powered by Trazmedia Segoro Digital.</p>
    </div>

    {{-- Right: form --}}
    <div class="flex flex-col justify-center px-4 sm:px-6 lg:px-16 py-12 bg-slate-50">
        <div class="w-full max-w-md mx-auto">

            <div class="lg:hidden flex items-center gap-2.5 mb-8">
                <span class="flex items-center justify-center w-9 h-9 rounded-button bg-gradient-to-br from-blue-600 to-blue-700">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
                <span class="text-xl font-bold font-display text-slate-900 tracking-tight">Ezi<span class="text-blue-600">Tour</span></span>
            </div>

            <div class="bg-white rounded-card border border-slate-100 shadow-card overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5">
                    <h1 class="font-display text-xl font-bold text-white">Buat akun baru 🚀</h1>
                    <p class="mt-1 text-sm text-blue-100">Hanya butuh sebentar, lalu langsung jalan.</p>
                </div>

                <div class="p-6 sm:p-7">
                    @if (session('error'))
                        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-input flex items-start gap-2.5 text-sm">
                            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <form class="space-y-5" action="{{ route('register.store') }}" method="POST">
                        @csrf

                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}" placeholder="Nama lengkap Anda" class="block w-full pl-11 pr-3.5 py-3 rounded-input border-2 border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 focus:outline-none focus:bg-white transition-colors sm:text-sm @error('name') border-red-400 bg-red-50 @enderror">
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" placeholder="email@example.com" class="block w-full pl-11 pr-3.5 py-3 rounded-input border-2 border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 focus:outline-none focus:bg-white transition-colors sm:text-sm @error('email') border-red-400 bg-red-50 @enderror">
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input id="password" name="password" type="password" autocomplete="new-password" required placeholder="Min. 8 karakter" class="block w-full pl-11 pr-11 py-3 rounded-input border-2 border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 focus:outline-none focus:bg-white transition-colors sm:text-sm @error('password') border-red-400 bg-red-50 @enderror">
                                <button type="button" onclick="togglePasswordVisibility('password')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer" aria-label="Tampilkan password">
                                    <svg id="eye-icon-password" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg id="eye-off-icon-password" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password Confirmation --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required placeholder="Ulangi password" class="block w-full pl-11 pr-11 py-3 rounded-input border-2 border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 focus:outline-none focus:bg-white transition-colors sm:text-sm">
                                <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer" aria-label="Tampilkan password">
                                    <svg id="eye-icon-password_confirmation" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg id="eye-off-icon-password_confirmation" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="w-full flex justify-center items-center py-3.5 px-4 rounded-button text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-soft focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                            Daftar Sekarang
                        </button>
                    </form>

                    <div class="mt-5 flex items-center justify-center gap-2 text-xs text-slate-400">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Pendaftaran aman &amp; terenkripsi
                    </div>
                </div>
            </div>

            <p class="mt-6 text-center text-sm text-slate-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(id) {
        var input = document.getElementById(id);
        var eyeIcon = document.getElementById('eye-icon-' + id);
        var eyeOffIcon = document.getElementById('eye-off-icon-' + id);

        if (input.type === 'password') {
            input.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeOffIcon.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeOffIcon.classList.add('hidden');
        }
    }
</script>
@endsection
