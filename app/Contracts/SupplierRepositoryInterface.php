<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface SupplierRepositoryInterface
{
    /**
     * Get suppliers available for product filters.
     *
     * @param  array<string, mixed>  $filters
     * @return Collection<int, \App\Models\Supplier>
     */
    public function forProductFilters(array $filters = []): Collection;
}
