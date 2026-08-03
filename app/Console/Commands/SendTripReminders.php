<?php

namespace App\Console\Commands;

use App\Jobs\SendTripReminder;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Sends the H-1 "your trip is tomorrow" reminder to paid bookings.
 *
 * Idempotent: rows are filtered by `trip_reminder_sent_at IS NULL` and stamped
 * after dispatch, so a re-run (or overlapping schedule tick) never double-sends.
 */
class SendTripReminders extends Command
{
    protected $signature = 'reminders:trip';

    protected $description = 'Kirim pengingat H-1 untuk perjalanan besok';

    public function handle(): int
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $bookings = Booking::query()
            ->where('status', 'paid')
            ->whereDate('travel_date', $tomorrow)
            ->whereNull('trip_reminder_sent_at')
            ->with('user')
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('Tidak ada booking yang perlu dikirimi pengingat perjalanan.');

            return self::SUCCESS;
        }

        $dispatched = 0;
        foreach ($bookings as $booking) {
            SendTripReminder::dispatch($booking);

            $booking->forceFill(['trip_reminder_sent_at' => now()])->save();

            $dispatched++;
        }

        Log::info('Trip reminders dispatched', ['count' => $dispatched, 'travel_date' => $tomorrow]);
        $this->info("{$dispatched} pengingat perjalanan telah dikirim.");

        return self::SUCCESS;
    }
}
