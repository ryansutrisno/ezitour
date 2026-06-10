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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('order_id')->unique();
            $table->string('snap_token')->nullable();
            $table->decimal('gross_amount', 12, 2);
            $table->string('payment_type')->nullable();
            $table->enum('transaction_status', [
                'pending',
                'paid',
                'failed',
                'expired',
                'superseded'
            ])->default('pending');
            $table->timestamp('transaction_time')->nullable();
            $table->timestamp('settlement_time')->nullable();
            $table->string('status_code')->nullable();
            $table->string('fraud_status')->nullable();
            $table->json('raw_notification')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('order_id');
            $table->index(['booking_id', 'transaction_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
