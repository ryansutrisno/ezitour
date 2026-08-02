<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: Reviews & ratings — gap #7.
 *
 * Verifies the review submission flow on a package show page: only users
 * with a paid booking can review, one review per (user, package), rating
 * validation, and only approved reviews are publicly visible.
 */
class ReviewTest extends TestCase
{
    use RefreshDatabase;

    private function createPackageWithPaidBooking(User $user): Package
    {
        $package = Package::factory()->create();

        Booking::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'travel_date' => now()->addDays(7),
            'total_amount' => 500000,
            'status' => 'paid',
            'pickup_location' => 'Hotel',
        ]);

        return $package;
    }

    public function test_guest_cannot_post_review(): void
    {
        $package = Package::factory()->create();

        $this->post(route('front.reviews.store', $package->slug), [
            'rating' => 5,
            'comment' => 'Bagus banget!',
        ])
            ->assertRedirect(route('login'));
    }

    public function test_user_without_paid_booking_cannot_review(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->create();

        $this->actingAs($user)
            ->post(route('front.reviews.store', $package->slug), [
                'rating' => 5,
                'comment' => 'Bagus!',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('reviews', [
            'user_id' => $user->id,
            'package_id' => $package->id,
        ]);
    }

    public function test_user_with_paid_booking_can_review(): void
    {
        $user = User::factory()->create();
        $package = $this->createPackageWithPaidBooking($user);

        $this->actingAs($user)
            ->post(route('front.reviews.store', $package->slug), [
                'rating' => 5,
                'comment' => 'Pengalaman luar biasa!',
            ])
            ->assertRedirect(route('front.packages.show', $package->slug));

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'package_id' => $package->id,
            'rating' => 5,
            'comment' => 'Pengalaman luar biasa!',
            'is_approved' => true,
        ]);
    }

    public function test_user_cannot_review_twice(): void
    {
        $user = User::factory()->create();
        $package = $this->createPackageWithPaidBooking($user);

        Review::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'rating' => 4,
            'comment' => 'Review pertama',
            'is_approved' => true,
        ]);

        $this->actingAs($user)
            ->post(route('front.reviews.store', $package->slug), [
                'rating' => 5,
                'comment' => 'Review kedua',
            ])
            ->assertForbidden();

        $this->assertEquals(1, Review::where('user_id', $user->id)->where('package_id', $package->id)->count());
    }

    public function test_only_approved_reviews_visible_on_show_page(): void
    {
        $package = Package::factory()->create();
        $approvedUser = User::factory()->create(['name' => 'Approved Reviewer']);
        $unapprovedUser = User::factory()->create(['name' => 'Hidden Reviewer']);

        Review::create([
            'user_id' => $approvedUser->id,
            'package_id' => $package->id,
            'rating' => 5,
            'comment' => 'Komentar yang terlihat publik',
            'is_approved' => true,
        ]);

        Review::create([
            'user_id' => $unapprovedUser->id,
            'package_id' => $package->id,
            'rating' => 3,
            'comment' => 'Komentar yang tersembunyi',
            'is_approved' => false,
        ]);

        $this->get(route('front.packages.show', $package->slug))
            ->assertOk()
            ->assertSee('Approved Reviewer')
            ->assertSee('Komentar yang terlihat publik')
            ->assertDontSee('Hidden Reviewer')
            ->assertDontSee('Komentar yang tersembunyi');
    }

    public function test_review_validates_rating_range(): void
    {
        $user = User::factory()->create();
        $package = $this->createPackageWithPaidBooking($user);

        // Rating 0 — below minimum
        $this->actingAs($user)
            ->post(route('front.reviews.store', $package->slug), ['rating' => 0])
            ->assertSessionHasErrors('rating');

        // Rating 6 — above maximum
        $this->actingAs($user)
            ->post(route('front.reviews.store', $package->slug), ['rating' => 6])
            ->assertSessionHasErrors('rating');

        $this->assertDatabaseMissing('reviews', [
            'user_id' => $user->id,
            'package_id' => $package->id,
        ]);
    }
}
