<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create a review for the given package.
     *
     * Rules (all must be true):
     *  1. User is authenticated (handled by the auth middleware, but we
     *     double-check for safety / when called manually).
     *  2. User has at least one paid booking for this package — only
     *     completed travel can be reviewed.
     *  3. User has not already reviewed this package (one review per user
     *     per package, also enforced by a DB unique constraint).
     *
     * @param  Package  $package  Route-injected via controller (not via policy
     *                            auto-binding, so we pass it explicitly).
     */
    public function create(User $user, Package $package): Response|bool
    {
        $hasPaidBooking = Booking::query()
            ->where('user_id', $user->id)
            ->where('package_id', $package->id)
            ->where('status', 'paid')
            ->exists();

        if (! $hasPaidBooking) {
            return Response::denyWithStatus(
                403,
                'Anda hanya dapat memberikan ulasan untuk paket yang sudah Anda pesan dan bayar.'
            );
        }

        $alreadyReviewed = Review::query()
            ->where('user_id', $user->id)
            ->where('package_id', $package->id)
            ->exists();

        if ($alreadyReviewed) {
            return Response::denyWithStatus(
                403,
                'Anda sudah memberikan ulasan untuk paket ini.'
            );
        }

        return true;
    }

    /**
     * Determine whether the user can update the review (owner or admin).
     */
    public function update(User $user, Review $review): bool
    {
        return $review->user_id === $user->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the review (owner or admin).
     */
    public function delete(User $user, Review $review): bool
    {
        return $review->user_id === $user->id || $user->isAdmin();
    }
}
