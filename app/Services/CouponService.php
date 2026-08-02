<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CouponService
{
    /**
     * Validate a coupon code against a subtotal and user.
     *
     * @return array{valid: bool, error: ?string, discount: float, coupon: ?Coupon}
     */
    public function validate(string $code, float $subtotal, User $user): array
    {
        $normalized = strtolower(trim($code));

        $coupon = Coupon::whereRaw('LOWER(code) = ?', [$normalized])->first();

        if (! $coupon) {
            return $this->invalid('Promo tidak ditemukan.');
        }

        if (! $coupon->is_active) {
            return $this->invalid('Promo sudah tidak aktif.');
        }

        $now = now();

        if ($coupon->valid_from && $now->lt($coupon->valid_from)) {
            return $this->invalid('Promo belum berlaku.');
        }

        if ($coupon->valid_until && $now->gt($coupon->valid_until)) {
            return $this->invalid('Promo sudah kadaluarsa.');
        }

        if ($coupon->usage_limit_per_coupon !== null && $coupon->times_used >= $coupon->usage_limit_per_coupon) {
            return $this->invalid('Promo sudah mencapai batas penggunaan total.');
        }

        $perUserUsed = (int) ($coupon->users()->where('user_id', $user->id)->value('times_used') ?? 0);
        if ($perUserUsed >= $coupon->usage_limit_per_user) {
            return $this->invalid('Kamu sudah menggunakan promo ini.');
        }

        if ($coupon->min_spend && $subtotal < (float) $coupon->min_spend) {
            return $this->invalid(
                'Minimal belanja Rp '.number_format((float) $coupon->min_spend, 0, ',', '.').
                ' untuk menggunakan promo ini.'
            );
        }

        $discount = $this->computeDiscount($coupon, $subtotal);

        return [
            'valid' => true,
            'error' => null,
            'discount' => $discount,
            'coupon' => $coupon,
        ];
    }

    /**
     * Compute the raw discount amount for a coupon against a subtotal.
     *
     * Fixed:   min(value, subtotal) — never negative, never exceeds subtotal.
     * Percent: subtotal * value/100, optionally capped by max_discount.
     */
    public function computeDiscount(Coupon $coupon, float $subtotal): float
    {
        if ($coupon->type === 'fixed') {
            return min((float) $coupon->value, $subtotal);
        }

        // percentage
        $amount = $subtotal * ((float) $coupon->value / 100);

        if ($coupon->max_discount) {
            $amount = min($amount, (float) $coupon->max_discount);
        }

        return round($amount, 2);
    }

    /**
     * Atomically increment usage counters for a coupon and user.
     */
    public function incrementUsage(Coupon $coupon, User $user): void
    {
        DB::transaction(function () use ($coupon, $user): void {
            $coupon->increment('times_used');

            // Pivot: bump per-user usage via syncWithoutDetaching + raw increment.
            $existing = (int) ($coupon->users()->where('user_id', $user->id)->value('times_used') ?? 0);

            $coupon->users()->syncWithoutDetaching([
                $user->id => ['times_used' => $existing + 1],
            ]);
        });
    }

    /**
     * @return array{valid: bool, error: ?string, discount: float, coupon: null}
     */
    private function invalid(string $message): array
    {
        return [
            'valid' => false,
            'error' => $message,
            'discount' => 0.0,
            'coupon' => null,
        ];
    }
}
