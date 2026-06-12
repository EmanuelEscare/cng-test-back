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
            'discount_percentage' => $this->activeDiscountPercentage(),
            'discounted_price' => $this->discountedPrice(),
            'final_price' => $this->finalPrice(),
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

    /**
     * Get the active discount percentage.
     */
    private function activeDiscountPercentage(): ?string
    {
        if (! $this->resource->relationLoaded('activePromotion') || $this->activePromotion === null) {
            return null;
        }

        return $this->activePromotion->discount_percentage;
    }

    /**
     * Get the active discounted price.
     */
    private function discountedPrice(): ?string
    {
        $discountPercentage = (float) ($this->activeDiscountPercentage() ?? 0);

        if ($discountPercentage <= 0) {
            return null;
        }

        $discountedPrice = (float) $this->price * (1 - ($discountPercentage / 100));

        return number_format($discountedPrice, 2, '.', '');
    }

    /**
     * Get the final price after any active discount.
     */
    private function finalPrice(): string
    {
        return $this->discountedPrice() ?? $this->price;
    }
}
