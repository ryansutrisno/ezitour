<?php

namespace Tests\Feature;

use App\Jobs\SendPaymentExpiryReminder;
use App\Jobs\SendTripReminder;
use App\Mail\PaymentExpiryReminder;
use App\Mail\TripReminder;
use App\Models\Booking;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Feature: reminder-jobs (Sprint 8).
 *
 * Covers the two scheduled reminder commands (H-1 trip reminder + pending
 * payment-expiry reminder) and their dispatch-target jobs. Each command is
 * idempotent via a nullable timestamp column on the bookings table.
 */
class ReminderJobTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Package $package;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Maya Traveler',
            'email' => 'maya@example.com',
        ]);

        $this->package = Package::create([
            'name' => 'Trip Raja Ampat',
            'slug' => 'trip-raja-ampat',
            'description' => 'Liburan bahari memukau di Papua Barat.',
            'total_price' => 4500000,
        ]);
    }

    protected function makeBooking(array $overrides = []): Booking
    {
        return Booking::create(array_merge([
            'user_id' => $this->user->id,
            'package_id' => $this->package->id,
            'travel_date' => now()->addDays(7),
            'total_amount' => 4500000,
            'status' => 'pending',
            'pickup_location' => 'Bandara Domine Eduard Osok',
        ], $overrides));
    }

    public function test_reminders_trip_command_dispatches_jobs_for_paid_bookings_tomorrow(): void
    {
        Bus::fake();

        $booking = $this->makeBooking([
            'status' => 'paid',
            'travel_date' => Carbon::tomorrow(),
        ]);

        $this->artisan('reminders:trip')->assertSuccessful();

        Bus::assertDispatched(SendTripReminder::class, function (SendTripReminder $job) use ($booking): bool {
            return $job->booking->is($booking);
        });

        $this->assertNotNull($booking->fresh()->trip_reminder_sent_at);
    }

    public function test_reminders_trip_command_skips_non_paid_bookings(): void
    {
        Bus::fake();

        $this->makeBooking([
            'status' => 'pending',
            'travel_date' => Carbon::tomorrow(),
        ]);

        $this->makeBooking([
            'status' => 'cancelled',
            'travel_date' => Carbon::tomorrow(),
        ]);

        $this->artisan('reminders:trip')->assertSuccessful();

        Bus::assertNotDispatched(SendTripReminder::class);
    }

    public function test_reminders_trip_command_skips_already_reminded_bookings(): void
    {
        Bus::fake();

        $booking = $this->makeBooking([
            'status' => 'paid',
            'travel_date' => Carbon::tomorrow(),
            'trip_reminder_sent_at' => now()->subHour(),
        ]);

        $this->artisan('reminders:trip')->assertSuccessful();

        Bus::assertNotDispatched(SendTripReminder::class);

        // Timestamp unchanged.
        $this->assertEquals(
            $booking->fresh()->trip_reminder_sent_at?->timestamp,
            $booking->trip_reminder_sent_at?->timestamp,
        );
    }

    public function test_reminders_trip_command_skips_paid_bookings_not_tomorrow(): void
    {
        Bus::fake();

        $this->makeBooking([
            'status' => 'paid',
            'travel_date' => today(), // today, not tomorrow
        ]);

        $this->makeBooking([
            'status' => 'paid',
            'travel_date' => now()->addDays(3), // 3 days out, not tomorrow
        ]);

        $this->artisan('reminders:trip')->assertSuccessful();

        Bus::assertNotDispatched(SendTripReminder::class);
    }

    public function test_payment_expiry_command_dispatches_jobs_for_expiring_pending_bookings(): void
    {
        Bus::fake();

        // created 20.5h ago → 3.5h from 24h expiry → inside the 4h lead window.
        $booking = $this->makeBooking(['status' => 'pending']);
        $booking->forceFill(['created_at' => now()->subMinutes(20 * 60 + 30)])->save();

        $this->artisan('reminders:payment-expiry')->assertSuccessful();

        Bus::assertDispatched(SendPaymentExpiryReminder::class, function (SendPaymentExpiryReminder $job) use ($booking): bool {
            return $job->booking->is($booking);
        });

        $this->assertNotNull($booking->fresh()->payment_reminder_sent_at);
    }

    public function test_payment_expiry_command_skips_non_pending_bookings(): void
    {
        Bus::fake();

        $paid = $this->makeBooking(['status' => 'paid']);
        $paid->forceFill(['created_at' => now()->subMinutes(20 * 60 + 30)])->save();

        $cancelled = $this->makeBooking(['status' => 'cancelled']);
        $cancelled->forceFill(['created_at' => now()->subMinutes(20 * 60 + 30)])->save();

        $this->artisan('reminders:payment-expiry')->assertSuccessful();

        Bus::assertNotDispatched(SendPaymentExpiryReminder::class);
    }

    public function test_trip_reminder_job_sends_email_to_booking_owner(): void
    {
        Mail::fake();

        $booking = $this->makeBooking(['status' => 'paid']);

        dispatch(new SendTripReminder($booking));

        // TripReminder implements ShouldQueue, so Mail::to()->send() routes it
        // through the queue — assert with assertQueued (matches the project's
        // existing EmailNotificationTest pattern).
        Mail::assertQueued(TripReminder::class, function (TripReminder $mail) use ($booking): bool {
            return $mail->hasTo($booking->user->email);
        });
    }

    public function test_payment_expiry_job_sends_email_to_booking_owner(): void
    {
        Mail::fake();

        $booking = $this->makeBooking(['status' => 'pending']);

        dispatch(new SendPaymentExpiryReminder($booking));

        Mail::assertQueued(PaymentExpiryReminder::class, function (PaymentExpiryReminder $mail) use ($booking): bool {
            return $mail->hasTo($booking->user->email);
        });
    }
}
