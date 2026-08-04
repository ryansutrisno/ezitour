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
                'quote' => ['id' => 'Bookingnya gampang banget, tinggal pilih tanggal dan bayar. Supirnya ramah dan tepat waktu. Trip ke Jogja jadi tanpa drama!', 'en' => 'Booking was so easy — just choose a date and pay. The driver was friendly and punctual. Our Jogja trip was completely hassle-free!'],
                'rating' => 5,
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'location' => 'Bali',
                'quote' => ['id' => 'Harganya bersaing banget dibanding saya booking sendiri. Tiket masuk dan mobil sudah termasuk. Pasti pakai EziTour lagi!', 'en' => 'The price was much more competitive than booking everything myself. Entrance tickets and the car were included. I will definitely use EziTour again!'],
                'rating' => 5,
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Dimas Kurniawan',
                'location' => 'Lombok',
                'quote' => ['id' => 'Pas ada kendala di tengah trip, customer service-nya respon cepat banget. Bikin tenang kalau liburan bareng keluarga.', 'en' => 'When we had an issue during the trip, customer service responded very quickly. It was reassuring to travel with my family.'],
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
