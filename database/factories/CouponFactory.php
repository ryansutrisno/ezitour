<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement(['percentage', 'fixed']);

        return [
            'code' => strtoupper($this->faker->unique()->lexify('??????')),
            'type' => $type,
            'value' => $type === 'percentage' ? $this->faker->numberBetween(5, 25) : $this->faker->numberBetween(25000, 100000),
            'min_spend' => $this->faker->optional(0.4)->numberBetween(200000, 800000),
            'max_discount' => $type === 'percentage' ? $this->faker->optional(0.5)->numberBetween(50000, 300000) : null,
            'usage_limit_per_coupon' => $this->faker->optional(0.3)->numberBetween(50, 500),
            'usage_limit_per_user' => 1,
            'valid_from' => null,
            'valid_until' => null,
            'is_active' => true,
            'times_used' => 0,
        ];
    }
}
