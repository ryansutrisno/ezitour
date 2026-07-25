<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add profile columns to the users table.
     *
     * `phone` has been written by CheckoutController::register() since the
     * checkout flow launched, but the column was missing from the schema, so
     * the value was silently dropped via mass-assignment guard. Adding it here
     * (together with the fillable entry on the User model) finally persists it.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar_url')->nullable()->after('phone');
            $table->string('role')->default('user')->after('avatar_url');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['phone', 'avatar_url', 'role']);
        });
    }
};
