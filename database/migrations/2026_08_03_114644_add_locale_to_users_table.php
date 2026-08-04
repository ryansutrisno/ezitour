<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the `locale` column to users for Phase 1 i18n preference persistence.
     *
     * Default is 'id' (Indonesian — the primary locale of the app). The
     * SetLocale middleware reads this column for authenticated visitors and
     * overrides session/Accept-Language detection. Phase 1 only stores UI
     * preference; DB content translation is Phase 2.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'locale')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('locale', 5)->default('id')->after('role');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'locale')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('locale');
            });
        }
    }
};
