<?php

namespace App\Http\Controllers;

use App\Contracts\ProductRepositoryInterface;
use App\Http\Requests\ListProductsRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {}

    /**
     * Display a listing of products.
     */
    public function index(ListProductsRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 15);

        $products = $this->products->paginate($filters, $perPage);

        return ApiResponse::resource(
            ProductResource::collection($products),
            'Products retrieved successfully.',
        );
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = DB::transaction(
            fn (): Product => $this->products->create($request->validated()),
        );

        return ApiResponse::resource(
            ProductResource::make($product),
            'Product created successfully.',
            JsonResponse::HTTP_CREATED,
        );
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): JsonResponse
    {
        return ApiResponse::resource(
            ProductResource::make($this->products->find($product)),
            'Product retrieved successfully.',
        );
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = DB::transaction(
            fn (): Product => $this->products->update($product, $request->validated()),
        );

        return ApiResponse::resource(
            ProductResource::make($product),
            'Product updated successfully.',
        );
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        DB::transaction(
            fn (): bool => $this->products->delete($product),
        );

        return ApiResponse::success(
            null,
            'Product deleted successfully.',
        );
    }
}
