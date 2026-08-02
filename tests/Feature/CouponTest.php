<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Package;
use App\Models\PriceTier;
use App\Models\User;
use App\Services\CouponService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: Promo/Coupon codes (gap #11) — stacks with tiered pricing.
 *
 * Order of discounts: base_subtotal → tier discount → coupon discount → total_amount.
 */
class CouponTest extends TestCase
{
    use RefreshDatabase;

    private function couponService(): CouponService
    {
        return app(CouponService::class);
    }

    public function test_fixed_amount_coupon_applies_correctly(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::create([
            'code' => 'FIXED50K',
            'type' => 'fixed',
            'value' => 50000,
            'usage_limit_per_user' => 1,
            'is_active' => true,
            'times_used' => 0,
        ]);

        $result = $this->couponService()->validate('FIXED50K', 600000, $user);

        $this->assertTrue($result['valid']);
        $this->assertSame(50000.0, $result['discount']);
    }

    public function test_percentage_coupon_with_max_discount_cap(): void
    {
        $user = User::factory()->create();
        // 20% off, capped at 100,000. On 1,000,000 subtotal → 200,000 raw → capped to 100,000.
        $coupon = Coupon::create([
            'code' => 'CAPPED20',
            'type' => 'percentage',
            'value' => 20,
            'max_discount' => 100000,
            'usage_limit_per_user' => 1,
            'is_active' => true,
            'times_used' => 0,
        ]);

        $result = $this->couponService()->validate('CAPPED20', 1000000, $user);

        $this->assertTrue($result['valid']);
        $this->assertSame(100000.0, $result['discount']);
    }

    public function test_percentage_coupon_without_cap_uses_full_subtotal(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::create([
            'code' => 'NOCAP15',
            'type' => 'percentage',
            'value' => 15,
            'usage_limit_per_user' => 1,
            'is_active' => true,
            'times_used' => 0,
        ]);

        $result = $this->couponService()->validate('NOCAP15', 800000, $user);

        $this->assertTrue($result['valid']);
        $this->assertSame(120000.0, $result['discount']); // 15% of 800k
    }

    public function test_coupon_below_min_spend_rejected(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::create([
            'code' => 'MIN500K',
            'type' => 'fixed',
            'value' => 50000,
            'min_spend' => 500000,
            'usage_limit_per_user' => 1,
            'is_active' => true,
            'times_used' => 0,
        ]);

        $result = $this->couponService()->validate('MIN500K', 300000, $user);

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('Minimal belanja', $result['error']);
    }

    public function test_coupon_per_coupon_usage_limit_enforced(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::create([
            'code' => 'LIMITED',
            'type' => 'fixed',
            'value' => 50000,
            'usage_limit_per_coupon' => 10,
            'usage_limit_per_user' => 100,
            'is_active' => true,
            'times_used' => 10,
        ]);

        $result = $this->couponService()->validate('LIMITED', 600000, $user);

        $this->assertFalse($result['valid']);
        $this->assertSame('Promo sudah mencapai batas penggunaan total.', $result['error']);
    }

    public function test_coupon_per_user_usage_limit_enforced(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::create([
            'code' => 'USERLIMIT',
            'type' => 'fixed',
            'value' => 50000,
            'usage_limit_per_user' => 1,
            'is_active' => true,
            'times_used' => 0,
        ]);

        // Simulate user has already used it once.
        $coupon->users()->attach($user, ['times_used' => 1]);

        $result = $this->couponService()->validate('USERLIMIT', 600000, $user);

        $this->assertFalse($result['valid']);
        $this->assertSame('Kamu sudah menggunakan promo ini.', $result['error']);
    }

    public function test_expired_coupon_rejected(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::create([
            'code' => 'EXPIRED',
            'type' => 'fixed',
            'value' => 50000,
            'usage_limit_per_user' => 1,
            'valid_until' => now()->subDay(),
            'is_active' => true,
            'times_used' => 0,
        ]);

        $result = $this->couponService()->validate('EXPIRED', 600000, $user);

        $this->assertFalse($result['valid']);
        $this->assertSame('Promo sudah kadaluarsa.', $result['error']);
    }

    public function test_inactive_coupon_rejected(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::create([
            'code' => 'INACTIVE',
            'type' => 'fixed',
            'value' => 50000,
            'usage_limit_per_user' => 1,
            'is_active' => false,
            'times_used' => 0,
        ]);

        $result = $this->couponService()->validate('INACTIVE', 600000, $user);

        $this->assertFalse($result['valid']);
        $this->assertSame('Promo sudah tidak aktif.', $result['error']);
    }

    public function test_checkout_applies_coupon_and_persists_breakdown(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->create(['total_price' => 500000]);

        $coupon = Coupon::create([
            'code' => 'HEMAT50K',
            'type' => 'fixed',
            'value' => 50000,
            'min_spend' => 300000,
            'usage_limit_per_user' => 1,
            'is_active' => true,
            'times_used' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('front.checkout.store', $package->slug), [
                'travel_date' => now()->addDays(7)->format('Y-m-d'),
                'participants' => 2,
                'pickup_location' => 'Hotel Tentrem',
                'coupon_code' => 'HEMAT50K',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'package_id' => $package->id,
            'coupon_id' => $coupon->id,
            'coupon_code' => 'HEMAT50K',
            'coupon_discount_amount' => 50000,
        ]);

        // total = 500000 * 2 - 50000 = 950000
        $booking = Booking::where('user_id', $user->id)->first();
        $this->assertSame(950000.0, (float) $booking->total_amount);

        // Usage incremented.
        $this->assertSame(1, (int) Coupon::find($coupon->id)->times_used);
        $this->assertSame(1, (int) $coupon->fresh()->users()->where('user_id', $user->id)->value('times_used'));
    }

    public function test_coupon_discount_visible_on_booking_detail_and_pdf(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->create(['name' => 'Paket Bahagia']);
        $coupon = Coupon::create([
            'code' => 'PROMOABC',
            'type' => 'fixed',
            'value' => 50000,
            'usage_limit_per_user' => 1,
            'is_active' => true,
            'times_used' => 0,
        ]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'travel_date' => now()->addDays(7),
            'participants' => 2,
            'pickup_location' => 'Hotel',
            'total_amount' => 950000,
            'base_subtotal' => 1000000,
            'coupon_id' => $coupon->id,
            'coupon_code' => 'PROMOABC',
            'coupon_discount_amount' => 50000,
            'status' => 'paid',
        ]);

        $this->actingAs($user)
            ->get(route('bookings.show', $booking))
            ->assertOk()
            ->assertSee('Diskon Promo')
            ->assertSee('PROMOABC');

        $this->actingAs($user)
            ->get(route('bookings.ticket', $booking))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_invalid_coupon_code_returns_error_message(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('front.checkout.coupon', $package->slug), [
                'code' => 'DOESNOTEXIST',
                'participants' => 2,
            ]);

        $response->assertOk();
        $response->assertJson(['valid' => false]);
        $response->assertJsonPath('error', 'Promo tidak ditemukan.');
    }

    public function test_coupon_stacks_with_tier_discount(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->create(['total_price' => 1000000]);

        PriceTier::create([
            'package_id' => $package->id,
            'name' => 'Rombongan Besar',
            'min_pax' => 10,
            'max_pax' => null,
            'price_per_pax' => 900000,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $coupon = Coupon::create([
            'code' => 'STACKME',
            'type' => 'fixed',
            'value' => 50000,
            'min_spend' => 500000,
            'usage_limit_per_user' => 1,
            'is_active' => true,
            'times_used' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('front.checkout.store', $package->slug), [
                'travel_date' => now()->addDays(7)->format('Y-m-d'),
                'participants' => 12,
                'pickup_location' => 'Hotel',
                'coupon_code' => 'STACKME',
            ])
            ->assertRedirect();

        $booking = Booking::where('user_id', $user->id)->first();

        // base = 1,000,000 * 12 = 12,000,000
        $this->assertSame(12000000.0, (float) $booking->base_subtotal);
        // tier discount = 100,000 * 12 = 1,200,000
        $this->assertSame(1200000.0, (float) $booking->discount_amount);
        // coupon discount = 50,000
        $this->assertSame(50000.0, (float) $booking->coupon_discount_amount);
        // total = (900,000 * 12) - 50,000 = 10,750,000
        $this->assertSame(10750000.0, (float) $booking->total_amount);
    }
}
