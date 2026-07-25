<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\User;
use App\Services\MidtransClient;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

/**
 * Feature: payment-integration
 * Checkpoint 14: Payment Flow Complete
 *
 * Tests untuk memverifikasi:
 * - Complete payment flow end-to-end
 * - Retry flow
 * - Expiration handling
 */
class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Package $package;

    protected Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create test package
        $this->package = Package::create([
            'name' => 'Test Package',
            'slug' => 'test-package',
            'description' => 'Test package description',
            'total_price' => 1000000,
        ]);

        // Create test booking
        $this->booking = Booking::create([
            'user_id' => $this->user->id,
            'package_id' => $this->package->id,
            'travel_date' => now()->addDays(7),
            'total_amount' => 1000000,
            'status' => 'pending',
            'pickup_location' => 'Test Pickup Location',
        ]);
    }

    /**
     * Test complete payment flow: create payment -> notification -> status update
     */
    public function test_complete_payment_flow(): void
    {
        // Mock MidtransClient
        $mockClient = Mockery::mock(MidtransClient::class);
        $mockClient->shouldReceive('getSnapToken')
            ->once()
            ->andReturn('test-snap-token-123');

        $this->app->instance(MidtransClient::class, $mockClient);

        // Step 1: Create payment
        $response = $this->actingAs($this->user)
            ->postJson(route('payments.create', $this->booking));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'snap_token',
                'order_id',
            ]);

        // Verify transaction was created
        $this->assertDatabaseHas('transactions', [
            'booking_id' => $this->booking->id,
            'transaction_status' => Transaction::STATUS_PENDING,
        ]);

        $transaction = Transaction::where('booking_id', $this->booking->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('test-snap-token-123', $transaction->snap_token);
    }

    /**
     * Test payment notification processing with settlement status
     */
    public function test_payment_notification_updates_status_to_paid(): void
    {
        // Create a pending transaction first
        $transaction = Transaction::create([
            'booking_id' => $this->booking->id,
            'order_id' => 'BOOK-'.$this->booking->id.'-'.time().'-test',
            'snap_token' => 'test-snap-token',
            'gross_amount' => $this->booking->total_amount,
            'transaction_status' => Transaction::STATUS_PENDING,
            'transaction_time' => now(),
        ]);

        // Mock MidtransClient for signature verification
        $mockClient = Mockery::mock(MidtransClient::class);
        $mockClient->shouldReceive('verifySignature')
            ->once()
            ->andReturn(true);

        $this->app->instance(MidtransClient::class, $mockClient);

        // Simulate Midtrans notification
        $notification = [
            'order_id' => $transaction->order_id,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => number_format($transaction->gross_amount, 2, '.', ''),
            'signature_key' => 'valid-signature',
            'payment_type' => 'bank_transfer',
        ];

        $response = $this->postJson(route('midtrans.notification'), $notification);

        $response->assertStatus(200);

        // Verify transaction status updated
        $transaction->refresh();
        $this->assertEquals(Transaction::STATUS_PAID, $transaction->transaction_status);

        // Verify booking status updated
        $this->booking->refresh();
        $this->assertEquals('paid', $this->booking->status);
        $this->assertNotNull($this->booking->payment_date);
    }

    /**
     * Test payment notification with failed status keeps booking pending
     */
    public function test_failed_payment_keeps_booking_pending(): void
    {
        // Create a pending transaction
        $transaction = Transaction::create([
            'booking_id' => $this->booking->id,
            'order_id' => 'BOOK-'.$this->booking->id.'-'.time().'-test',
            'snap_token' => 'test-snap-token',
            'gross_amount' => $this->booking->total_amount,
            'transaction_status' => Transaction::STATUS_PENDING,
            'transaction_time' => now(),
        ]);

        // Mock MidtransClient
        $mockClient = Mockery::mock(MidtransClient::class);
        $mockClient->shouldReceive('verifySignature')
            ->once()
            ->andReturn(true);

        $this->app->instance(MidtransClient::class, $mockClient);

        // Simulate failed payment notification
        $notification = [
            'order_id' => $transaction->order_id,
            'transaction_status' => 'deny',
            'status_code' => '202',
            'gross_amount' => number_format($transaction->gross_amount, 2, '.', ''),
            'signature_key' => 'valid-signature',
        ];

        $response = $this->postJson(route('midtrans.notification'), $notification);

        $response->assertStatus(200);

        // Verify transaction status updated to failed
        $transaction->refresh();
        $this->assertEquals(Transaction::STATUS_FAILED, $transaction->transaction_status);

        // Verify booking status remains pending (Property 10: Failed Payment Isolation)
        $this->booking->refresh();
        $this->assertEquals('pending', $this->booking->status);
    }

    /**
     * Test payment retry flow
     */
    public function test_payment_retry_creates_new_transaction(): void
    {
        // Create a failed transaction first
        $oldTransaction = Transaction::create([
            'booking_id' => $this->booking->id,
            'order_id' => 'BOOK-'.$this->booking->id.'-'.time().'-old',
            'snap_token' => 'old-snap-token',
            'gross_amount' => $this->booking->total_amount,
            'transaction_status' => Transaction::STATUS_FAILED,
            'transaction_time' => now()->subHour(),
        ]);

        // Mock MidtransClient
        $mockClient = Mockery::mock(MidtransClient::class);
        $mockClient->shouldReceive('getSnapToken')
            ->once()
            ->andReturn('new-snap-token-456');

        $this->app->instance(MidtransClient::class, $mockClient);

        // Retry payment
        $response = $this->actingAs($this->user)
            ->postJson(route('payments.retry', $this->booking));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Verify old transaction marked as superseded
        $oldTransaction->refresh();
        $this->assertEquals(Transaction::STATUS_SUPERSEDED, $oldTransaction->transaction_status);

        // Verify new transaction created
        $newTransaction = Transaction::where('booking_id', $this->booking->id)
            ->where('transaction_status', Transaction::STATUS_PENDING)
            ->first();

        $this->assertNotNull($newTransaction);
        $this->assertEquals('new-snap-token-456', $newTransaction->snap_token);
        $this->assertNotEquals($oldTransaction->order_id, $newTransaction->order_id);
    }

    /**
     * Test expiration handling
     */
    public function test_expired_transaction_can_be_retried(): void
    {
        // Create an expired transaction
        $expiredTransaction = Transaction::create([
            'booking_id' => $this->booking->id,
            'order_id' => 'BOOK-'.$this->booking->id.'-'.time().'-expired',
            'snap_token' => 'expired-snap-token',
            'gross_amount' => $this->booking->total_amount,
            'transaction_status' => Transaction::STATUS_EXPIRED,
            'transaction_time' => now()->subDay(),
            'expiry_time' => now()->subHour(),
        ]);

        // Mock MidtransClient
        $mockClient = Mockery::mock(MidtransClient::class);
        $mockClient->shouldReceive('getSnapToken')
            ->once()
            ->andReturn('retry-snap-token');

        $this->app->instance(MidtransClient::class, $mockClient);

        // Retry payment after expiration
        $response = $this->actingAs($this->user)
            ->postJson(route('payments.retry', $this->booking));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Verify expired transaction marked as superseded
        $expiredTransaction->refresh();
        $this->assertEquals(Transaction::STATUS_SUPERSEDED, $expiredTransaction->transaction_status);

        // Verify new transaction created
        $newTransaction = Transaction::where('booking_id', $this->booking->id)
            ->where('transaction_status', Transaction::STATUS_PENDING)
            ->first();

        $this->assertNotNull($newTransaction);
    }

    /**
     * Test paid booking cannot be downgraded (Property 12: Status Immutability)
     */
    public function test_paid_booking_status_cannot_be_downgraded(): void
    {
        // Set booking as paid
        $this->booking->update([
            'status' => 'paid',
            'payment_date' => now(),
        ]);

        // Create a paid transaction
        $paidTransaction = Transaction::create([
            'booking_id' => $this->booking->id,
            'order_id' => 'BOOK-'.$this->booking->id.'-'.time().'-paid',
            'snap_token' => 'paid-snap-token',
            'gross_amount' => $this->booking->total_amount,
            'transaction_status' => Transaction::STATUS_PAID,
            'transaction_time' => now(),
        ]);

        // Create another transaction that fails
        $failedTransaction = Transaction::create([
            'booking_id' => $this->booking->id,
            'order_id' => 'BOOK-'.$this->booking->id.'-'.time().'-failed',
            'snap_token' => 'failed-snap-token',
            'gross_amount' => $this->booking->total_amount,
            'transaction_status' => Transaction::STATUS_FAILED,
            'transaction_time' => now()->addMinute(),
        ]);

        // Try to update booking status via PaymentService
        $paymentService = app(PaymentService::class);
        $result = $paymentService->updateBookingStatus($failedTransaction);

        // Verify booking status remains paid
        $this->booking->refresh();
        $this->assertEquals('paid', $this->booking->status);
        $this->assertFalse($result);
    }

    /**
     * Test unauthorized user cannot create payment
     */
    public function test_unauthorized_user_cannot_create_payment(): void
    {
        /** @var User $otherUser */
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->postJson(route('payments.create', $this->booking));

        $response->assertStatus(403);
    }

    /**
     * Test already paid booking cannot create new payment
     */
    public function test_already_paid_booking_cannot_create_payment(): void
    {
        $this->booking->update(['status' => 'paid']);

        $response = $this->actingAs($this->user)
            ->postJson(route('payments.create', $this->booking));

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Booking ini sudah dibayar.',
            ]);
    }

    /**
     * Test invalid signature notification is rejected
     */
    public function test_invalid_signature_notification_rejected(): void
    {
        // Create a pending transaction
        $transaction = Transaction::create([
            'booking_id' => $this->booking->id,
            'order_id' => 'BOOK-'.$this->booking->id.'-'.time().'-test',
            'snap_token' => 'test-snap-token',
            'gross_amount' => $this->booking->total_amount,
            'transaction_status' => Transaction::STATUS_PENDING,
            'transaction_time' => now(),
        ]);

        // Mock MidtransClient to return invalid signature
        $mockClient = Mockery::mock(MidtransClient::class);
        $mockClient->shouldReceive('verifySignature')
            ->once()
            ->andReturn(false);

        $this->app->instance(MidtransClient::class, $mockClient);

        // Simulate notification with invalid signature
        $notification = [
            'order_id' => $transaction->order_id,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => number_format($transaction->gross_amount, 2, '.', ''),
            'signature_key' => 'invalid-signature',
        ];

        $response = $this->postJson(route('midtrans.notification'), $notification);

        $response->assertStatus(403);

        // Verify transaction status unchanged
        $transaction->refresh();
        $this->assertEquals(Transaction::STATUS_PENDING, $transaction->transaction_status);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
