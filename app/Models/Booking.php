<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'travel_date' => 'date',
        'total_amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'base_subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'price_per_pax' => 'decimal:2',
    ];

    /**
     * Auto-generate a human-readable booking code on creation.
     *
     * Format: EZT-{year}-{zero-padded id}, e.g. EZT-2026-0007. Generated in
     * the `created` hook (after insert) so the real id is available, keeping
     * new bookings aligned with the backfilled rows from the migration.
     */
    protected static function booted(): void
    {
        static::created(function (self $booking): void {
            if (! empty($booking->code)) {
                return;
            }

            $booking->code = 'EZT-'.$booking->created_at->format('Y').'-'.str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
            $booking->saveQuietly();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get all transactions for this booking.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the latest transaction for this booking.
     */
    public function latestTransaction()
    {
        return $this->hasOne(Transaction::class)->latestOfMany();
    }

    /**
     * Check if booking has been paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Whether this booking received a tier discount.
     */
    public function hasDiscount(): bool
    {
        return (float) $this->discount_amount > 0;
    }

    /**
     * Check if booking has pending payment.
     */
    public function hasPendingPayment(): bool
    {
        return $this->transactions()
            ->where('transaction_status', Transaction::STATUS_PENDING)
            ->exists();
    }

    /**
     * Check if booking can initiate new payment.
     */
    public function canPay(): bool
    {
        // Cannot pay if already paid
        if ($this->isPaid()) {
            return false;
        }

        // Cannot pay if there's a pending transaction
        if ($this->hasPendingPayment()) {
            return false;
        }

        return true;
    }

    /**
     * Check if booking can retry payment (has failed/expired transaction).
     */
    public function canRetryPayment(): bool
    {
        // Cannot retry if already paid
        if ($this->isPaid()) {
            return false;
        }

        // Cannot retry if there's a pending transaction
        if ($this->hasPendingPayment()) {
            return false;
        }

        // Can retry if there's a failed or expired transaction
        return $this->transactions()
            ->whereIn('transaction_status', [
                Transaction::STATUS_FAILED,
                Transaction::STATUS_EXPIRED,
            ])
            ->exists();
    }

    /**
     * Check if booking has failed payment.
     */
    public function hasFailedPayment(): bool
    {
        return $this->transactions()
            ->whereIn('transaction_status', [
                Transaction::STATUS_FAILED,
                Transaction::STATUS_EXPIRED,
            ])
            ->exists();
    }

    /**
     * Get the pending transaction for this booking (if any).
     */
    public function getPendingTransaction(): ?Transaction
    {
        return $this->transactions()
            ->where('transaction_status', Transaction::STATUS_PENDING)
            ->latest()
            ->first();
    }

    /**
     * Check if booking has expired transaction that can be retried.
     */
    public function hasExpiredTransaction(): bool
    {
        return $this->transactions()
            ->where('transaction_status', Transaction::STATUS_EXPIRED)
            ->exists();
    }
}
