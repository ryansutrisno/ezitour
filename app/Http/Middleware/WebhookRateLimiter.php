<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * WebhookRateLimiter - Rate limiting middleware for webhook endpoints
 *
 * This middleware limits the number of notification requests per IP address
 * to prevent abuse and potential DDoS attacks on the webhook endpoint.
 *
 * Requirements: 4.2 - Limit notification requests per IP and log excessive requests
 */
class WebhookRateLimiter
{
    /**
     * The rate limiter instance.
     */
    protected RateLimiter $limiter;

    /**
     * Maximum number of requests allowed per minute per IP
     */
    protected int $maxAttempts = 60;

    /**
     * Decay time in seconds (1 minute)
     */
    protected int $decaySeconds = 60;

    /**
     * Create a new middleware instance.
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $this->maxAttempts)) {
            $this->logExcessiveRequests($request, $key);

            return $this->buildTooManyRequestsResponse($key);
        }

        $this->limiter->hit($key, $this->decaySeconds);

        $response = $next($request);

        return $this->addRateLimitHeaders($response, $key);
    }

    /**
     * Resolve the request signature for rate limiting.
     * Uses IP address as the unique identifier.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        return 'webhook_rate_limit:'.$request->ip();
    }

    /**
     * Log excessive requests from an IP address.
     *
     * Requirements: 4.2 - Log excessive requests
     */
    protected function logExcessiveRequests(Request $request, string $key): void
    {
        Log::channel('payment')->warning('Webhook rate limit exceeded', [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'endpoint' => $request->path(),
            'remaining_attempts' => $this->limiter->remaining($key, $this->maxAttempts),
            'retry_after_seconds' => $this->limiter->availableIn($key),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Build the response for too many requests.
     */
    protected function buildTooManyRequestsResponse(string $key): Response
    {
        $retryAfter = $this->limiter->availableIn($key);

        return response()->json([
            'status' => 'error',
            'message' => 'Too many requests. Please try again later.',
            'retry_after' => $retryAfter,
        ], 429)->withHeaders([
            'Retry-After' => $retryAfter,
            'X-RateLimit-Limit' => $this->maxAttempts,
            'X-RateLimit-Remaining' => 0,
        ]);
    }

    /**
     * Add rate limit headers to the response.
     */
    protected function addRateLimitHeaders(Response $response, string $key): Response
    {
        $remaining = $this->limiter->remaining($key, $this->maxAttempts);

        $response->headers->add([
            'X-RateLimit-Limit' => $this->maxAttempts,
            'X-RateLimit-Remaining' => max(0, $remaining - 1),
        ]);

        return $response;
    }
}
