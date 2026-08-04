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
 * Email sent when a booking is created and is awaiting payment.
 *
 * Dispatched from BookingCreationService (covers every checkout path) and the
 * legacy BookingController::store(). Queued via the default connection so it
 * never blocks the request.
 */
class BookingConfirmed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
        // Ensure relations used by the subject + template are loaded without
        // triggering lazy N+1 queries during serialization.
        $this->booking->loadMissing(['user', 'package']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address', 'no-reply@ezitour.com'), config('mail.from.name', 'EziTour')),
            subject: 'Booking Paket Wisata '.$this->booking->package->name.' - Pesanan Diterima',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking.confirmed',
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
