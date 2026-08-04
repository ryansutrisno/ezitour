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
                'footerTagline_en' => 'Hassle-free travel platform. Pick a package, pay, and go! We handle transport, driver, and attraction tickets — you just sit back and enjoy.',
            ],
            'home' => [
                'heroBadge' => 'Travel partner tepercaya sejak 2019',
                'heroBadge_en' => 'Trusted travel partner since 2019',
                'heroHeadline' => 'Liburan Impian,',
                'heroHeadline_en' => 'Dream Holidays,',
                'heroHeadlineAccent' => 'Tanpa Ribet.',
                'heroHeadlineAccent_en' => 'Zero Hassle.',
                'heroSubheadline' => 'Pilih paket wisata favoritmu, kami urus sisanya. Dari transportasi, supir berpengalaman, hingga tiket masuk wisata. Kamu tinggal duduk manis dan nikmati perjalanannya!',
                'heroSubheadline_en' => 'Choose your favorite travel package, we handle the rest. From transport, experienced drivers, to attraction tickets. You just sit back and enjoy the journey!',
                'statDestinations' => '500+',
                'statTravelers' => '10K+',
                'statRating' => '4.9',
                'statSupport' => '24/7',
            ],
            'contact' => [
                'email' => 'hallo@trazmedia.com',
                'phone' => '+62 851 0326 3777',
                'whatsapp' => '6285103263777',
                'address' => 'Yogyakarta, Indonesia',
                'instagramUrl' => 'https://instagram.com/trazmedia',
                'facebookUrl' => 'https://facebook.com/trazmedia',
                'twitterUrl' => 'https://x.com/trazmedia',
            ],
            'about' => [
                'foundedYear' => '2019',
                'provincesCovered' => '34',
                'partnersCount' => '200+',
                'visionText' => 'Menjadi platform perjalanan wisata paling tepercaya di Indonesia — yang menghubungkan setiap traveler dengan pengalaman otentik di setiap sudut Nusantara, dari Sabang sampai Merauke.',
                'visionText_en' => 'To become Indonesia\'s most trusted travel platform — connecting every traveler with authentic experiences in every corner of the archipelago, from Sabang to Merauke.',
                'missionPoints' => [
                    ['point' => 'Menyederhanakan perencanaan perjalanan lewat teknologi yang intuitif.'],
                    ['point' => 'Memberdayakan mitra lokal — supir, vendor, dan komunitas wisata.'],
                    ['point' => 'Memberi kepastian harga yang jujur dan layanan yang bisa diandalkan.'],
                ],
                'missionPoints_en' => [
                    ['point' => 'Simplifying travel planning through intuitive technology.'],
                    ['point' => 'Empowering local partners — drivers, vendors, and tourism communities.'],
                    ['point' => 'Delivering honest pricing and reliable service you can count on.'],
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
