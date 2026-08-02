<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\PriceTier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PriceTier>
 */
class PriceTierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'package_id' => Package::factory(),
            'name' => 'Promo Rombongan',
            'min_pax' => $this->faker->numberBetween(5, 10),
            'max_pax' => $this->faker->optional(0.5)->numberBetween(11, 30),
            'price_per_pax' => $this->faker->numberBetween(400000, 900000),
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
