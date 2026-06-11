<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $product_id
 * @property \Illuminate\Support\Carbon|null $promotion_started_at
 * @property \Illuminate\Support\Carbon|null $promotion_ends_at
 * @property string|null $promotion
 * @property-read Product $product
 *
 * @method static Builder<static> active()
 */
class ProductPromotion extends Model
{
    /** @use HasFactory<\Database\Factories\ProductPromotionFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'promotion_started_at',
        'promotion_ends_at',
        'promotion',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'promotion_started_at' => 'datetime',
            'promotion_ends_at' => 'datetime',
        ];
    }

    /**
     * Get the product that owns the promotion.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope the query to promotions active at the current time.
     *
     * @param  Builder<ProductPromotion>  $query
     * @return Builder<ProductPromotion>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('promotion_started_at')
                    ->orWhere('promotion_started_at', '<=', now());
            })
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('promotion_ends_at')
                    ->orWhere('promotion_ends_at', '>=', now());
            });
    }

    /**
     * Determine if the promotion is active.
     */
    public function isActive(): bool
    {
        return ($this->promotion_started_at === null || $this->promotion_started_at->lte(now()))
            && ($this->promotion_ends_at === null || $this->promotion_ends_at->gte(now()));
    }
}
