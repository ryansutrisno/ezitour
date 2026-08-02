<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('base_subtotal', 12, 2)->nullable()->after('total_amount')
                ->comment('Original price × pax before any tier discount');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('base_subtotal')
                ->comment('Discount applied from a matched price tier');
            $table->string('applied_tier_label')->nullable()->after('discount_amount')
                ->comment('Snapshot of the matched tier name (historical record)');
            $table->decimal('price_per_pax', 12, 2)->nullable()->after('applied_tier_label')
                ->comment('Actual per-pax price charged (tier or base)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['base_subtotal', 'discount_amount', 'applied_tier_label', 'price_per_pax']);
        });
    }
};
