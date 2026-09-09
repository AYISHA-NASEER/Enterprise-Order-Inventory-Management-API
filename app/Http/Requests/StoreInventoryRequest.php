<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
                'unique:inventories,product_id',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }
}