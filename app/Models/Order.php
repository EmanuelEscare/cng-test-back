<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Product> $products
 * @property-read \Illuminate\Database\Eloquent\Collection<int, OrderProduct> $items
 */
class Order extends Model
{
    /**
     * Get the order product pivot records.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * Get the products sold in the order.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->using(OrderProduct::class)
            ->withPivot(['sold_at', 'quantity']);
    }
}
