<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = $this->route('product');

        if (!$product) {
            $productId = $this->route('id');

            $product = Product::find($productId);
        }

        if (!$product) {
            return false;
        }

        return $this->user()?->can('update', $product) ?? false;
    }

    public function rules(): array
    {
        $product = $this->route('product');

        $productId = $product instanceof Product
            ? $product->id
            : $this->route('id');

        return [
            'category_id' => [
                'sometimes',
                'integer',
                'exists:categories,id',
            ],

            'name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'sku' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($productId),
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'price' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'status' => [
                'sometimes',
                'string',
                'in:active,inactive',
            ],
        ];
    }
}