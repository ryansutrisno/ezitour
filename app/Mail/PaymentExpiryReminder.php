<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email sent when a pending booking is ~4 hours from its payment expiry window.
 *
 * Dispatched by SendPaymentExpiryReminders (schedule: hourly) via
 * SendPaymentExpiryReminderJob. Queued via the default connection.
 */
class PaymentExpiryReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
        $this->booking->load(['user', 'package']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address', 'no-reply@ezitour.com'), config('mail.from.name', 'EziTour')),
            subject: 'Segera Selesaikan Pembayaran - Booking #'.$this->booking->code,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment.expiry-reminder',
            with: [
                'bookingCode' => (string) $this->booking->code,
                'totalAmount' => (float) $this->booking->total_amount,
            ],
        );
    }

    /**
     * @return array<int, mixed>
     */
    public function attachments(): array
    {
        return [];
    }
}
