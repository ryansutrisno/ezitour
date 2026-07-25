<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\User;
use App\Services\MidtransClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Mockery;
use Tests\TestCase;

/**
 * Feature: payment-integration
 * Task 19.2: Add rate limiting to webhook endpoint
 *
 * Tests untuk memverifikasi:
 * - Rate limiting pada webhook endpoint
 * - Logging excessive requests
 * - Proper response headers
 *
 * Requirements: 4.2
 */
class WebhookRateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Package $package;

    protected Booking $booking;

    protected Transaction $transaction;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear rate limiter before each test
        RateLimiter::clear('webhook_rate_limit:127.0.0.1');

        // Create test data
        $this->user = User::factory()->create();

        $this->package = Package::create([
            'name' => 'Test Package',
            'slug' => 'test-package',
            'description' => 'Test package description',
            'total_price' => 1000000,
        ]);

        $this->booking = Booking::create([
            'user_id' => $this->user->id,
            'package_id' => $this->package->id,
            'travel_date' => now()->addDays(7),
            'total_amount' => 1000000,
            'status' => 'pending',
            'pickup_location' => 'Test Location',
        ]);

        $this->transaction = Transaction::create([
            'booking_id' => $this->booking->id,
            'order_id' => 'BOOK-'.$this->booking->id.'-'.time().'-test',
            'snap_token' => 'test-snap-token',
            'gross_amount' => $this->booking->total_amount,
            'transaction_status' => Transaction::STATUS_PENDING,
            'transaction_time' => now(),
        ]);
    }

    /**
     * Test webhook request includes rate limit headers
     */
    public function test_webhook_response_includes_rate_limit_headers(): void
    {
        // Mock MidtransClient
        $mockClient = Mockery::mock(MidtransClient::class);
        $mockClient->shouldReceive('verifySignature')->andReturn(true);
        $this->app->instance(MidtransClient::class, $mockClient);

        $notification = [
            'order_id' => $this->transaction->order_id,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => number_format($this->transaction->gross_amount, 2, '.', ''),
            'signature_key' => 'valid-signature',
        ];

        $response = $this->postJson(route('midtrans.notification'), $notification);

        $response->assertHeader('X-RateLimit-Limit', 60);
        $response->assertHeader('X-RateLimit-Remaining');
    }

    /**
     * Test rate limit is enforced after exceeding max attempts
     */
    public function test_rate_limit_enforced_after_max_attempts(): void
    {
        // Mock MidtransClient
        $mockClient = Mockery::mock(MidtransClient::class);
        $mockClient->shouldReceive('verifySignature')->andReturn(true);
        $this->app->instance(MidtransClient::class, $mockClient);

        $notification = [
            'order_id' => $this->transaction->order_id,
            'transaction_status' => 'pending',
            'status_code' => '201',
            'gross_amount' => number_format($this->transaction->gross_amount, 2, '.', ''),
            'signature_key' => 'valid-signature',
        ];

        // Simulate hitting rate limit by manually setting attempts
        $key = 'webhook_rate_limit:127.0.0.1';
        for ($i = 0; $i < 60; $i++) {
            RateLimiter::hit($key, 60);
        }

        // Next request should be rate limited
        $response = $this->postJson(route('midtrans.notification'), $notification);

        $response->assertStatus(429)
            ->assertJson([
                'status' => 'error',
                'message' => 'Too many requests. Please try again later.',
            ])
            ->assertHeader('Retry-After')
            ->assertHeader('X-RateLimit-Limit', 60)
            ->assertHeader('X-RateLimit-Remaining', 0);
    }

    /**
     * Test normal requests within rate limit are allowed
     */
    public function test_requests_within_rate_limit_are_allowed(): void
    {
        // Mock MidtransClient
        $mockClient = Mockery::mock(MidtransClient::class);
        $mockClient->shouldReceive('verifySignature')->andReturn(true);
        $this->app->instance(MidtransClient::class, $mockClient);

        $notification = [
            'order_id' => $this->transaction->order_id,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => number_format($this->transaction->gross_amount, 2, '.', ''),
            'signature_key' => 'valid-signature',
        ];

        // Make multiple requests within limit
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson(route('midtrans.notification'), $notification);
            $response->assertStatus(200);
        }
    }

    protected function tearDown(): void
    {
        Mockery::close();
        RateLimiter::clear('webhook_rate_limit:127.0.0.1');
        parent::tearDown();
    }
}
