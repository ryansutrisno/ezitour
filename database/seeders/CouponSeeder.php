<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Seed 3 sample coupons.
     * Idempotent via firstOrCreate keyed by code.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'HEMAT10',
                'type' => 'fixed',
                'value' => 50000,
                'min_spend' => 500000,
                'max_discount' => null,
                'usage_limit_per_coupon' => null,
                'usage_limit_per_user' => 1,
                'valid_from' => null,
                'valid_until' => null,
                'is_active' => true,
            ],
            [
                'code' => 'LIBURAN15',
                'type' => 'percentage',
                'value' => 15,
                'min_spend' => null,
                'max_discount' => 200000,
                'usage_limit_per_coupon' => null,
                'usage_limit_per_user' => 1,
                'valid_from' => null,
                'valid_until' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'code' => 'NEWUSER',
                'type' => 'percentage',
                'value' => 10,
                'min_spend' => 300000,
                'max_discount' => null,
                'usage_limit_per_coupon' => null,
                'usage_limit_per_user' => 1,
                'valid_from' => null,
                'valid_until' => null,
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::firstOrCreate(
                ['code' => $coupon['code']],
                $coupon
            );
        }
    }
}
