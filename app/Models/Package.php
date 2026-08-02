<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
