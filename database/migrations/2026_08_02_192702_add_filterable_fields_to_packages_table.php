<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Region → category mapping heuristic used to backfill existing packages
     * whose names contain a known Indonesian destination keyword. Kept as a
     * constant so the up()/down() backfill logic stays in sync.
     */
    private const REGION_HEURISTICS = [
        // keyword => [region, category]
        'bali' => ['Bali', 'Pantai'],
        'balinese' => ['Bali', 'Pantai'],
        'jogja' => ['Yogyakarta', 'Budaya'],
        'yogyakarta' => ['Yogyakarta', 'Budaya'],
        'lombok' => ['Lombok', 'Pantai'],
        'raja ampat' => ['Raja Ampat', 'Petualangan'],
        'raja ampat.' => ['Raja Ampat', 'Petualangan'],
        'brom' => ['Jawa Timur', 'Pegunungan'],
        'bromo' => ['Jawa Timur', 'Pegunungan'],
        'dieng' => ['Jawa Tengah', 'Pegunungan'],
        'bandung' => ['Jawa Barat', 'Kuliner'],
        'jakarta' => ['Jakarta', 'Kuliner'],
        'labuan bajo' => ['Labuan Bajo', 'Petualangan'],
        'komodo' => ['Labuan Bajo', 'Petualangan'],
        'bunaken' => ['Sulawesi Utara', 'Pantai'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('region')->nullable()->after('description');
            $table->string('category')->nullable()->after('region');
            $table->unsignedTinyInteger('duration_days')->nullable()->after('category');
        });

        // Backfill existing packages using a simple name-based heuristic.
        // Idempotent: only writes when region/category are still NULL.
        if (Schema::hasTable('packages')) {
            $packages = DB::table('packages')
                ->whereNull('region')
                ->orWhereNull('category')
                ->orWhereNull('duration_days')
                ->get(['id', 'name']);

            foreach ($packages as $package) {
                $name = mb_strtolower((string) $package->name);
                $region = null;
                $category = null;

                foreach (self::REGION_HEURISTICS as $keyword => [$matchRegion, $matchCategory]) {
                    if (str_contains($name, $keyword)) {
                        $region = $matchRegion;
                        $category = $matchCategory;

                        break;
                    }
                }

                DB::table('packages')
                    ->where('id', $package->id)
                    ->update([
                        'region' => $region,
                        'category' => $category,
                        'duration_days' => 3,
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['region', 'category', 'duration_days']);
        });
    }
};
