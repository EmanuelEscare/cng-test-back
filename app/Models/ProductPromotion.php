<?php

namespace App\Models;

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
}
