<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE faqs SET category = JSON_OBJECT('id', COALESCE(category, 'Umum'))");
    }

    public function down(): void
    {
        // Keep the translated JSON values when rolling back this migration.
    }
};
