<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseSummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
            'vendor_id'  => ['nullable', 'integer', 'exists:vendors,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required'        => 'Start date is required.',
            'end_date.required'          => 'End date is required.',
            'end_date.after_or_equal'    => 'End date must be after or equal to start date.',
            'vendor_id.exists'           => 'Vendor not found.',
        ];
    }
}
