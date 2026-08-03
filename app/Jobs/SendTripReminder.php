<?php

namespace App\Jobs;

use App\Mail\TripReminder;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends the H-1 trip reminder email to the booking owner.
 *
 * Dispatched by the SendTripReminders scheduler command for paid bookings
 * whose travel_date is tomorrow. Failures are logged but never re-thrown so
 * one bad address can't poison the queue worker.
 */
class SendTripReminder implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * @var array<int, int>
     */
    public array $backoff = [10, 30, 60];

    public function __construct(public Booking $booking)
    {
        //
    }

    public function handle(): void
    {
        try {
            Mail::to($this->booking->user->email)->send(new TripReminder($this->booking));
        } catch (\Throwable $e) {
            Log::warning('Trip reminder email failed to send', [
                'booking_id' => $this->booking->id,
                'code' => $this->booking->code,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
