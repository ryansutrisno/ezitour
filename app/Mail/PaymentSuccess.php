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
 * Email sent once a Midtrans `settlement` / accepted-`capture` notification
 * marks the booking as paid (the E-Ticket confirmation).
 *
 * Dispatched from PaymentService::processNotification() after the DB
 * transaction commits. Wrapped in try/catch at the call site so an email
 * failure never breaks the webhook response.
 */
class PaymentSuccess extends Mailable implements ShouldQueue
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
            subject: 'Pembayaran Berhasil - E-Ticket #'.$this->booking->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment.success',
            with: [
                'bookingCode' => (string) $this->booking->id,
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
