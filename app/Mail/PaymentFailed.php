<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email sent when a payment is denied / fails / expires, so the customer knows
 * they can retry. The booking stays pending (Failed Payment Isolation).
 *
 * Dispatched from PaymentService::processNotification() after the DB
 * transaction commits.
 */
class PaymentFailed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking, public Transaction $transaction)
    {
        $this->booking->loadMissing(['user', 'package']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address', 'no-reply@ezitour.com'), config('mail.from.name', 'EziTour')),
            subject: 'Pembayaran Gagal - Booking #'.$this->booking->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment.failed',
            with: [
                'bookingCode' => (string) $this->booking->id,
                'totalAmount' => (float) $this->booking->total_amount,
            ],
        );
    }

    public function build(): static
    {
        app()->setLocale($this->booking->user->locale ?? config('app.locale', 'id'));

        return $this;
    }

    /**
     * @return array<int, mixed>
     */
    public function attachments(): array
    {
        return [];
    }
}
