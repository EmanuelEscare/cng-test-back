<?php

namespace App\Http\Requests;

class ListSuppliersRequest extends ApiFormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->normalizeBoolean('has_products');
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
            'has_products' => ['sometimes', 'boolean'],
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
