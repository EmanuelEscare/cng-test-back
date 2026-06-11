<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Supplier
 */
class SupplierResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'webs' => $this->webs,
            'address_line' => $this->address_line,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'products_count' => $this->whenCounted('products'),
            'active_products_count' => $this->whenCounted('active_products'),
            'is_active_for_product' => $this->whenPivotLoaded(
                'product_supplier',
                fn (): bool => (bool) $this->pivot->is_active,
            ),
        ];
    }
}
