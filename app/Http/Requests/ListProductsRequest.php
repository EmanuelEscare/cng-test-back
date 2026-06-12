<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ListProductsRequest extends ApiFormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('direction')) {
            $this->merge([
                'direction' => strtolower((string) $this->input('direction')),
            ]);
        }

        $this->normalizeBoolean('is_active');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'supplier_id' => ['sometimes', 'integer', 'exists:suppliers,id'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', Rule::in(['name', 'sku', 'price', 'created_at'])],
            'direction' => ['sometimes', Rule::in(['asc', 'desc'])],
        ];
    }

    /**
     * Normalize boolean query params before validation.
     */
    private function normalizeBoolean(string $key): void
    {
        if (! $this->has($key)) {
            return;
        }

        $value = filter_var($this->input($key), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($value !== null) {
            $this->merge([$key => $value]);
        }
    }
}
