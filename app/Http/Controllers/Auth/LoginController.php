<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\BookingCreationService;
use App\Services\CheckoutSessionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function __construct(
        private CheckoutSessionService $checkoutSessionService,
        private BookingCreationService $bookingCreationService
    ) {}

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Check if there's a pending booking from checkout flow
            if ($this->checkoutSessionService->hasPendingBooking()) {
                return $this->processPendingBooking();
            }

            return redirect()->intended(route('dashboard.index'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Process pending booking after successful authentication.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function processPendingBooking()
    {
        try {
            $booking = $this->bookingCreationService->createFromSession(Auth::user());

            if ($booking) {
                return redirect()->route('payments.create', $booking)
                    ->with('success', 'Booking berhasil dibuat! Silakan lanjutkan ke pembayaran.');
            }

            // Session data was invalid or package not found
            return redirect()->route('dashboard.index')
                ->with('warning', 'Sesi checkout telah berakhir. Silakan mulai pemesanan kembali.');
        } catch (Exception $e) {
            Log::error('Failed to create booking from session after login', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            $this->checkoutSessionService->clear();

            return redirect()->route('dashboard.index')
                ->with('error', 'Terjadi kesalahan saat membuat booking. Silakan coba lagi.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
