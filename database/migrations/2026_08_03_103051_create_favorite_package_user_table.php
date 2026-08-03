<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pivot table for the many-to-many "wishlist" relationship between
     * users and packages. Composite primary key prevents a user from
     * favoriting the same package twice.
     */
    public function up(): void
    {
        Schema::create('favorite_package_user', function (Blueprint $table) {
            $table->foreignId('package_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->primary(['package_id', 'user_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite_package_user');
    }
};
