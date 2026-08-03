<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(PackageItem::class)->orderBy('sequence_order');
    }

    public function destinations()
    {
        return $this->belongsToMany(Destination::class, 'package_items')
            ->withPivot('sequence_order')
            ->orderByPivot('sequence_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Users who have saved this package as a favorite (wishlist).
     * Inverse of {@link User::favorites()}.
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_package_user')
            ->withTimestamps();
    }

    /**
     * Whether the currently authenticated user has favorited this package.
     *
     * Uses the loaded auth user's cached favorites collection when present,
     * falling back to a single exists() check otherwise. Safe to call on
     * paginated card lists — `auth()->user()->favorites` is cached on the
     * User instance after the first access within a request.
     */
    public function getIsFavoritedAttribute(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->relationLoaded('favorites')
            ? $user->favorites->contains($this->getKey())
            : $this->favoritedBy()->where('user_id', $user->getKey())->exists();
    }

    /**
     * Only approved (publicly visible) reviews for this package,
     * newest first.
     */
    public function approvedReviews(): HasMany
    {
        return $this->reviews()->approved()->latest();
    }

    /**
     * Active price tiers, ordered ascending by sort_order.
     */
    public function priceTiers(): HasMany
    {
        return $this->hasMany(PriceTier::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * Resolve the applicable price tier for a given participant count.
     *
     * Picks the first tier (sorted ASC by sort_order) where
     *   min_pax <= participants AND (max_pax IS NULL OR max_pax >= participants).
     * Returns null when no tiers exist or none match — caller falls back to linear.
     */
    public function resolvePriceTier(int $participants): ?PriceTier
    {
        return $this->priceTiers()
            ->where('min_pax', '<=', $participants)
            ->where(function ($query) use ($participants) {
                $query->whereNull('max_pax')->orWhere('max_pax', '>=', $participants);
            })
            ->first();
    }

    /**
     * Calculate full pricing breakdown for a participant count.
     *
     * @return array{
     *     price_per_pax: float,
     *     base_price_per_pax: float,
     *     subtotal: float,
     *     base_subtotal: float,
     *     discount_amount: float,
     *     discount_percent: float,
     *     tier: \App\Models\PriceTier|null,
     *     tier_label: string|null,
     *     participants: int,
     * }
     */
    public function calculatePricing(int $participants): array
    {
        $basePricePerPax = (float) $this->total_price;
        $tier = $this->resolvePriceTier($participants);
        $pricePerPax = $tier ? (float) $tier->price_per_pax : $basePricePerPax;

        $baseSubtotal = $basePricePerPax * $participants;
        $subtotal = $pricePerPax * $participants;
        $discountAmount = max(0.0, $baseSubtotal - $subtotal);
        $discountPercent = $baseSubtotal > 0 ? ($discountAmount / $baseSubtotal) * 100 : 0.0;

        return [
            'price_per_pax' => $pricePerPax,
            'base_price_per_pax' => $basePricePerPax,
            'subtotal' => $subtotal,
            'base_subtotal' => $baseSubtotal,
            'discount_amount' => $discountAmount,
            'discount_percent' => round($discountPercent, 1),
            'tier' => $tier,
            'tier_label' => $tier?->name,
            'participants' => $participants,
        ];
    }

    /**
     * Apply faceted search filters to the query.
     *
     * Supported keys:
     *  - keyword:     existing LIKE search on name + description
     *  - region:      exact match on region
     *  - category:    exact match on category
     *  - duration_min / duration_max: inclusive range on duration_days
     *
     * @param  array<string, mixed>  $filters
     */
    #[Scope]
    protected function filter(Builder $query, array $filters): void
    {
        $query->when(filled($filters['keyword'] ?? null), function (Builder $q) use ($filters): void {
            $term = '%'.$filters['keyword'].'%';

            $q->where(function (Builder $inner) use ($term): void {
                $inner->where('name', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        });

        $query->when(filled($filters['region'] ?? null), fn (Builder $q) => $q->where('region', $filters['region']));

        $query->when(filled($filters['category'] ?? null), fn (Builder $q) => $q->where('category', $filters['category']));

        $min = $filters['duration_min'] ?? null;
        $max = $filters['duration_max'] ?? null;

        $query->when(filled($min) && filled($max), fn (Builder $q) => $q->whereBetween('duration_days', [(int) $min, (int) $max]));
        $query->when(filled($min) && ! filled($max), fn (Builder $q) => $q->where('duration_days', '>=', (int) $min));
        $query->when(! filled($min) && filled($max), fn (Builder $q) => $q->where('duration_days', '<=', (int) $max));
    }
}
