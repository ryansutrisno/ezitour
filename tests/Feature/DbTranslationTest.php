<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DbTranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_package_name_resolves_in_indonesian(): void
    {
        App::setLocale('id');
        $package = Package::create([
            'name' => ['id' => 'Paket A', 'en' => 'Package A'],
            'description' => ['id' => 'Deskripsi', 'en' => 'Description'],
            'slug' => 'paket-a',
            'total_price' => 100000,
        ]);

        $this->assertSame('Paket A', $package->name);
    }

    public function test_package_name_resolves_in_english(): void
    {
        App::setLocale('en');
        $package = Package::create([
            'name' => ['id' => 'Paket A', 'en' => 'Package A'],
            'description' => ['id' => 'Deskripsi', 'en' => 'Description'],
            'slug' => 'paket-a',
            'total_price' => 100000,
        ]);

        $this->assertSame('Package A', $package->name);
    }

    public function test_faq_translation_fallback_to_indonesian(): void
    {
        $faq = Faq::create([
            'question' => ['id' => 'Pertanyaan'],
            'answer' => ['id' => 'Jawaban'],
        ]);

        App::setLocale('fr');

        $this->assertSame('Pertanyaan', $faq->question);
    }

    public function test_existing_data_backfill_is_valid_json(): void
    {
        $package = Package::create([
            'name' => ['id' => 'Paket A'],
            'description' => ['id' => 'Deskripsi'],
            'slug' => 'paket-a',
            'total_price' => 100000,
        ]);

        $result = DB::selectOne('SELECT JSON_VALID(name) AS valid FROM packages WHERE id = ?', [$package->id]);

        $this->assertSame(1, (int) $result->valid);
    }
}
