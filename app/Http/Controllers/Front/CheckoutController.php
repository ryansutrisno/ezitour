<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\User;
use App\Services\BookingCreationService;
use App\Services\CheckoutSessionService;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutSessionService $checkoutSessionService,
        private BookingCreationService $bookingCreationService,
        private CouponService $couponService
    ) {}

    /**
     * Display the checkout page for a package.
     * Shows booking form and auth section (if guest).
     *
     * Requirements: 1.1, 1.3
     * - Display package details, booking form, and auth section (if guest)
     * - Hide auth section for authenticated users
     */
    public function show(string $slug): View
    {
        $package = Package::where('slug', $slug)
            ->with(['destinations', 'items.destination'])
            ->firstOrFail();

        // Get pending booking data if exists (for repopulating form after failed auth)
        $pendingBooking = $this->checkoutSessionService->retrieve();

        return view('front.checkout.index', [
            'package' => $package,
            'isAuthenticated' => Auth::check(),
            'user' => Auth::user(),
            'pendingBooking' => $pendingBooking,
        ]);
    }

    /**
     * Process booking form submission.
     * For guests: store in session and show auth forms.
     * For authenticated: create booking and redirect to payment.
     *
     * Requirements: 2.1, 4.1, 4.3
     * - Store booking data in session for guests
     * - Create booking and redirect to payment for authenticated users
     * - Validate all booking data before processing
     */
    public function store(Request $request, string $slug): RedirectResponse
    {
        $package = Package::where('slug', $slug)->firstOrFail();

        // Validate booking form data
        $validated = $request->validate([
            'travel_date' => 'required|date|after:today',
            'participants' => 'required|integer|min:1|max:50',
            'pickup_location' => 'required|string|max:500',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        // Calculate tier-aware subtotal (used for coupon validation threshold).
        $pricing = $package->calculatePricing((int) $validated['participants']);

        // Resolve coupon (if provided) — re-validate server-side for defense in depth.
        // Only authenticated users can use coupons; guests go through auth first.
        $couponId = null;
        $couponDiscount = 0.0;
        $couponModel = null;

        $user = $request->user();
        if (! empty($validated['coupon_code']) && $user) {
            $result = $this->couponService->validate(
                $validated['coupon_code'],
                $pricing['subtotal'],
                $user
            );

            if (! $result['valid']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Promo: '.$result['error']);
            }

            $couponId = $result['coupon']->id;
            $couponDiscount = $result['discount'];
            $couponModel = $result['coupon'];
        }

        // If user is authenticated, create booking directly
        if ($user) {
            try {
                $booking = $this->bookingCreationService->createBooking(
                    $user,
                    $package,
                    $validated,
                    $couponId,
                    $couponDiscount
                );

                // Increment coupon usage AFTER booking is committed.
                if ($couponModel) {
                    $this->couponService->incrementUsage($couponModel, $user);
                }

                return redirect()->route('payments.create', $booking)
                    ->with('success', 'Booking berhasil dibuat! Silakan lanjutkan pembayaran.');
            } catch (\Exception $e) {
                \Log::error('Failed to create booking for authenticated user', [
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal membuat booking. Silakan coba lagi. Error: '.$e->getMessage());
            }
        }

        // For guest users, store booking data in session
        $this->checkoutSessionService->store(
            array_merge($validated, ['total_amount' => max(0, $pricing['subtotal'] - $couponDiscount)]),
            $package->id,
            $package->slug
        );

        // Redirect back to checkout page to show auth forms
        return redirect()->route('front.checkout.show', $slug)
            ->with('show_auth', true)
            ->with('info', 'Silakan login atau daftar untuk melanjutkan pemesanan.');
    }

    /**
     * AJAX coupon validation endpoint.
     * Returns JSON with discount breakdown for real-time checkout price update.
     */
    public function applyCoupon(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'participants' => 'required|integer|min:1|max:50',
        ]);

        $package = Package::where('slug', $slug)->firstOrFail();

        $pricing = $package->calculatePricing((int) $request->participants);

        $result = $this->couponService->validate(
            $request->code,
            $pricing['subtotal'],
            $request->user()
        );

        if (! $result['valid']) {
            return response()->json([
                'valid' => false,
                'error' => $result['error'],
            ]);
        }

        $discount = $result['discount'];
        $totalAfter = max(0.0, $pricing['subtotal'] - $discount);

        return response()->json([
            'valid' => true,
            'error' => null,
            'discount' => $discount,
            'formatted_discount' => 'Rp '.number_format($discount, 0, ',', '.'),
            'total_after' => $totalAfter,
            'formatted_total' => 'Rp '.number_format($totalAfter, 0, ',', '.'),
        ]);
    }

    /**
     * Handle inline login on checkout page.
     * Authenticates user and processes pending booking.
     *
     * Requirements: 3.2, 3.4, 3.5
     * - Authenticate user without full page redirect
     * - Display error messages inline without losing booking form data
     * - Proceed to create booking and redirect to payment on success
     */
    public function login(Request $request, string $slug): RedirectResponse
    {
        // Validate login credentials
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt authentication
        if (! Auth::attempt($credentials)) {
            return redirect()->route('front.checkout.show', $slug)
                ->withErrors(['login_email' => 'Email atau password salah.'])
                ->withInput($request->only('email'))
                ->with('show_auth', true)
                ->with('active_tab', 'login');
        }

        // Regenerate session for security
        $request->session()->regenerate();

        // Check for pending booking and create it
        if ($this->checkoutSessionService->hasPendingBooking()) {
            try {
                $booking = $this->bookingCreationService->createFromSession(Auth::user());

                if ($booking) {
                    return redirect()->route('payments.create', $booking)
                        ->with('success', 'Login berhasil! Silakan lanjutkan pembayaran.');
                }
            } catch (\Exception $e) {
                return redirect()->route('front.checkout.show', $slug)
                    ->with('error', 'Gagal membuat booking. Silakan coba lagi.');
            }
        }

        // No pending booking, redirect to dashboard
        return redirect()->route('dashboard.index')
            ->with('success', 'Login berhasil!');
    }

    /**
     * Handle inline registration on checkout page.
     * Creates user, logs in, and processes pending booking.
     *
     * Requirements: 3.3, 3.4, 3.5
     * - Create account and log user in automatically
     * - Display error messages inline without losing booking form data
     * - Proceed to create booking and redirect to payment on success
     */
    public function register(Request $request, string $slug): RedirectResponse
    {
        // Validate registration data
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'phone' => ['nullable', 'string', 'max:20'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Add prefix to error keys to distinguish from login errors
            $errors = [];
            foreach ($e->errors() as $key => $messages) {
                $errors['register_'.$key] = $messages;
            }

            return redirect()->route('front.checkout.show', $slug)
                ->withErrors($errors)
                ->withInput($request->only('name', 'email', 'phone'))
                ->with('show_auth', true)
                ->with('active_tab', 'register');
        }

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
        ]);

        // Auto-login the new user
        Auth::login($user);

        // Regenerate session for security
        $request->session()->regenerate();

        // Check for pending booking and create it
        if ($this->checkoutSessionService->hasPendingBooking()) {
            try {
                $booking = $this->bookingCreationService->createFromSession($user);

                if ($booking) {
                    return redirect()->route('payments.create', $booking)
                        ->with('success', 'Registrasi berhasil! Silakan lanjutkan pembayaran.');
                }
            } catch (\Exception $e) {
                return redirect()->route('front.checkout.show', $slug)
                    ->with('error', 'Gagal membuat booking. Silakan coba lagi.');
            }
        }

        // No pending booking, redirect to dashboard
        return redirect()->route('dashboard.index')
            ->with('success', 'Registrasi berhasil! Selamat datang.');
    }
}
