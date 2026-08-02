<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Front\BookingController;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Front\DashboardController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\MidtransNotificationController;
use App\Http\Controllers\Front\PackageController;
use App\Http\Controllers\Front\PaymentController;
use App\Http\Controllers\Front\ProfileController;
use App\Http\Controllers\Front\ReviewController;
use Illuminate\Support\Facades\Route;

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
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.store');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::post('/packages/{slug}/book', [BookingController::class, 'store'])->name('front.booking.store');
    Route::post('/packages/{slug}/reviews', [ReviewController::class, 'store'])->name('front.reviews.store');
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
});

// Midtrans Webhook Route (no auth, CSRF exempted via bootstrap/app.php)
// Rate limited to prevent abuse (Requirements: 4.2)
Route::post('/midtrans/notification', [MidtransNotificationController::class, 'handle'])
    ->middleware('webhook.ratelimit')
    ->name('midtrans.notification');
