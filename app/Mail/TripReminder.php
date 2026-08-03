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
 * Email sent H-1 before a paid booking's travel_date.
 *
 * Dispatched by SendTripReminders (schedule: hourly) via SendTripReminderJob.
 * Queued via the default connection so it never blocks the scheduler run.
 */
class TripReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
        // Ensure relations used by the subject + template are loaded without
        // triggering lazy N+1 queries during serialization.
        $this->booking->load(['user', 'package']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address', 'no-reply@ezitour.com'), config('mail.from.name', 'EziTour')),
            subject: 'Pengingat Perjalanan Besok - '.$this->booking->package->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.trip.reminder',
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
