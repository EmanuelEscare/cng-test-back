<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends ApiFormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->normalizeProductInput();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $product = $this->route('product');
        $productId = $product instanceof Product ? $product->id : $product;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'sku' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'currency' => ['sometimes', 'required', 'string', 'size:3'],
        ];
    }

    /**
     * Normalize product fields before validation.
     */
    private function normalizeProductInput(): void
    {
        $data = [];

        if ($this->filled('sku')) {
            $data['sku'] = strtoupper(trim((string) $this->input('sku')));
        }

        if ($this->filled('currency')) {
            $data['currency'] = strtoupper(trim((string) $this->input('currency')));
        }

        if ($data !== []) {
            $this->merge($data);
        }
    }
}
