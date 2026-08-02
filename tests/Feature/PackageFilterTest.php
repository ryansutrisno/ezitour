<?php

namespace Tests\Feature;

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: Advanced package filter on public packages index.
 *
 * Verifies the faceted search by region, category, and duration buckets,
 * including keyword search regression and graceful empty-state rendering.
 */
class PackageFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_packages_index_displays_facet_ui(): void
    {
        Package::factory()->create([
            'name' => 'Paket Bali',
            'region' => 'Bali',
            'category' => 'Pantai',
            'duration_days' => 3,
        ]);

        $this->get(route('front.packages.index'))
            ->assertOk()
            ->assertSee('Filter Paket')
            ->assertSee('Wilayah')
            ->assertSee('Kategori')
            ->assertSee('Durasi')
            ->assertSee('Bali')
            ->assertSee('Pantai')
            ->assertSee('1-3 hari');
    }

    public function test_filter_by_region(): void
    {
        Package::factory()->create(['name' => 'Bali Tour', 'region' => 'Bali', 'category' => 'Pantai', 'duration_days' => 3]);
        Package::factory()->create(['name' => 'Jogja Tour', 'region' => 'Yogyakarta', 'category' => 'Budaya', 'duration_days' => 2]);
        Package::factory()->create(['name' => 'Lombok Tour', 'region' => 'Lombok', 'category' => 'Pantai', 'duration_days' => 4]);

        $this->get(route('front.packages.index', ['region' => 'Bali']))
            ->assertOk()
            ->assertSee('Bali Tour')
            ->assertDontSee('Jogja Tour')
            ->assertDontSee('Lombok Tour');
    }

    public function test_filter_by_category(): void
    {
        Package::factory()->create(['name' => 'Budaya Jogja', 'region' => 'Yogyakarta', 'category' => 'Budaya', 'duration_days' => 2]);
        Package::factory()->create(['name' => 'Pantai Bali', 'region' => 'Bali', 'category' => 'Pantai', 'duration_days' => 3]);
        Package::factory()->create(['name' => 'Gunung Bromo', 'region' => 'Jawa Timur', 'category' => 'Pegunungan', 'duration_days' => 2]);

        $this->get(route('front.packages.index', ['category' => 'Pantai']))
            ->assertOk()
            ->assertSee('Pantai Bali')
            ->assertDontSee('Budaya Jogja')
            ->assertDontSee('Gunung Bromo');
    }

    public function test_filter_by_duration_bucket(): void
    {
        Package::factory()->create(['name' => 'Libur Pendek', 'region' => 'Bali', 'category' => 'Pantai', 'duration_days' => 2]);
        Package::factory()->create(['name' => 'Petualangan Sedang', 'region' => 'Lombok', 'category' => 'Petualangan', 'duration_days' => 5]);
        Package::factory()->create(['name' => 'Ekspedisi Panjang', 'region' => 'Raja Ampat', 'category' => 'Petualangan', 'duration_days' => 10]);

        $this->get(route('front.packages.index', ['duration_min' => 1, 'duration_max' => 3]))
            ->assertOk()
            ->assertSee('Libur Pendek')
            ->assertDontSee('Petualangan Sedang')
            ->assertDontSee('Ekspedisi Panjang');
    }

    public function test_filter_combination(): void
    {
        Package::factory()->create(['name' => 'Bali Pantai 2 Hari', 'region' => 'Bali', 'category' => 'Pantai', 'duration_days' => 2]);
        Package::factory()->create(['name' => 'Bali Budaya 2 Hari', 'region' => 'Bali', 'category' => 'Budaya', 'duration_days' => 2]);
        Package::factory()->create(['name' => 'Bali Pantai 5 Hari', 'region' => 'Bali', 'category' => 'Pantai', 'duration_days' => 5]);
        Package::factory()->create(['name' => 'Lombok Pantai 2 Hari', 'region' => 'Lombok', 'category' => 'Pantai', 'duration_days' => 2]);

        $this->get(route('front.packages.index', [
            'region' => 'Bali',
            'category' => 'Pantai',
            'duration_min' => 1,
            'duration_max' => 3,
        ]))
            ->assertOk()
            ->assertSee('Bali Pantai 2 Hari')
            ->assertDontSee('Bali Budaya 2 Hari')
            ->assertDontSee('Bali Pantai 5 Hari')
            ->assertDontSee('Lombok Pantai 2 Hari');
    }

    public function test_keyword_search_still_works(): void
    {
        Package::factory()->create(['name' => 'Liburan Keluarga Bali', 'description' => 'Perjalanan menyenangkan', 'region' => 'Bali', 'category' => 'Pantai', 'duration_days' => 3]);
        Package::factory()->create(['name' => 'Tour Lombok', 'description' => 'Petualangan laut', 'region' => 'Lombok', 'category' => 'Pantai', 'duration_days' => 4]);

        $this->get(route('front.packages.index', ['keyword' => 'Keluarga']))
            ->assertOk()
            ->assertSee('Liburan Keluarga Bali')
            ->assertDontSee('Tour Lombok');
    }

    public function test_empty_result_renders_gracefully(): void
    {
        Package::factory()->create(['name' => 'Bali Tour', 'region' => 'Bali', 'category' => 'Pantai', 'duration_days' => 3]);

        $this->get(route('front.packages.index', ['region' => 'Raja Ampat']))
            ->assertOk()
            ->assertSee('Tidak ada paket yang cocok');
    }
}
