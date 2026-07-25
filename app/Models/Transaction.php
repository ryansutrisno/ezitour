<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Transaction status constants
     */
    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_SUPERSEDED = 'superseded';

    /**
     * Default expiry duration in minutes (24 hours)
     */
    public const DEFAULT_EXPIRY_MINUTES = 1440;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'order_id',
        'snap_token',
        'gross_amount',
        'payment_type',
        'transaction_status',
        'transaction_time',
        'settlement_time',
        'expiry_time',
        'status_code',
        'fraud_status',
        'raw_notification',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gross_amount' => 'decimal:2',
        'transaction_time' => 'datetime',
        'settlement_time' => 'datetime',
        'expiry_time' => 'datetime',
        'raw_notification' => 'array',
    ];

    /**
     * Get the booking that owns the transaction.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Check if transaction is paid.
     */
    public function isPaid(): bool
    {
        return $this->transaction_status === self::STATUS_PAID;
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->transaction_status === self::STATUS_PENDING;
    }

    /**
     * Check if transaction is failed.
     */
    public function isFailed(): bool
    {
        return $this->transaction_status === self::STATUS_FAILED;
    }

    /**
     * Check if transaction is expired.
     */
    public function isExpired(): bool
    {
        return $this->transaction_status === self::STATUS_EXPIRED;
    }

    /**
     * Check if transaction can be retried.
     */
    public function canRetry(): bool
    {
        return in_array($this->transaction_status, [
            self::STATUS_FAILED,
            self::STATUS_EXPIRED,
        ]);
    }

    /**
     * Check if transaction has expired based on expiry_time.
     */
    public function hasExpired(): bool
    {
        // If already marked as expired, return true
        if ($this->isExpired()) {
            return true;
        }

        // If no expiry_time set, cannot determine expiry
        if (! $this->expiry_time) {
            return false;
        }

        // Check if current time is past expiry_time
        return now()->greaterThan($this->expiry_time);
    }

    /**
     * Get time remaining until expiry in minutes.
     * Returns null if no expiry_time set or already expired.
     */
    public function getTimeRemainingMinutes(): ?int
    {
        if (! $this->expiry_time || $this->hasExpired()) {
            return null;
        }

        return (int) now()->diffInMinutes($this->expiry_time, false);
    }

    /**
     * Get formatted time remaining string.
     */
    public function getTimeRemainingFormatted(): ?string
    {
        $minutes = $this->getTimeRemainingMinutes();

        if ($minutes === null || $minutes <= 0) {
            return null;
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0) {
            return sprintf('%d jam %d menit', $hours, $remainingMinutes);
        }

        return sprintf('%d menit', $remainingMinutes);
    }
}
