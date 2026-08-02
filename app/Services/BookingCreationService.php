<?php

namespace App\Services;

use App\Mail\BookingConfirmed;
use App\Models\Booking;
use App\Models\Package;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingCreationService
{
    public function __construct(
        private CheckoutSessionService $checkoutSessionService
    ) {}

    /**
     * Create booking from validated data.
     * Returns the created booking or throws exception on failure.
     *
     * @throws Exception
     */
    public function createBooking(User $user, Package $package, array $bookingData): Booking
    {
        $booking = DB::transaction(function () use ($user, $package, $bookingData) {
            $pricing = $package->calculatePricing((int) $bookingData['participants']);

            $booking = Booking::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'travel_date' => $bookingData['travel_date'],
                'participants' => (int) $bookingData['participants'],
                'pickup_location' => $bookingData['pickup_location'],
                'total_amount' => $pricing['subtotal'],
                'base_subtotal' => $pricing['base_subtotal'],
                'discount_amount' => $pricing['discount_amount'],
                'applied_tier_label' => $pricing['tier_label'],
                'price_per_pax' => $pricing['price_per_pax'],
                'status' => 'pending',
            ]);

            return $booking;
        });

        // Dispatch confirmation email after the transaction commits so we never
        // mail a booking that got rolled back.
        $this->sendBookingConfirmedEmail($booking);

        return $booking;
    }

    /**
     * Queue the booking-confirmation email, swallowing mail failures so they
     * never break the booking flow.
     */
    protected function sendBookingConfirmedEmail(Booking $booking): void
    {
        try {
            Mail::to($booking->user)->send(new BookingConfirmed($booking));
        } catch (\Throwable $e) {
            Log::warning('Failed to send BookingConfirmed email', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create booking from pending session data.
     * Retrieves session data, creates booking, and clears session.
     *
     * @throws Exception
     */
    public function createFromSession(User $user): ?Booking
    {
        $sessionData = $this->checkoutSessionService->retrieve();

        if (! $sessionData) {
            return null;
        }

        $package = Package::find($sessionData['package_id']);

        if (! $package) {
            $this->checkoutSessionService->clear();

            return null;
        }

        $booking = $this->createBooking($user, $package, [
            'travel_date' => $sessionData['travel_date'],
            'participants' => $sessionData['participants'],
            'pickup_location' => $sessionData['pickup_location'],
        ]);

        // Clear session after successful booking creation
        $this->checkoutSessionService->clear();

        return $booking;
    }

    /**
     * Calculate total price for booking (delegates to Package::calculatePricing).
     * Returns the final subtotal after any tier discount.
     */
    public function calculateTotalPrice(Package $package, int $participants): float
    {
        return $package->calculatePricing($participants)['subtotal'];
    }
}
