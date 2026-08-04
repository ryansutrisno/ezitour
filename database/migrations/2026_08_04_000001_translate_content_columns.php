<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convert translatable content columns to JSON after wrapping existing
     * values in the default Indonesian locale.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE packages MODIFY name LONGTEXT NOT NULL');
        DB::statement("UPDATE packages SET name = JSON_OBJECT('id', name)");
        DB::statement('ALTER TABLE packages MODIFY name JSON NOT NULL');

        DB::statement('ALTER TABLE packages MODIFY description LONGTEXT NULL');
        DB::statement("UPDATE packages SET description = JSON_OBJECT('id', description) WHERE description IS NOT NULL");
        DB::statement('ALTER TABLE packages MODIFY description JSON NULL');

        DB::statement('ALTER TABLE faqs MODIFY question LONGTEXT NOT NULL');
        DB::statement("UPDATE faqs SET question = JSON_OBJECT('id', question)");
        DB::statement('ALTER TABLE faqs MODIFY question JSON NOT NULL');

        DB::statement('ALTER TABLE faqs MODIFY answer LONGTEXT NOT NULL');
        DB::statement("UPDATE faqs SET answer = JSON_OBJECT('id', answer)");
        DB::statement('ALTER TABLE faqs MODIFY answer JSON NOT NULL');

        DB::statement('ALTER TABLE testimonials MODIFY quote LONGTEXT NOT NULL');
        DB::statement("UPDATE testimonials SET quote = JSON_OBJECT('id', quote)");
        DB::statement('ALTER TABLE testimonials MODIFY quote JSON NOT NULL');
    }

    /**
     * Restore the original string and text columns using the Indonesian
     * translation as the legacy scalar value.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE packages MODIFY name LONGTEXT NOT NULL');
        DB::statement("UPDATE packages SET name = COALESCE(JSON_UNQUOTE(JSON_EXTRACT(name, '$.id')), '')");
        DB::statement('ALTER TABLE packages MODIFY name VARCHAR(255) NOT NULL');

        DB::statement('ALTER TABLE packages MODIFY description LONGTEXT NULL');
        DB::statement("UPDATE packages SET description = JSON_UNQUOTE(JSON_EXTRACT(description, '$.id')) WHERE description IS NOT NULL");
        DB::statement('ALTER TABLE packages MODIFY description TEXT NULL');

        DB::statement('ALTER TABLE faqs MODIFY question LONGTEXT NOT NULL');
        DB::statement("UPDATE faqs SET question = COALESCE(JSON_UNQUOTE(JSON_EXTRACT(question, '$.id')), '')");
        DB::statement('ALTER TABLE faqs MODIFY question VARCHAR(255) NOT NULL');

        DB::statement('ALTER TABLE faqs MODIFY answer LONGTEXT NOT NULL');
        DB::statement("UPDATE faqs SET answer = COALESCE(JSON_UNQUOTE(JSON_EXTRACT(answer, '$.id')), '')");
        DB::statement('ALTER TABLE faqs MODIFY answer TEXT NOT NULL');

        DB::statement('ALTER TABLE testimonials MODIFY quote LONGTEXT NOT NULL');
        DB::statement("UPDATE testimonials SET quote = COALESCE(JSON_UNQUOTE(JSON_EXTRACT(quote, '$.id')), '')");
        DB::statement('ALTER TABLE testimonials MODIFY quote TEXT NOT NULL');
    }
};
