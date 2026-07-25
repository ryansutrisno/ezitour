<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Package;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

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
        return DB::transaction(function () use ($user, $package, $bookingData) {
            $totalAmount = $this->calculateTotalPrice($package, (int) $bookingData['participants']);

            $booking = Booking::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'travel_date' => $bookingData['travel_date'],
                'participants' => (int) $bookingData['participants'],
                'pickup_location' => $bookingData['pickup_location'],
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            return $booking;
        });
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
     * Calculate total price for booking.
     */
    public function calculateTotalPrice(Package $package, int $participants): float
    {
        return (float) $package->total_price * $participants;
    }
}
