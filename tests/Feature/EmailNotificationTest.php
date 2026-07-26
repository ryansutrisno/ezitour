<?php

namespace Tests\Feature;

use App\Mail\BookingConfirmed;
use App\Mail\PaymentFailed;
use App\Mail\PaymentSuccess;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\User;
use App\Services\MidtransClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

/**
 * Feature: email-notifications.
 *
 * Verifies that booking + payment lifecycle emails are dispatched from the
 * right chokepoints (BookingCreationService, BookingController, and the
 * Midtrans webhook via PaymentService).
 */
class EmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Package $package;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->package = Package::create([
            'name' => 'Trip Bromo Ijen',
            'slug' => 'trip-bromo-ijen',
            'description' => 'Paket wisata Bromo Ijen',
            'total_price' => 1500000,
        ]);
    }

    public function test_booking_confirmation_email_sent_on_checkout(): void
    {
        Mail::fake();

        $this->actingAs($this->user)
            ->post(route('front.checkout.store', $this->package->slug), [
                'travel_date' => now()->addDays(7)->format('Y-m-d'),
                'participants' => 2,
                'pickup_location' => 'Hotel Malioboro',
            ])
            ->assertRedirect();

        // Mailables implement ShouldQueue, so Mail::send() routes them to the
        // queue — assert with assertQueued (not assertSent).
        Mail::assertQueued(BookingConfirmed::class);
    }

    public function test_booking_confirmation_email_sent_on_direct_booking(): void
    {
        Mail::fake();

        $this->actingAs($this->user)
            ->post(route('front.booking.store', $this->package->slug), [
                'travel_date' => now()->addDays(7)->format('Y-m-d'),
                'participants' => 2,
                'pickup_location' => 'Hotel Malioboro',
            ])
            ->assertRedirect(route('dashboard.index'));

        Mail::assertQueued(BookingConfirmed::class);
    }

    public function test_payment_success_email_sent_on_settlement_webhook(): void
    {
        Mail::fake();

        $booking = Booking::create([
            'user_id' => $this->user->id,
            'package_id' => $this->package->id,
            'travel_date' => now()->addDays(7),
            'total_amount' => 1500000,
            'status' => 'pending',
            'pickup_location' => 'Hotel Malioboro',
        ]);

        $transaction = Transaction::create([
            'booking_id' => $booking->id,
            'order_id' => 'BOOK-'.$booking->id.'-'.time().'-settle',
            'snap_token' => 'snap-token',
            'gross_amount' => $booking->total_amount,
            'transaction_status' => Transaction::STATUS_PENDING,
            'transaction_time' => now(),
        ]);

        $mockClient = Mockery::mock(MidtransClient::class);
        $mockClient->shouldReceive('verifySignature')->once()->andReturn(true);
        $this->app->instance(MidtransClient::class, $mockClient);

        $this->postJson(route('midtrans.notification'), [
            'order_id' => $transaction->order_id,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => number_format($transaction->gross_amount, 2, '.', ''),
            'signature_key' => 'valid-signature',
            'payment_type' => 'bank_transfer',
        ])->assertStatus(200);

        Mail::assertQueued(PaymentSuccess::class);
        Mail::assertNotQueued(PaymentFailed::class);
    }

    public function test_payment_failed_email_sent_on_deny_webhook(): void
    {
        Mail::fake();

        $booking = Booking::create([
            'user_id' => $this->user->id,
            'package_id' => $this->package->id,
            'travel_date' => now()->addDays(7),
            'total_amount' => 1500000,
            'status' => 'pending',
            'pickup_location' => 'Hotel Malioboro',
        ]);

        $transaction = Transaction::create([
            'booking_id' => $booking->id,
            'order_id' => 'BOOK-'.$booking->id.'-'.time().'-deny',
            'snap_token' => 'snap-token',
            'gross_amount' => $booking->total_amount,
            'transaction_status' => Transaction::STATUS_PENDING,
            'transaction_time' => now(),
        ]);

        $mockClient = Mockery::mock(MidtransClient::class);
        $mockClient->shouldReceive('verifySignature')->once()->andReturn(true);
        $this->app->instance(MidtransClient::class, $mockClient);

        $this->postJson(route('midtrans.notification'), [
            'order_id' => $transaction->order_id,
            'transaction_status' => 'deny',
            'status_code' => '202',
            'gross_amount' => number_format($transaction->gross_amount, 2, '.', ''),
            'signature_key' => 'valid-signature',
        ])->assertStatus(200);

        Mail::assertQueued(PaymentFailed::class);
        Mail::assertNotQueued(PaymentSuccess::class);
    }

    public function test_booking_confirmed_email_renders_with_booking_details(): void
    {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'package_id' => $this->package->id,
            'travel_date' => now()->addDays(7),
            'total_amount' => 1500000,
            'status' => 'pending',
            'pickup_location' => 'Hotel Malioboro',
        ]);

        // Render the mailable to HTML and verify the customer-visible details
        // are present (package name, booking id, formatted total).
        $rendered = (new BookingConfirmed($booking))->render();

        $this->assertStringContainsString('Trip Bromo Ijen', $rendered);
        $this->assertStringContainsString('Kode Booking', $rendered);
        $this->assertStringContainsString('1.500.000', $rendered);
        $this->assertStringContainsString('Test User', $rendered);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
