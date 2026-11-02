<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'device_type' => ['nullable', 'string', 'max:100'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:devices,serial_number'],
            'purchase_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.exists' => 'The selected customer is invalid.',
            'serial_number.unique' => 'This serial number is already registered.',
        ];
    }
}
