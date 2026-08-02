<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Package;
use App\Models\PriceTier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: Tiered group pricing (Opsi A — PriceTier table).
 *
 * Verifies tier resolution logic, pricing breakdown calculation, the full
 * checkout flow with breakdown fields, E-ticket PDF generation with discount,
 * Filament admin tier management, and the booking-detail breakdown display.
 */
class PricingTierTest extends TestCase
{
    use RefreshDatabase;

    public function test_package_without_tiers_uses_linear_pricing(): void
    {
        $package = Package::factory()->create(['total_price' => 500000]);

        $pricing = $package->calculatePricing(3);

        $this->assertSame(0.0, $pricing['discount_amount']);
        $this->assertNull($pricing['tier']);
        $this->assertNull($pricing['tier_label']);
        $this->assertSame($pricing['base_subtotal'], $pricing['subtotal']);
        $this->assertEquals(500000.0 * 3, $pricing['subtotal']);
        $this->assertSame($pricing['price_per_pax'], $pricing['base_price_per_pax']);
    }

    public function test_package_with_tier_match_applies_discount(): void
    {
        $package = Package::factory()->create(['total_price' => 1000000]);

        PriceTier::create([
            'package_id' => $package->id,
            'name' => 'Diskon 10+ pax',
            'min_pax' => 10,
            'max_pax' => null,
            'price_per_pax' => 900000,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $pricing = $package->calculatePricing(12);

        $this->assertNotNull($pricing['tier']);
        $this->assertSame('Diskon 10+ pax', $pricing['tier_label']);
        $this->assertSame(900000.0, $pricing['price_per_pax']);
        $this->assertEquals(900000.0 * 12, $pricing['subtotal']);
        $this->assertEquals(1000000.0 * 12, $pricing['base_subtotal']);
        $this->assertSame(100000.0 * 12, $pricing['discount_amount']);
        $this->assertSame(10.0, $pricing['discount_percent']);
    }

    public function test_tier_with_null_max_pax_is_open_ended(): void
    {
        $package = Package::factory()->create(['total_price' => 500000]);

        PriceTier::create([
            'package_id' => $package->id,
            'name' => 'Rombongan Besar',
            'min_pax' => 10,
            'max_pax' => null,
            'price_per_pax' => 450000,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Open-ended tier should match any count >= min_pax.
        foreach ([15, 20, 100] as $participants) {
            $this->assertNotNull(
                $package->resolvePriceTier($participants),
                "Expected tier match for {$participants} participants."
            );
        }

        // Below min_pax should not match.
        $this->assertNull($package->resolvePriceTier(9));
    }

    public function test_first_matching_tier_wins_when_overlap(): void
    {
        $package = Package::factory()->create(['total_price' => 1000000]);

        // Two overlapping tiers — the one with lower sort_order wins.
        PriceTier::create([
            'package_id' => $package->id,
            'name' => 'Tier Pertama',
            'min_pax' => 5,
            'max_pax' => 20,
            'price_per_pax' => 800000,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        PriceTier::create([
            'package_id' => $package->id,
            'name' => 'Tier Kedua',
            'min_pax' => 8,
            'max_pax' => 20,
            'price_per_pax' => 700000,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // 10 pax matches both, but sort_order=1 wins.
        $tier = $package->resolvePriceTier(10);

        $this->assertNotNull($tier);
        $this->assertSame('Tier Pertama', $tier->name);
        $this->assertSame(800000.0, (float) $tier->price_per_pax);
    }

    public function test_checkout_creates_booking_with_breakdown_fields(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->create(['total_price' => 1000000]);

        PriceTier::create([
            'package_id' => $package->id,
            'name' => 'Promo Rombongan Besar',
            'min_pax' => 10,
            'max_pax' => null,
            'price_per_pax' => 900000,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(
            route('front.checkout.store', $package->slug),
            [
                'travel_date' => now()->addDays(7)->format('Y-m-d'),
                'participants' => 12,
                'pickup_location' => 'Hotel Tentrem Yogyakarta',
            ]
        );

        $response->assertRedirect(); // Redirects to payments.create

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'package_id' => $package->id,
            'participants' => 12,
            'total_amount' => 900000 * 12,
            'base_subtotal' => 1000000 * 12,
            'discount_amount' => 100000 * 12,
            'applied_tier_label' => 'Promo Rombongan Besar',
            'price_per_pax' => 900000,
        ]);
    }

    public function test_e_ticket_pdf_download_still_works_with_discount(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->create(['total_price' => 1000000]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'travel_date' => now()->addDays(7),
            'participants' => 12,
            'pickup_location' => 'Hotel Tentrem',
            'total_amount' => 900000 * 12,
            'base_subtotal' => 1000000 * 12,
            'discount_amount' => 100000 * 12,
            'applied_tier_label' => 'Promo Rombongan Besar',
            'price_per_pax' => 900000,
            'status' => 'paid',
        ]);

        $response = $this->actingAs($user)
            ->get(route('bookings.ticket', $booking));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_filament_package_resource_can_manage_tiers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $package = Package::factory()->create();

        PriceTier::create([
            'package_id' => $package->id,
            'name' => 'Existing Tier',
            'min_pax' => 5,
            'max_pax' => 9,
            'price_per_pax' => 450000,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get("/admin/packages/{$package->id}/edit")
            ->assertOk()
            ->assertSee('Tier Harga Khusus')
            ->assertSee('Existing Tier');
    }

    public function test_pricing_breakdown_visible_on_booking_detail_page(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->create(['name' => 'Liburan Bahagia']);

        $booking = Booking::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'travel_date' => now()->addDays(7),
            'participants' => 12,
            'pickup_location' => 'Hotel',
            'total_amount' => 900000 * 12,
            'base_subtotal' => 1000000 * 12,
            'discount_amount' => 100000 * 12,
            'applied_tier_label' => 'Promo Rombongan Besar',
            'price_per_pax' => 900000,
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->get(route('bookings.show', $booking))
            ->assertOk()
            ->assertSee('Subtotal')
            ->assertSee('Diskon')
            ->assertSee('Promo Rombongan Besar')
            ->assertSee(number_format((float) $booking->discount_amount, 0, ',', '.'));
    }
}
