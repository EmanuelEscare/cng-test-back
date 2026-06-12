<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\ProductPromotion
 */
class ProductPromotionResource extends JsonResource
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
            'promotion' => $this->promotion,
            'discount_percentage' => $this->discount_percentage,
            'promotion_started_at' => $this->promotion_started_at?->toISOString(),
            'promotion_ends_at' => $this->promotion_ends_at?->toISOString(),
            'is_active' => $this->isActive(),
        ];
    }
}
