<?php

namespace App\Repositories;

use App\Contracts\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Get a paginated product list.
     *
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<Product>
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $sort = (string) ($filters['sort'] ?? 'created_at');
        $direction = (string) ($filters['direction'] ?? 'desc');

        return Product::query()
            ->with('activePromotion')
            ->withExists([
                'promotions as has_active_promotion' => fn (Builder $query): Builder => $query->active(),
            ])
            ->when(
                $filters['search'] ?? null,
                fn (Builder $query, string $search): Builder => $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                }),
            )
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Load a product for API detail responses.
     */
    public function find(Product $product): Product
    {
        return $product
            ->load([
                'activePromotion',
                'promotions' => fn ($query) => $query
                    ->latest('promotion_started_at')
                    ->latest('id'),
                'suppliers',
            ])
            ->loadExists([
                'promotions as has_active_promotion' => fn (Builder $query): Builder => $query->active(),
            ]);
    }

    /**
     * Create a product.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product
    {
        $product = Product::create($data);

        return $this->find($product->refresh());
    }

    /**
     * Update a product.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $this->find($product->refresh());
    }

    /**
     * Soft delete a product.
     */
    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }
}
