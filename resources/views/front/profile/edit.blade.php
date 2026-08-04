@extends('layouts.front')

@section('title', __('front.nav_profile') . ' - EziTour')

@section('seo')
    <x-seo :title="__('front.nav_profile')" noindex />
@endsection

@section('content')
    @php($user = auth()->user())

    <div class="min-h-[calc(100vh-4rem)] bg-slate-50 py-10 sm:py-14">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Page header --}}
            <div class="mb-8">
                <h1 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">{{ __('front.nav_profile') }}</h1>
                <p class="mt-2 text-slate-600">{{ __('front.profile_subtitle') }}</p>
            </div>

            {{-- Flash success --}}
            @if (session('status') === 'profile-updated')
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-input flex items-start gap-2.5 text-sm">
                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ __('front.profile_updated_success') }}</span>
                </div>
            @elseif (session('status') === 'password-updated')
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-input flex items-start gap-2.5 text-sm">
                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ __('front.profile_password_updated_success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ============================================================
                LEFT: Avatar / identity card
                ============================================================ --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-card border border-slate-100 shadow-card overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5">
                            <h2 class="font-display text-lg font-bold text-white">{{ __('front.profile_account_title') }}</h2>
                        </div>

                        <div class="p-6 text-center">
                            {{-- Avatar or initials fallback --}}
                            @if($user->avatar_url)
                                <img class="mx-auto h-24 w-24 rounded-full object-cover ring-4 ring-white shadow-soft" src="{{ str_starts_with($user->avatar_url, 'http') ? $user->avatar_url : \Illuminate\Support\Facades\Storage::url($user->avatar_url) }}" alt="{{ $user->name }}">
                            @else
                                <span class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white font-display text-3xl font-extrabold shadow-soft">{{ $user->initials }}</span>
                            @endif

                            <h3 class="mt-4 font-display text-lg font-bold text-slate-900">{{ $user->name }}</h3>
                            <p class="mt-0.5 text-sm text-slate-500 break-all">{{ $user->email }}</p>

                            <span class="mt-3 inline-flex items-center px-2.5 py-1 rounded-pill text-xs font-semibold {{ $user->isAdmin() ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700' }}">
                                {{ $user->isAdmin() ? __('front.profile_role_admin') : __('front.profile_role_traveler') }}
                            </span>

                            <p class="mt-4 text-xs text-slate-400">{{ __('front.profile_joined', ['date' => $user->created_at->format('d/m/Y')]) }}</p>
                        </div>

                        {{-- Avatar upload form --}}
                        <div class="px-6 pb-6">
                            <form method="POST" action="{{ route('front.profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <label for="avatar" class="block text-xs font-semibold text-slate-600 mb-2">{{ __('front.profile_avatar_label') }}</label>
                                <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-button file:border-0 file:text-sm file:font-semibold file:text-white file:bg-blue-600 hover:file:bg-blue-700 file:cursor-pointer cursor-pointer border-2 border-slate-200 bg-slate-50 rounded-input @error('avatar') border-red-400 @enderror">
                                <p class="mt-1.5 text-[11px] text-slate-400">{{ __('front.profile_avatar_hint') }}</p>
                                @error('avatar')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <button type="submit" class="mt-3 w-full inline-flex justify-center items-center gap-2 py-2.5 px-4 rounded-button text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-soft transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    {{ __('front.button_upload') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ============================================================
                RIGHT: Forms
                ============================================================ --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Form 1: Profile data --}}
                    <div class="bg-white rounded-card border border-slate-100 shadow-card overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5">
                            <h2 class="font-display text-lg font-bold text-white">{{ __('front.profile_data_title') }}</h2>
                            <p class="mt-0.5 text-sm text-blue-100">{{ __('front.profile_data_subtitle') }}</p>
                        </div>

                        <div class="p-6 sm:p-7">
                            <form action="{{ route('front.profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                {{-- Name --}}
                                <div class="mb-5">
                                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('front.label_name') }}</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                        <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name', $user->name) }}" class="block w-full pl-11 pr-3.5 py-3 rounded-input border-2 border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 focus:outline-none focus:bg-white transition-colors sm:text-sm @error('name') border-red-400 bg-red-50 @enderror">
                                    </div>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="mb-5">
                                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('front.label_email') }}</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </div>
                                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email', $user->email) }}" class="block w-full pl-11 pr-3.5 py-3 rounded-input border-2 border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 focus:outline-none focus:bg-white transition-colors sm:text-sm @error('email') border-red-400 bg-red-50 @enderror">
                                    </div>
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Phone --}}
                                <div class="mb-6">
                                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('front.label_phone') }}</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </div>
                                        <input id="phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone', $user->phone) }}" placeholder="0812 3456 7890" class="block w-full pl-11 pr-3.5 py-3 rounded-input border-2 border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 focus:outline-none focus:bg-white transition-colors sm:text-sm @error('phone') border-red-400 bg-red-50 @enderror">
                                    </div>
                                    @error('phone')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-button text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-soft focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ __('front.button_save_changes') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Form 2: Change password --}}
                    <div class="bg-white rounded-card border border-slate-100 shadow-card overflow-hidden">
                        <div class="bg-gradient-to-r from-sand-500 to-sand-600 px-6 py-5">
                            <h2 class="font-display text-lg font-bold text-white">{{ __('front.profile_password_title') }}</h2>
                            <p class="mt-0.5 text-sm text-sand-50">{{ __('front.profile_password_subtitle') }}</p>
                        </div>

                        <div class="p-6 sm:p-7">
                            <form action="{{ route('front.profile.password') }}" method="POST">
                                @csrf
                                @method('PUT')

                                {{-- Current password --}}
                                <div class="mb-5">
                                    <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('front.profile_current_password_label') }}</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        </div>
                                        <input id="current_password" name="current_password" type="password" autocomplete="current-password" required class="block w-full pl-11 pr-11 py-3 rounded-input border-2 border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 focus:outline-none focus:bg-white transition-colors sm:text-sm @error('current_password') border-red-400 bg-red-50 @enderror">
                                        <button type="button" onclick="togglePasswordVisibility('current_password')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer" aria-label="Tampilkan password">
                                            <svg id="eye-icon-current_password" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg id="eye-off-icon-current_password" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- New password --}}
                                <div class="mb-5">
                                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('front.profile_new_password_label') }}</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        </div>
                                        <input id="password" name="password" type="password" autocomplete="new-password" required class="block w-full pl-11 pr-11 py-3 rounded-input border-2 border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 focus:outline-none focus:bg-white transition-colors sm:text-sm @error('password') border-red-400 bg-red-50 @enderror">
                                        <button type="button" onclick="togglePasswordVisibility('password')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer" aria-label="Tampilkan password">
                                            <svg id="eye-icon-password" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg id="eye-off-icon-password" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Confirm password --}}
                                <div class="mb-6">
                                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('front.profile_confirm_password_label') }}</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        </div>
                                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="block w-full pl-11 pr-11 py-3 rounded-input border-2 border-slate-200 bg-slate-50 text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 focus:outline-none focus:bg-white transition-colors sm:text-sm">
                                        <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer" aria-label="Tampilkan password">
                                            <svg id="eye-icon-password_confirmation" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg id="eye-off-icon-password_confirmation" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" class="w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-button text-sm font-bold text-white bg-gradient-to-r from-sand-500 to-sand-600 hover:from-sand-600 hover:to-sand-700 shadow-soft focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sand-500 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    {{ __('front.profile_password_update_button') }}
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Password visibility toggle (same pattern as auth/login + auth/register) --}}
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
