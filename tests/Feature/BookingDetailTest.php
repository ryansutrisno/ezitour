<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: booking-detail-cancel.
 *
 * Covers the booking detail page authorization, E-ticket PDF download,
 * cancellation flow, and the auto-generated booking `code`.
 */
class BookingDetailTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected Package $package;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create([
            'name' => 'Andi Traveler',
            'email' => 'andi@example.com',
        ]);

        $this->package = Package::create([
            'name' => 'Trip Bromo Ijen',
            'slug' => 'trip-bromo-ijen',
            'description' => 'Petualangan gunung berapi memukau.',
            'total_price' => 1500000,
        ]);
    }

    protected function makeBooking(array $overrides = []): Booking
    {
        return Booking::create(array_merge([
            'user_id' => $this->owner->id,
            'package_id' => $this->package->id,
            'travel_date' => now()->addDays(7),
            'total_amount' => 1500000,
            'status' => 'pending',
            'pickup_location' => 'Hotel Malioboro',
        ], $overrides));
    }

    public function test_guest_cannot_view_booking_detail(): void
    {
        $booking = $this->makeBooking();

        $this->get(route('bookings.show', $booking))
            ->assertRedirect(route('login'));
    }

    public function test_owner_can_view_own_booking_detail(): void
    {
        $booking = $this->makeBooking();

        $this->actingAs($this->owner)
            ->get(route('bookings.show', $booking))
            ->assertOk()
            ->assertSee($booking->code)
            ->assertSee('Trip Bromo Ijen');
    }

    public function test_other_user_cannot_view_booking_detail(): void
    {
        $booking = $this->makeBooking();
        $other = User::factory()->create();

        $this->actingAs($other)
            ->get(route('bookings.show', $booking))
            ->assertForbidden();
    }

    public function test_owner_can_download_e_ticket_pdf(): void
    {
        $booking = $this->makeBooking(['status' => 'paid', 'payment_date' => now()]);

        $response = $this->actingAs($this->owner)
            ->get(route('bookings.ticket', $booking));

        $response->assertOk();
        $this->assertStringContainsString('pdf', $response->headers->get('content-type'));
    }

    public function test_owner_can_cancel_pending_booking(): void
    {
        $booking = $this->makeBooking(); // pending by default

        $this->actingAs($this->owner)
            ->post(route('bookings.cancel', $booking))
            ->assertRedirect(route('bookings.show', $booking));

        $this->assertSame('cancelled', $booking->fresh()->status);
    }

    public function test_paid_booking_cannot_be_cancelled(): void
    {
        $booking = $this->makeBooking(['status' => 'paid', 'payment_date' => now()]);

        $this->actingAs($this->owner)
            ->post(route('bookings.cancel', $booking))
            ->assertRedirect(route('bookings.show', $booking));

        // Status must remain paid.
        $this->assertSame('paid', $booking->fresh()->status);
    }

    public function test_booking_has_unique_code_after_creation(): void
    {
        $booking = $this->makeBooking();

        $this->assertNotEmpty($booking->fresh()->code);
        $this->assertMatchesRegularExpression(
            '/^EZT-\d{4}-\d{4}$/',
            $booking->fresh()->code,
        );
    }

    public function test_e_ticket_blocked_for_unpaid_booking(): void
    {
        $booking = $this->makeBooking(); // pending

        $this->actingAs($this->owner)
            ->get(route('bookings.ticket', $booking))
            ->assertRedirect(route('bookings.show', $booking));
    }
}
