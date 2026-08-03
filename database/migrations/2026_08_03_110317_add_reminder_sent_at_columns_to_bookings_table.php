<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Idempotency timestamps for the scheduled reminder jobs (Sprint 8).
            // Null = reminder not yet sent; once dispatched the job/command stamps now().
            $table->timestamp('trip_reminder_sent_at')->nullable()->after('payment_date')
                ->comment('H-1 trip reminder dispatch timestamp; null = not yet sent');
            $table->timestamp('payment_reminder_sent_at')->nullable()->after('trip_reminder_sent_at')
                ->comment('Pending-payment expiry reminder dispatch timestamp; null = not yet sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['trip_reminder_sent_at', 'payment_reminder_sent_at']);
        });
    }
};
