<?php

namespace App\Http\Controllers;

use App\Contracts\SupplierRepositoryInterface;
use App\Http\Requests\ListSuppliersRequest;
use App\Http\Resources\SupplierResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierRepositoryInterface $suppliers,
    ) {}

    /**
     * Display suppliers available for product filters.
     */
    public function index(ListSuppliersRequest $request): JsonResponse
    {
        $suppliers = $this->suppliers->forProductFilters($request->validated());

        return ApiResponse::resource(
            SupplierResource::collection($suppliers),
            'Suppliers retrieved successfully.',
        );
    }
}
