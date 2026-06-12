<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Product
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'has_active_promotion' => $this->hasActivePromotion(),
            'active_promotion' => $this->whenLoaded(
                'activePromotion',
                fn (): ?ProductPromotionResource => $this->activePromotion === null
                    ? null
                    : ProductPromotionResource::make($this->activePromotion),
            ),
            'promotions' => ProductPromotionResource::collection(
                $this->whenLoaded('promotions'),
            ),
            'suppliers' => SupplierResource::collection(
                $this->whenLoaded('suppliers'),
            ),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Determine if the resource has an active promotion.
     */
    private function hasActivePromotion(): bool
    {
        if (isset($this->resource->has_active_promotion)) {
            return (bool) $this->resource->has_active_promotion;
        }

        return $this->resource->relationLoaded('activePromotion')
            && $this->activePromotion !== null;
    }
}
