<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true).' Tour';

        return [
            'name' => $name,
            'slug' => Str::slug($name.'-'.$this->faker->unique()->randomNumber(5)),
            'description' => $this->faker->sentence(12),
            'total_price' => $this->faker->numberBetween(500_000, 5_000_000),
            'thumbnail_url' => null,
            'region' => null,
            'category' => null,
            'duration_days' => null,
        ];
    }
}
