<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReviewSeeder extends Seeder
{
    /**
     * Seed 6 sample reviews across the first 3 packages.
     *
     * Each review is backed by a real user + a paid booking so the demo data
     * respects the ReviewPolicy constraints. Idempotent: keyed by
     * (user email, package slug) via updateOrCreate with the unique index.
     */
    public function run(): void
    {
        $packages = Package::query()->orderBy('id')->limit(3)->get();

        if ($packages->isEmpty()) {
            return;
        }

        $reviewers = [
            ['name' => 'Budi Santoso', 'email' => 'budi@ezitour.com', 'rating' => 5, 'comment' => 'Trip yang luar biasa! Supirnya ramah dan tepat waktu, destinasi yang dikunjungi sesuai dengan yang dijanjikan. Pasti pakai EziTour lagi!'],
            ['name' => 'Andi Wijaya', 'email' => 'andi@ezitour.com', 'rating' => 5, 'comment' => 'Pelayanan bintang lima! Dari proses booking sampai eksekusi trip semua mulus. Highly recommended buat yang mau liburan tanpa ribet.'],
            ['name' => 'Siti Rahmawati', 'email' => 'siti@ezitour.com', 'rating' => 4, 'comment' => 'Trip menyenangkan, harga bersaing banget. Sedikit catatan: mungkin bisa ditambah waktu istirahat di tengah perjalanan. Overall puas!'],
            ['name' => 'Dimas Pratama', 'email' => 'dimas@ezitour.com', 'rating' => 5, 'comment' => 'Pengalaman tak terlupakan! Guide lokalnya paham betul sejarah setiap tempat. Foto-foto jadi keren banget.'],
            ['name' => 'Rina Marlina', 'email' => 'rina@ezitour.com', 'rating' => 4, 'comment' => 'Liburan keluarga jadi gampang banget. Mobilnya nyaman, AC dingin, dan anak-anak happy. Terima kasih EziTour!'],
            ['name' => 'Joko Susilo', 'email' => 'joko@ezitour.com', 'rating' => 5, 'comment' => 'Sudah ketiga kali pakai EziTour dan tidak pernah kecewa. Profesional, transparan harganya, dan selalu memberikan pengalaman terbaik.'],
        ];

        $i = 0;
        foreach ($reviewers as $reviewer) {
            $package = $packages[$i % $packages->count()];

            $user = User::firstOrCreate(
                ['email' => $reviewer['email']],
                [
                    'name' => $reviewer['name'],
                    'password' => bcrypt('password'),
                    'role' => 'traveler',
                ]
            );

            // Ensure the user has a paid booking so the review is legitimate.
            Booking::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                    'status' => 'paid',
                ],
                [
                    'travel_date' => now()->subDays(30 + $i),
                    'total_amount' => $package->total_price,
                    'pickup_location' => 'Hotel',
                    'code' => 'EZT-SEED-'.strtoupper(Str::random(6)),
                ]
            );

            Review::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                ],
                [
                    'rating' => $reviewer['rating'],
                    'comment' => $reviewer['comment'],
                    'is_approved' => true,
                ]
            );

            $i++;
        }
    }
}
