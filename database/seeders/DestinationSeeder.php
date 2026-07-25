<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destinations = [
            [
                'name' => 'Candi Borobudur',
                'description' => 'Candi Buddha terbesar di dunia yang merupakan Situs Warisan Dunia UNESCO.',
                'price' => 50000,
                'avg_duration' => 120,
                'image_url' => 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?q=80&w=2070&auto=format&fit=crop',
            ],
            [
                'name' => 'Candi Prambanan',
                'description' => 'Kompleks candi Hindu terbesar di Indonesia yang dibangun pada abad ke-9.',
                'price' => 50000,
                'avg_duration' => 120,
                'image_url' => 'https://images.unsplash.com/photo-1604917621956-10dfa7cce2e7?q=80&w=2062&auto=format&fit=crop',
            ],
            [
                'name' => 'Tebing Breksi',
                'description' => 'Bekas pertambangan batu kapur yang disulap menjadi tempat wisata dengan ukiran artistik.',
                'price' => 10000,
                'avg_duration' => 60,
                'image_url' => 'https://images.unsplash.com/photo-1628488321554-4e207b71172d?q=80&w=2070&auto=format&fit=crop',
            ],
            [
                'name' => 'HeHa Sky View',
                'description' => 'Restoran dan tempat wisata kekinian dengan pemandangan kota Jogja dari ketinggian.',
                'price' => 20000,
                'avg_duration' => 90,
                'image_url' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=2070&auto=format&fit=crop',
            ],
            [
                'name' => 'Pantai Parangtritis',
                'description' => 'Pantai paling populer di Yogyakarta, terkenal dengan legenda Ratu Kidul dan sunset yang indah.',
                'price' => 10000,
                'avg_duration' => 120,
                'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=2070&auto=format&fit=crop',
            ],
            [
                'name' => 'Gereja Ayam (Bukit Rhema)',
                'description' => 'Bangunan doa unik berbentuk merpati yang sering disebut Gereja Ayam, dekat Borobudur.',
                'price' => 25000,
                'avg_duration' => 60,
                'image_url' => 'https://images.unsplash.com/photo-1582264560734-706c641c881c?q=80&w=2070&auto=format&fit=crop',
            ],
            [
                'name' => 'Malioboro',
                'description' => 'Jantung kota Yogyakarta, tempat belanja oleh-oleh dan menikmati suasana kota.',
                'price' => 0,
                'avg_duration' => 120,
                'image_url' => 'https://images.unsplash.com/photo-1584809825479-7a08b5066929?q=80&w=2067&auto=format&fit=crop',
            ],
        ];

        foreach ($destinations as $dest) {
            Destination::create($dest);
        }
    }
}
