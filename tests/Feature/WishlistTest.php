<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: Wishlist (Sprint 7, gap #12) — heart-toggle saved packages.
 *
 * Covers guest guards, toggle add/remove idempotency, listing, deletion,
 * and 404 handling for unknown slugs. Stays clear of the coupon (PR #11)
 * and tier pricing (PR #10) surfaces.
 */
class WishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_wishlist_page(): void
    {
        $this->get(route('front.wishlist.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_toggle_wishlist(): void
    {
        $package = Package::factory()->create();

        $this->post(route('front.wishlist.toggle', $package->slug))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_add_package_to_wishlist(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('front.wishlist.toggle', $package->slug));

        $response->assertOk()
            ->assertJson([
                'is_favorited' => true,
                'message' => 'Ditambahkan ke wishlist',
            ]);

        $this->assertDatabaseHas('favorite_package_user', [
            'user_id' => $user->id,
            'package_id' => $package->id,
        ]);
    }

    public function test_toggle_removes_from_wishlist_on_second_call(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->create();

        // First toggle → add.
        $this->actingAs($user)
            ->postJson(route('front.wishlist.toggle', $package->slug))
            ->assertOk()
            ->assertJson(['is_favorited' => true]);

        // Second toggle → remove.
        $this->actingAs($user)
            ->postJson(route('front.wishlist.toggle', $package->slug))
            ->assertOk()
            ->assertJson([
                'is_favorited' => false,
                'message' => 'Dihapus dari wishlist',
            ]);

        $this->assertDatabaseMissing('favorite_package_user', [
            'user_id' => $user->id,
            'package_id' => $package->id,
        ]);
    }

    public function test_wishlist_page_lists_only_favorited_packages(): void
    {
        $user = User::factory()->create();
        $favorited = Package::factory()->create(['name' => 'Paket Favorit Satu']);
        $other = Package::factory()->create(['name' => 'Paket Lain Tanpa Wishlist']);

        $user->favorites()->attach([$favorited->id]);

        $this->actingAs($user)
            ->get(route('front.wishlist.index'))
            ->assertOk()
            ->assertSee($favorited->name)
            ->assertDontSee($other->name);
    }

    public function test_destroy_removes_package_from_wishlist(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->create();
        $user->favorites()->attach([$package->id]);

        $this->actingAs($user)
            ->delete(route('front.wishlist.destroy', $package->slug))
            ->assertRedirect();

        $this->assertDatabaseMissing('favorite_package_user', [
            'user_id' => $user->id,
            'package_id' => $package->id,
        ]);
    }

    public function test_invalid_package_slug_returns_404_on_toggle(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('front.wishlist.toggle', 'slug-yang-tidak-ada'))
            ->assertNotFound();
    }
}
