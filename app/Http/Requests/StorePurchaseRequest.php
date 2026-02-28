<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendor_id'          => ['required', 'integer', 'exists:vendors,id'],
            'invoice_number'     => ['required', 'string', 'unique:purchases,invoice_number'],
            'purchase_date'      => ['required', 'date'],
            'notes'              => ['nullable', 'string'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty'        => ['required', 'integer', 'min:1'],
            'items.*.price'      => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'             => 'At least one item is required.',
            'items.*.qty.min'            => 'Quantity must be greater than zero.',
            'items.*.price.min'          => 'Price must be greater than zero.',
            'items.*.product_id.exists'  => 'One or more products do not exist.',
            'vendor_id.exists'           => 'Vendor not found.',
            'invoice_number.unique'      => 'Invoice number already exists.',
        ];
    }
}
