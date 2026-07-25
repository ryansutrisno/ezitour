<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CheckoutSessionService
{
    private const SESSION_KEY = 'pending_booking';

    private const SESSION_TTL = 30; // minutes

    /**
     * Store booking data in session.
     */
    public function store(array $bookingData, int $packageId, string $packageSlug): void
    {
        $sessionData = [
            'package_id' => $packageId,
            'package_slug' => $packageSlug,
            'travel_date' => $bookingData['travel_date'],
            'participants' => (int) $bookingData['participants'],
            'pickup_location' => $bookingData['pickup_location'],
            'total_amount' => (float) $bookingData['total_amount'],
            'created_at' => now()->timestamp,
        ];

        Session::put(self::SESSION_KEY, $sessionData);
    }

    /**
     * Retrieve pending booking data from session.
     */
    public function retrieve(): ?array
    {
        if (! $this->isValid()) {
            $this->clear();

            return null;
        }

        return Session::get(self::SESSION_KEY);
    }

    /**
     * Check if there's pending booking data.
     */
    public function hasPendingBooking(): bool
    {
        return Session::has(self::SESSION_KEY) && $this->isValid();
    }

    /**
     * Clear pending booking data from session.
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Check if session data is still valid (not expired).
     */
    public function isValid(): bool
    {
        $data = Session::get(self::SESSION_KEY);

        if (! $data || ! isset($data['created_at'])) {
            return false;
        }

        $createdAt = $data['created_at'];
        $expiresAt = $createdAt + (self::SESSION_TTL * 60);

        return now()->timestamp < $expiresAt;
    }

    /**
     * Get the session TTL in minutes.
     */
    public function getTtlMinutes(): int
    {
        return self::SESSION_TTL;
    }
}
