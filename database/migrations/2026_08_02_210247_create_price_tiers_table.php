<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tier resolution: pick the FIRST tier where
     *   min_pax <= participants AND (max_pax IS NULL OR max_pax >= participants)
     * sorted ascending by sort_order. If no tier matches, fall back to the
     * package's base total_price (linear pricing).
     */
    public function up(): void
    {
        Schema::create('price_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
            $table->string('name')->comment('Human-readable label, e.g. "Diskon Rombongan 10+ pax"');
            $table->unsignedSmallInteger('min_pax');
            $table->unsignedSmallInteger('max_pax')->nullable()->comment('Null = open-ended (e.g. "20+ pax")');
            $table->decimal('price_per_pax', 12, 2);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['package_id', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_tiers');
    }
};
