<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $sku
 * @property string|null $description
 * @property string $price
 * @property string $currency
 * @property bool|null $has_active_promotion
 * @property-read ProductPromotion|null $activePromotion
 * @property-read \Illuminate\Database\Eloquent\Collection<int, OrderProduct> $orderItems
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Order> $orders
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductPromotion> $promotions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Supplier> $suppliers
 */
class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'currency',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    /**
     * Get the promotions assigned to the product.
     */
    public function promotions(): HasMany
    {
        return $this->hasMany(ProductPromotion::class);
    }

    /**
     * Get the order product pivot records.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * Get the orders where the product was sold.
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)
            ->using(OrderProduct::class)
            ->withPivot(['sold_at', 'quantity']);
    }

    /**
     * Get the currently active promotion for the product.
     */
    public function activePromotion(): HasOne
    {
        return $this->hasOne(ProductPromotion::class)
            ->active()
            ->latestOfMany();
    }

    /**
     * Get the suppliers assigned to the product.
     */
    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class)
            ->using(ProductSupplier::class)
            ->withPivot(['id', 'is_active'])
            ->withTimestamps();
    }
}
