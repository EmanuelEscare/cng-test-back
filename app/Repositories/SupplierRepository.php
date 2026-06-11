<?php

namespace App\Repositories;

use App\Contracts\SupplierRepositoryInterface;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SupplierRepository implements SupplierRepositoryInterface
{
    /**
     * Get suppliers available for product filters.
     *
     * @param  array<string, mixed>  $filters
     * @return Collection<int, Supplier>
     */
    public function forProductFilters(array $filters = []): Collection
    {
        return Supplier::query()
            ->withCount([
                'products',
                'products as active_products_count' => fn (Builder $query): Builder => $query
                    ->where('product_supplier.is_active', true),
            ])
            ->when(
                $filters['search'] ?? null,
                fn (Builder $query, string $search): Builder => $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                }),
            )
            ->when(
                array_key_exists('has_products', $filters),
                fn (Builder $query): Builder => ((bool) $filters['has_products'])
                    ? $query->has('products')
                    : $query->doesntHave('products'),
            )
            ->orderBy('name')
            ->get();
    }
}
