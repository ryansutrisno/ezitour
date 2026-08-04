<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@ezitour.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Demo customer account for testing
        User::firstOrCreate(
            ['email' => 'customer@ezitour.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'phone' => '0812 3456 7890',
                'role' => 'user',
                'locale' => 'id',
            ],
        );

        $this->call([
            DestinationSeeder::class,
            PackageSeeder::class,
            PriceTierSeeder::class,
            CouponSeeder::class,
            TestimonialSeeder::class,
            FaqSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
