<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds expiry_time column to transactions table for payment expiration handling.
     * Requirements: 9.1 - Set expiry_time when creating transaction
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->timestamp('expiry_time')->nullable()->after('settlement_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('expiry_time');
        });
    }
};
