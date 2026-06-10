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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
            $table->foreignId('car_id')->nullable()->constrained('cars')->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null');
            $table->date('travel_date');
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['pending', 'paid', 'completed', 'cancelled'])->default('pending');
            $table->text('pickup_location');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
