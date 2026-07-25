<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Seed the default settings with the values currently hardcoded in the
     * front-end Blade templates so the site keeps rendering identically once
     * those templates start reading from spatie/laravel-settings.
     *
     * Rows are written directly (instead of resolving the Settings objects)
     * because spatie/laravel-settings refuses to hydrate a settings class
     * before its properties exist in the database.
     */
    public function up(): void
    {
        if (! Schema::hasTable($this->table())) {
            return;
        }

        $rows = $this->defaults();

        // Idempotent: re-running the migration (e.g. in tests) overwrites
        // existing rows with the same (group, name) pair.
        DB::table($this->table())->upsert(
            $rows,
            ['group', 'name'],
            ['payload', 'updated_at'],
        );
    }

    public function down(): void
    {
        if (! Schema::hasTable($this->table())) {
            return;
        }

        DB::table($this->table())
            ->whereIn('group', ['general', 'home', 'contact', 'about'])
            ->delete();
    }

    /**
     * Default setting rows, one per property.
     *
     * Each payload mirrors spatie/laravel-settings' default encoder
     * (`json_encode($value)`), so a string becomes a JSON string and an array
     * becomes a JSON array.
     *
     * @return array<int, array{group: string, name: string, locked: bool, payload: string, created_at: string, updated_at: string}>
     */
    private function defaults(): array
    {
        $now = now()->toDateTimeString();

        $values = [
            'general' => [
                'siteName' => 'EziTour',
                'tagline' => 'Liburan Impian, Tanpa Ribet',
                'footerTagline' => 'Platform perjalanan wisata tanpa ribet. Pilih paket, bayar, dan berangkat! Kami urus transportasi, supir, dan tiket masuk — kamu tinggal duduk manis.',
            ],
            'home' => [
                'heroBadge' => 'Travel partner tepercaya sejak 2019',
                'heroHeadline' => 'Liburan Impian,',
                'heroHeadlineAccent' => 'Tanpa Ribet.',
                'heroSubheadline' => 'Pilih paket wisata favoritmu, kami urus sisanya. Dari transportasi, supir berpengalaman, hingga tiket masuk wisata. Kamu tinggal duduk manis dan nikmati perjalanannya!',
                'statDestinations' => '500+',
                'statTravelers' => '10K+',
                'statRating' => '4.9',
                'statSupport' => '24/7',
            ],
            'contact' => [
                'email' => 'support@ezitour.com',
                'phone' => '+62 812 3456 7890',
                'whatsapp' => '6281234567890',
                'address' => 'Yogyakarta, Indonesia',
                'instagramUrl' => 'https://instagram.com/ezitour',
                'facebookUrl' => 'https://facebook.com/ezitour',
                'twitterUrl' => 'https://twitter.com/ezitour',
            ],
            'about' => [
                'foundedYear' => '2019',
                'provincesCovered' => '34',
                'partnersCount' => '200+',
                'visionText' => 'Menjadi platform perjalanan wisata paling tepercaya di Indonesia — yang menghubungkan setiap traveler dengan pengalaman otentik di setiap sudut Nusantara, dari Sabang sampai Merauke.',
                'missionPoints' => [
                    ['point' => 'Menyederhanakan perencanaan perjalanan lewat teknologi yang intuitif.'],
                    ['point' => 'Memberdayakan mitra lokal — supir, vendor, dan komunitas wisata.'],
                    ['point' => 'Memberi kepastian harga yang jujur dan layanan yang bisa diandalkan.'],
                ],
            ],
        ];

        $rows = [];
        foreach ($values as $group => $properties) {
            foreach ($properties as $name => $payload) {
                $rows[] = [
                    'group' => $group,
                    'name' => $name,
                    'locked' => false,
                    'payload' => json_encode($payload),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        return $rows;
    }

    private function table(): string
    {
        return config('settings.repositories.database.table') ?? 'settings';
    }
};
