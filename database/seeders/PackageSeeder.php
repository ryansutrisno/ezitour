<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Destination;
use App\Models\Package;
use App\Models\PackageItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have cars
        if (Car::count() == 0) {
            Car::create([
                'name' => 'Toyota Avanza',
                'type' => 'MPV',
                'license_plate' => 'AB 1234 XY',
                'capacity' => 6,
                'base_price_per_day' => 450000,
                'image_url' => 'https://www.toyota.astra.co.id/sites/default/files/2023-11/1_15.png',
            ]);
            Car::create([
                'name' => 'Toyota Hiace',
                'type' => 'Minibus',
                'license_plate' => 'AB 5678 CD',
                'capacity' => 14,
                'base_price_per_day' => 1200000,
                'image_url' => 'https://www.toyota.astra.co.id/sites/default/files/2021-08/1-white.png',
            ]);
        }

        // Create Packages
        $packages = [
            [
                'name' => 'Jogja Heritage Tour',
                'description' => 'Menjelajahi keajaiban sejarah Candi Borobudur dan Prambanan dalam satu hari.',
                'total_price' => 750000, // Estimasi harga
                'thumbnail_url' => 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?q=80&w=2070&auto=format&fit=crop',
                'items' => ['Candi Borobudur', 'Gereja Ayam (Bukit Rhema)', 'Candi Prambanan'],
            ],
            [
                'name' => 'Jogja Instagramable Spot',
                'description' => 'Kunjungi tempat-tempat hits dan instagramable di Jogja. Cocok untuk anak muda!',
                'total_price' => 600000,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=2070&auto=format&fit=crop',
                'items' => ['HeHa Sky View', 'Tebing Breksi', 'Malioboro'],
            ],
            [
                'name' => 'Jogja Beach & Sunset',
                'description' => 'Menikmati angin laut selatan dan sunset romantis di Parangtritis.',
                'total_price' => 550000,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=2070&auto=format&fit=crop',
                'items' => ['Pantai Parangtritis', 'Malioboro'],
            ],
        ];

        foreach ($packages as $pkgData) {
            $package = Package::create([
                'name' => $pkgData['name'],
                'slug' => Str::slug($pkgData['name']),
                'description' => $pkgData['description'],
                'total_price' => $pkgData['total_price'],
                'thumbnail_url' => $pkgData['thumbnail_url'],
            ]);

            $sequence = 1;
            foreach ($pkgData['items'] as $destName) {
                $destination = Destination::where('name', $destName)->first();
                if ($destination) {
                    PackageItem::create([
                        'package_id' => $package->id,
                        'destination_id' => $destination->id,
                        'sequence_order' => $sequence++,
                    ]);
                }
            }
        }
    }
}
