<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Seed the testimonials extracted verbatim from the home page's previously
     * hardcoded testimonial section.
     */
    public function run(): void
    {
        $rows = [
            [
                'name' => 'Andi Rahman',
                'location' => 'Yogyakarta',
                'quote' => 'Bookingnya gampang banget, tinggal pilih tanggal dan bayar. Supirnya ramah dan tepat waktu. Trip ke Jogja jadi tanpa drama!',
                'rating' => 5,
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'location' => 'Bali',
                'quote' => 'Harganya bersaing banget dibanding saya booking sendiri. Tiket masuk dan mobil sudah termasuk. Pasti pakai EziTour lagi!',
                'rating' => 5,
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Dimas Kurniawan',
                'location' => 'Lombok',
                'quote' => 'Pas ada kendala di tengah trip, customer service-nya respon cepat banget. Bikin tenang kalau liburan bareng keluarga.',
                'rating' => 5,
                'is_published' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($rows as $row) {
            Testimonial::updateOrCreate(['name' => $row['name']], $row);
        }
    }
}
