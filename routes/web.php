<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Front\BookingController;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Front\DashboardController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\LocaleController;
use App\Http\Controllers\Front\MidtransNotificationController;
use App\Http\Controllers\Front\PackageController;
use App\Http\Controllers\Front\PaymentController;
use App\Http\Controllers\Front\ProfileController;
use App\Http\Controllers\Front\ReviewController;
use App\Http\Controllers\Front\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Locale-aware routing (Phase 1 i18n)
|--------------------------------------------------------------------------
| Indonesian (default) lives at the root URL with NO prefix. English gets an
| `/en/` prefix. Both versions share the exact same controllers and route
| definitions via the $localeRoutes closure below.
|
| Implementation notes:
|  • ID routes use the original names (`front.home`, `front.packages.show`,
|    etc.) so every existing route() call keeps producing root paths.
|  • EN routes are registered with `->name('en.')` so they live under names
|    like `en.front.home`. App\Routing\UrlGenerator::route() transparently
|    switches between the two namespaces based on the active app locale,
|    which means `route('front.home')` returns `/` for ID and `/en/` for EN
|    without any blade-level changes.
|  • The locale switch endpoint and Midtrans webhook are intentionally kept
|    OUTSIDE both groups — the switcher must work from any locale, and the
|    webhook path is hard-coded in the Midtrans dashboard.
*/
$localeRoutes = function (): void {
    Route::get('/', [HomeController::class, 'index'])->name('front.home');
    Route::get('/tentang', [HomeController::class, 'about'])->name('front.about');
    Route::get('/faq', [HomeController::class, 'faq'])->name('front.faq');
    Route::get('/packages', [PackageController::class, 'index'])->name('front.packages.index');
    Route::get('/packages/{slug}', [PackageController::class, 'show'])->name('front.packages.show');

    // Checkout Routes (accessible by both guests and authenticated users)
    Route::get('/checkout/{slug}', [CheckoutController::class, 'show'])->name('front.checkout.show');
    Route::post('/checkout/{slug}', [CheckoutController::class, 'store'])->name('front.checkout.store');
    Route::post('/checkout/{slug}/login', [CheckoutController::class, 'login'])->name('front.checkout.login');
    Route::post('/checkout/{slug}/register', [CheckoutController::class, 'register'])->name('front.checkout.register');

    // Auth Routes
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.store');
        Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [RegisterController::class, 'register'])->name('register.store');
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Protected Routes
    Route::middleware('auth')->group(function (): void {
        Route::post('/packages/{slug}/book', [BookingController::class, 'store'])->name('front.booking.store');
        Route::post('/packages/{slug}/reviews', [ReviewController::class, 'store'])->name('front.reviews.store');
        Route::post('/checkout/{slug}/coupon', [CheckoutController::class, 'applyCoupon'])->middleware('webhook.ratelimit')->name('front.checkout.coupon');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Booking detail / E-ticket / cancel
        Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
        Route::get('/bookings/{booking}/ticket', [BookingController::class, 'ticket'])->name('bookings.ticket');
        Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

        // Profile Routes
        Route::get('/profile', [ProfileController::class, 'edit'])->name('front.profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('front.profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('front.profile.password');

        // Payment Routes
        Route::post('/payments/{booking}/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments/{booking}/retry', [PaymentController::class, 'retry'])->name('payments.retry');
        Route::get('/payments/finish', [PaymentController::class, 'finish'])->name('payments.finish');
        Route::get('/payments/unfinish', [PaymentController::class, 'unfinish'])->name('payments.unfinish');
        Route::get('/payments/error', [PaymentController::class, 'error'])->name('payments.error');

        // Wishlist (heart toggle + remove)
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('front.wishlist.index');
        Route::delete('/wishlist/{slug}', [WishlistController::class, 'destroy'])->name('front.wishlist.destroy');
    });

    // Wishlist AJAX toggle — separate from the auth group so it carries its own
    // webhook.ratelimit middleware (matches the coupon endpoint pattern).
    Route::post('/packages/{slug}/wishlist', [WishlistController::class, 'toggle'])
        ->middleware(['auth', 'webhook.ratelimit'])
        ->name('front.wishlist.toggle');
};

// Indonesian (default, root paths) — registered FIRST so the nameserver
// entry points to it. Existing route() calls produce root URLs unchanged.
$localeRoutes();

// English mirror — `en.` name namespace keeps the two registrations from
// clashing. UrlGenerator::route() picks the right namespace per request.
Route::prefix('en')->middleware('locale')->name('en.')->group($localeRoutes);

// Locale switch endpoint — sits OUTSIDE both locale groups so the literal
// `/locale/` path is never shadowed by the {locale} URL prefix. The
// controller persists the preference and redirects to the localized
// equivalent of whatever URL the user came from.
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])
    ->middleware('locale')
    ->name('front.locale.switch');

// Midtrans Webhook Route (no auth, CSRF exempted via bootstrap/app.php)
// Rate limited to prevent abuse (Requirements: 4.2)
// Kept OUTSIDE the locale groups — Midtrans callbacks always post to the
// root /midtrans/notification path regardless of any active locale.
Route::post('/midtrans/notification', [MidtransNotificationController::class, 'handle'])
    ->middleware('webhook.ratelimit')
    ->name('midtrans.notification');
