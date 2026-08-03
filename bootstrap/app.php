<?php

use App\Http\Middleware\WebhookRateLimiter;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        // Reminder jobs (Sprint 8) — both gated by idempotency columns on
        // bookings, so an hourly tick (or overlap) never double-sends.
        $schedule->command('reminders:trip')
            ->hourly()
            ->withoutOverlapping()
            ->onOneServer();

        $schedule->command('reminders:payment-expiry')
            ->hourly()
            ->withoutOverlapping()
            ->onOneServer();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        // Exempt Midtrans webhook from CSRF verification
        // This is required because Midtrans sends POST requests without CSRF token
        $middleware->validateCsrfTokens(except: [
            'midtrans/notification',
        ]);

        // Register webhook rate limiter middleware alias
        $middleware->alias([
            'webhook.ratelimit' => WebhookRateLimiter::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
