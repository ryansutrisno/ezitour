{{-- Auth Section with Login/Register Tabs --}}
{{-- Requirements: 3.1, 3.4 --}}
@php
    $showAuth = session('show_auth') || $pendingBooking;
    $activeTab = session('active_tab', 'login');
@endphp

<div id="auth-section" class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden {{ !$showAuth ? 'hidden' : '' }}">
    <!-- Header with gradient -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
        <h2 class="text-xl font-bold text-white flex items-center">
            <span class="flex items-center justify-center w-8 h-8 bg-white/20 text-white rounded-full mr-3 text-sm font-bold">2</span>
            Login atau Daftar
        </h2>
    </div>

    <div class="p-6">
        <!-- Tab Navigation - Pill Style -->
        <div class="bg-gray-100 p-1 rounded-xl mb-6">
            <nav class="flex" aria-label="Tabs">
                <button 
                    type="button"
                    onclick="switchTab('login')"
                    id="tab-login"
                    class="tab-button flex-1 py-2.5 px-4 rounded-lg text-sm font-semibold transition-all duration-200 {{ $activeTab === 'login' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}"
                >
                    Login
                </button>
                <button 
                    type="button"
                    onclick="switchTab('register')"
                    id="tab-register"
                    class="tab-button flex-1 py-2.5 px-4 rounded-lg text-sm font-semibold transition-all duration-200 {{ $activeTab === 'register' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}"
                >
                    Daftar Baru
                </button>
            </nav>
        </div>

    <!-- Login Form -->
    <div id="login-form-container" class="{{ $activeTab !== 'login' ? 'hidden' : '' }}">
        <form action="{{ route('front.checkout.login', $package->slug) }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <!-- Email -->
                <div>
                    <label for="login_email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input 
                            type="email" 
                            name="email" 
                            id="login_email" 
                            class="block w-full pl-10 rounded-xl border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-gray-50 p-3.5 transition-colors hover:border-gray-300 @error('login_email') border-red-400 bg-red-50 @enderror" 
                            required
                            value="{{ old('email') }}"
                            placeholder="email@example.com"
                        >
                    </div>
                    @error('login_email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="login_password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            id="login_password" 
                            class="block w-full pl-10 pr-10 rounded-xl border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-gray-50 p-3.5 transition-colors hover:border-gray-300" 
                            required
                            placeholder="Masukkan password"
                        >
                        <button type="button" onclick="togglePasswordVisibility('login_password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none z-10 cursor-pointer">
                            <svg id="eye-icon-login_password" class="w-5 h-5" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-off-icon-login_password" class="w-5 h-5 hidden" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <button 
                type="submit" 
                class="mt-6 w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-xl shadow-lg text-base font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
            >
                Login & Lanjutkan →
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                Belum punya akun? 
                <button type="button" onclick="switchTab('register')" class="text-blue-600 hover:text-blue-700 font-semibold hover:underline">
                    Daftar sekarang
                </button>
            </p>
        </div>
    </div>

    <!-- Register Form -->
    <div id="register-form-container" class="{{ $activeTab !== 'register' ? 'hidden' : '' }}">
        <form action="{{ route('front.checkout.register', $package->slug) }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <!-- Name -->
                <div>
                    <label for="register_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="name" 
                            id="register_name" 
                            class="block w-full pl-10 rounded-xl border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-gray-50 p-3.5 transition-colors hover:border-gray-300 @error('register_name') border-red-400 bg-red-50 @enderror" 
                            required
                            value="{{ old('name') }}"
                            placeholder="Nama lengkap Anda"
                        >
                    </div>
                    @error('register_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="register_email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input 
                            type="email" 
                            name="email" 
                            id="register_email" 
                            class="block w-full pl-10 rounded-xl border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-gray-50 p-3.5 transition-colors hover:border-gray-300 @error('register_email') border-red-400 bg-red-50 @enderror" 
                            required
                            value="{{ old('email') }}"
                            placeholder="email@example.com"
                        >
                    </div>
                    @error('register_email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone (Optional) -->
                <div>
                    <label for="register_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nomor Telepon <span class="text-gray-400 text-xs font-normal">(opsional)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <input 
                            type="tel" 
                            name="phone" 
                            id="register_phone" 
                            class="block w-full pl-10 rounded-xl border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-gray-50 p-3.5 transition-colors hover:border-gray-300 @error('register_phone') border-red-400 bg-red-50 @enderror" 
                            value="{{ old('phone') }}"
                            placeholder="08xxxxxxxxxx"
                        >
                    </div>
                    @error('register_phone')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Fields in Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Password -->
                    <div>
                        <label for="register_password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input 
                                type="password" 
                                name="password" 
                                id="register_password" 
                                class="block w-full pl-10 pr-10 rounded-xl border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-gray-50 p-3.5 transition-colors hover:border-gray-300 @error('register_password') border-red-400 bg-red-50 @enderror" 
                                required
                                placeholder="Min. 8 karakter"
                            >
                            <button type="button" onclick="togglePasswordVisibility('register_password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none z-10 cursor-pointer">
                                <svg id="eye-icon-register_password" class="w-5 h-5" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eye-off-icon-register_password" class="w-5 h-5 hidden" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('register_password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="register_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                            Konfirmasi <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                id="register_password_confirmation" 
                                class="block w-full pl-10 pr-10 rounded-xl border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-gray-50 p-3.5 transition-colors hover:border-gray-300" 
                                required
                                placeholder="Ulangi password"
                            >
                            <button type="button" onclick="togglePasswordVisibility('register_password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none z-10 cursor-pointer">
                                <svg id="eye-icon-register_password_confirmation" class="w-5 h-5" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eye-off-icon-register_password_confirmation" class="w-5 h-5 hidden" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <p id="password-match-error" class="mt-2 text-sm text-red-600 hidden">Password dan konfirmasi tidak cocok</p>
                    </div>
                </div>
            </div>

            <button 
                type="submit" 
                id="register-submit-btn"
                class="mt-6 w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-xl shadow-lg text-base font-semibold text-white bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Daftar & Lanjutkan →
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                Sudah punya akun? 
                <button type="button" onclick="switchTab('login')" class="text-blue-600 hover:text-blue-700 font-semibold hover:underline">
                    Login di sini
                </button>
            </p>
        </div>
    </div>
    </div>
</div>

@push('scripts')
<script>
    function switchTab(tab) {
        // Update tab buttons with new pill style
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
            btn.classList.add('text-gray-500');
        });
        
        const activeTabBtn = document.getElementById('tab-' + tab);
        activeTabBtn.classList.remove('text-gray-500');
        activeTabBtn.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
        
        // Update form containers
        document.getElementById('login-form-container').classList.toggle('hidden', tab !== 'login');
        document.getElementById('register-form-container').classList.toggle('hidden', tab !== 'register');
    }

    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const eyeIcon = document.getElementById('eye-icon-' + inputId);
        const eyeOffIcon = document.getElementById('eye-off-icon-' + inputId);
        
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

    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('register_password');
        const confirmInput = document.getElementById('register_password_confirmation');
        const errorMsg = document.getElementById('password-match-error');
        const submitBtn = document.getElementById('register-submit-btn');

        function validatePasswordMatch() {
            const password = passwordInput.value;
            const confirm = confirmInput.value;

            if (confirm.length > 0) {
                if (password !== confirm) {
                    errorMsg.classList.remove('hidden');
                    submitBtn.disabled = true;
                } else {
                    errorMsg.classList.add('hidden');
                    submitBtn.disabled = false;
                }
            } else {
                errorMsg.classList.add('hidden');
                submitBtn.disabled = false;
            }
        }

        passwordInput.addEventListener('input', validatePasswordMatch);
        confirmInput.addEventListener('input', validatePasswordMatch);
    });
</script>
@endpush
