<?php

namespace App\Concerns;

use App\Models\Review;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait WithReviews
{
    protected ?Collection $ratings = null;

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function ratings(): Collection
    {
        if (! $this->ratings) {
            $this->ratings = $this->reviews()
                ->select('rating', DB::raw('COUNT(rating) as total'))
                ->groupBy('rating')
                ->get()
                ->mapWithKeys(fn (Review $review) => [
                    $review->rating => $review->total,
                ]);
        }

        return $this->ratings;
    }

    public function averageRating(): int
    {
        $totalRating = $this->ratings()
            ->map(fn (int $total, int $rating) => $total * $rating)
            ->sum();

        if ($totalRating === 0) {
            return 0;
        }

        return floor($totalRating / $this->ratings()->sum());
    }
}
