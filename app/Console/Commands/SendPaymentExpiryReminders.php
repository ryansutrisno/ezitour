<?php

namespace App\Console\Commands;

use App\Jobs\SendPaymentExpiryReminder;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Sends a final "complete your payment before it expires" reminder for
 * pending bookings that are ~4 hours from their configured expiry window.
 *
 * Expiry math (minutes):
 *   expiry_at = created_at + config('midtrans.expiry_duration', 1440)
 *   reminder fires when:  expiry_at - 4h <= now()  AND  now() < expiry_at
 *
 * Idempotent: rows are filtered by `payment_reminder_sent_at IS NULL` and
 * stamped after dispatch, so overlap never causes duplicate sends.
 */
class SendPaymentExpiryReminders extends Command
{
    protected $signature = 'reminders:payment-expiry';

    protected $description = 'Kirim pengingat untuk booking pending yang akan kedaluwarsa';

    public function handle(): int
    {
        $expiryMinutes = (int) config('midtrans.expiry_duration', 1440);
        $reminderLeadMinutes = 240; // 4 hours before expiry

        $expiryThreshold = now()->subMinutes($expiryMinutes - $reminderLeadMinutes);
        $hardCutoff = now()->subMinutes($expiryMinutes);

        $bookings = Booking::query()
            ->where('status', 'pending')
            ->whereNull('payment_reminder_sent_at')
            ->where('created_at', '<=', $expiryThreshold)
            ->where('created_at', '>', $hardCutoff)
            ->with('user')
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('Tidak ada booking pending yang mendekati kedaluwarsa.');

            return self::SUCCESS;
        }

        $dispatched = 0;
        foreach ($bookings as $booking) {
            SendPaymentExpiryReminder::dispatch($booking);

            $booking->forceFill(['payment_reminder_sent_at' => now()])->save();

            $dispatched++;
        }

        Log::info('Payment expiry reminders dispatched', ['count' => $dispatched]);
        $this->info("{$dispatched} pengingat kedaluwarsa pembayaran telah dikirim.");

        return self::SUCCESS;
    }
}
