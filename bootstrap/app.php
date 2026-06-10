<?php

use App\Http\Middleware\WebhookRateLimiter;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
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
