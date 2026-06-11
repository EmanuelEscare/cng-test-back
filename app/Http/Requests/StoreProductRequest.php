<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreProductRequest extends ApiFormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', Rule::unique('products', 'sku')],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'currency' => ['sometimes', 'string', 'size:3'],
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
