<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BookingCreationService;
use App\Services\CheckoutSessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class RegisterController extends Controller
{
    public function __construct(
        private CheckoutSessionService $checkoutSessionService,
        private BookingCreationService $bookingCreationService
    ) {}

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        // Check if there's a pending booking from checkout flow
        if ($this->checkoutSessionService->hasPendingBooking()) {
            return $this->processPendingBooking();
        }

        return redirect(route('dashboard.index'));
    }

    /**
     * Process pending booking after successful registration.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function processPendingBooking()
    {
        try {
            $booking = $this->bookingCreationService->createFromSession(Auth::user());

            if ($booking) {
                return redirect()->route('payments.create', $booking)
                    ->with('success', 'Akun berhasil dibuat dan booking berhasil dibuat! Silakan lanjutkan ke pembayaran.');
            }

            // Session data was invalid or package not found
            return redirect()->route('dashboard.index')
                ->with('warning', 'Sesi checkout telah berakhir. Silakan mulai pemesanan kembali.');
        } catch (Exception $e) {
            Log::error('Failed to create booking from session after registration', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            $this->checkoutSessionService->clear();

            return redirect()->route('dashboard.index')
                ->with('error', 'Terjadi kesalahan saat membuat booking. Silakan coba lagi.');
        }
    }
}
