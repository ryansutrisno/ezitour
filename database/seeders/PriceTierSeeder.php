<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\PriceTier;
use Illuminate\Database\Seeder;

class PriceTierSeeder extends Seeder
{
    /**
     * Seed sample group-pricing tiers for existing Jogja packages.
     *
     * Two tiers per package:
     *  - "Promo Rombongan Kecil"   5–9 pax  → 5% off
     *  - "Promo Rombongan Besar"  10+ pax  → 10% off (open-ended)
     *
     * Idempotent: keyed by (package_id, name).
     */
    public function run(): void
    {
        $packages = Package::query()
            ->whereIn('name', [
                'Jogja Heritage Tour',
                'Jogja Instagramable Spot',
                'Jogja Beach & Sunset',
            ])
            ->get();

        foreach ($packages as $package) {
            $base = (float) $package->total_price;

            PriceTier::updateOrCreate(
                ['package_id' => $package->id, 'name' => 'Promo Rombongan Kecil'],
                [
                    'min_pax' => 5,
                    'max_pax' => 9,
                    'price_per_pax' => round($base * 0.95, 2),
                    'sort_order' => 1,
                    'is_active' => true,
                ]
            );

            PriceTier::updateOrCreate(
                ['package_id' => $package->id, 'name' => 'Promo Rombongan Besar'],
                [
                    'min_pax' => 10,
                    'max_pax' => null,
                    'price_per_pax' => round($base * 0.90, 2),
                    'sort_order' => 2,
                    'is_active' => true,
                ]
            );
        }
    }
}
