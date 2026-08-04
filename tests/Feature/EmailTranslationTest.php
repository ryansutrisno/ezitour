<?php

namespace Tests\Feature;

use App\Mail\BookingConfirmed;
use App\Mail\PaymentSuccess;
use App\Mail\TripReminder;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTranslationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Package $package;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Test Traveler',
            'locale' => 'id',
        ]);

        $this->package = Package::create([
            'name' => 'Trip Bromo Ijen',
            'slug' => 'trip-bromo-ijen',
            'description' => 'Paket wisata Bromo Ijen',
            'total_price' => 1500000,
        ]);
    }

    public function test_booking_confirmed_email_renders_in_indonesian(): void
    {
        $rendered = (new BookingConfirmed($this->makeBooking()))->render();

        $this->assertStringContainsString('Terima kasih telah memesan', $rendered);
        $this->assertStringContainsString('Menunggu Pembayaran', $rendered);
    }

    public function test_booking_confirmed_email_renders_in_english(): void
    {
        $this->user->update(['locale' => 'en']);

        $rendered = (new BookingConfirmed($this->makeBooking()))->render();

        $this->assertStringContainsString('Thank you for booking', $rendered);
        $this->assertStringContainsString('Awaiting Payment', $rendered);
    }

    public function test_trip_reminder_email_respects_user_locale(): void
    {
        $this->user->update(['locale' => 'en']);

        $rendered = (new TripReminder($this->makeBooking(['status' => 'paid'])))->render();

        $this->assertStringContainsString('Your Trip Is Tomorrow', $rendered);
        $this->assertStringContainsString('Here are a few things to prepare', $rendered);
    }

    public function test_payment_success_email_in_indonesian(): void
    {
        $booking = $this->makeBooking(['status' => 'paid']);
        $transaction = Transaction::create([
            'booking_id' => $booking->id,
            'order_id' => 'BOOK-'.$booking->id.'-translation',
            'gross_amount' => $booking->total_amount,
            'transaction_status' => Transaction::STATUS_PAID,
            'payment_type' => 'bank_transfer',
            'transaction_time' => now(),
        ]);

        $rendered = (new PaymentSuccess($booking, $transaction))->render();

        $this->assertStringContainsString('LUNAS', $rendered);
        $this->assertStringContainsString('Pembayaran Berhasil', $rendered);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    protected function makeBooking(array $overrides = []): Booking
    {
        return Booking::create(array_merge([
            'user_id' => $this->user->id,
            'package_id' => $this->package->id,
            'travel_date' => now()->addDays(7),
            'total_amount' => 1500000,
            'status' => 'pending',
            'pickup_location' => 'Hotel Malioboro',
        ], $overrides));
    }
}
