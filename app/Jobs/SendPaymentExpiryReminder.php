<?php

namespace App\Jobs;

use App\Mail\PaymentExpiryReminder;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends the "complete your payment before it expires" reminder email.
 *
 * Dispatched by the SendPaymentExpiryReminders scheduler command for pending
 * bookings that are ~4 hours from their payment-expiry window. The retry
 * payment form lives behind auth on the dashboard, so the CTA points there
 * (the POST-only payments.create route can't be linked from an email).
 */
class SendPaymentExpiryReminder implements ShouldQueue
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
            Mail::to($this->booking->user->email)->send(new PaymentExpiryReminder($this->booking));
        } catch (\Throwable $e) {
            Log::warning('Payment expiry reminder email failed to send', [
                'booking_id' => $this->booking->id,
                'code' => $this->booking->code,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
