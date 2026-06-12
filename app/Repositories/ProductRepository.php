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
        $supplierId = isset($filters['supplier_id']) ? (int) $filters['supplier_id'] : null;
        $hasSupplierActiveFilter = array_key_exists('is_active', $filters);

        return Product::query()
            ->with('activePromotion')
            ->when(
                $supplierId !== null,
                fn (Builder $query): Builder => $query->with([
                    'suppliers' => fn ($query) => $query->whereKey($supplierId),
                ]),
            )
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
            ->when(
                $supplierId !== null || $hasSupplierActiveFilter,
                fn (Builder $query): Builder => $query->whereHas(
                    'suppliers',
                    function (Builder $query) use ($filters, $hasSupplierActiveFilter, $supplierId): void {
                        if ($supplierId !== null) {
                            $query->whereKey($supplierId);
                        }

                        if ($hasSupplierActiveFilter) {
                            $query->where('product_supplier.is_active', (bool) $filters['is_active']);
                        }
                    },
                ),
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
