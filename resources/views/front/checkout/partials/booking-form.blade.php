{{-- Booking Form Section --}}
{{-- Requirements: 1.4, 5.1, 5.2 --}}
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
    <!-- Header with gradient -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
        <h2 class="text-xl font-bold text-white flex items-center">
            <svg class="w-6 h-6 mr-3 text-white" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            Detail Pemesanan
        </h2>
    </div>

    <div class="p-6">
        <form id="booking-form" action="{{ route('front.checkout.store', $package->slug) }}" method="POST">
            @csrf
            
            <div class="space-y-5">
                <!-- Travel Date -->
                <div>
                    <label for="travel_date" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal Perjalanan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input 
                            type="date" 
                            name="travel_date" 
                            id="travel_date" 
                            class="block w-full pl-10 rounded-xl border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-gray-50 p-3.5 transition-colors hover:border-gray-300 @error('travel_date') border-red-400 bg-red-50 @enderror" 
                            required 
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            value="{{ old('travel_date', $pendingBooking['travel_date'] ?? '') }}"
                        >
                    </div>
                    @error('travel_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Participants -->
                <div>
                    <label for="participants" class="block text-sm font-semibold text-gray-700 mb-2">
                        Jumlah Peserta <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <input 
                            type="number" 
                            name="participants" 
                            id="participants" 
                            min="1" 
                            max="50"
                            value="{{ old('participants', $pendingBooking['participants'] ?? 1) }}" 
                            class="block w-full pl-10 rounded-xl border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-gray-50 p-3.5 transition-colors hover:border-gray-300 @error('participants') border-red-400 bg-red-50 @enderror" 
                            required
                        >
                    </div>
                    @error('participants')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">Maksimal 50 peserta per pemesanan</p>
                </div>

                <!-- Pickup Location -->
                <div>
                    <label for="pickup_location" class="block text-sm font-semibold text-gray-700 mb-2">
                        Lokasi Penjemputan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute top-3 left-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <textarea 
                            name="pickup_location" 
                            id="pickup_location" 
                            rows="3" 
                            class="block w-full pl-10 rounded-xl border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-gray-50 p-3.5 transition-colors hover:border-gray-300 resize-none @error('pickup_location') border-red-400 bg-red-50 @enderror" 
                            required 
                            placeholder="Contoh: Hotel Tentrem Yogyakarta, Jl. AM Sangaji No.72A..."
                        >{{ old('pickup_location', $pendingBooking['pickup_location'] ?? '') }}</textarea>
                    </div>
                    @error('pickup_location')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price Breakdown (Real-time calculation) - Improved Card -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100">
                    <h4 class="text-sm font-bold text-blue-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Rincian Harga
                    </h4>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center text-blue-800">
                            <span>Harga per orang</span>
                            <span class="font-semibold">Rp {{ number_format($package->total_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-blue-800">
                            <span>Jumlah peserta</span>
                            <span class="font-semibold bg-blue-100 px-3 py-0.5 rounded-full" id="display-participants">{{ old('participants', $pendingBooking['participants'] ?? 1) }}</span>
                        </div>
                        <div class="border-t border-blue-200 pt-3 mt-3">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-blue-900 text-base">Total Harga</span>
                                <span class="text-xl font-bold text-blue-600" id="display-total-price">Rp {{ number_format($package->total_price * (old('participants', $pendingBooking['participants'] ?? 1)), 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            @if($isAuthenticated)
                <button 
                    type="submit" 
                    class="mt-6 w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-xl shadow-lg text-base font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
                >
                    Lanjutkan ke Pembayaran →
                </button>
            @else
                <button 
                    type="submit" 
                    class="mt-6 w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-xl shadow-lg text-base font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
                >
                    Lanjutkan →
                </button>
                <p class="mt-3 text-xs text-center text-gray-500">
                    Anda akan diminta untuk login atau mendaftar setelah ini
                </p>
            @endif
        </form>
    </div>
</div>
