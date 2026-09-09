<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_number' => [
                'required',
                'string',
                'max:255',
                'unique:orders,order_number',
            ],
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'total_amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'status' => [
                'required',
                'string',
                'in:pending,confirmed,cancelled',

            ]

        ];
    }
}
