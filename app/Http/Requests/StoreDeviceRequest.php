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
            'brand' => ['required', 'string', 'max:20'],
            'model' => ['required', 'string', 'max:20'],
            'device_type' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z ]+$/'],
            'serial_number' => ['required', 'string', 'max:20', 'unique:devices,serial_number'],
            'purchase_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.exists' => 'The selected customer is invalid.',
            'serial_number.unique' => 'This serial number is already registered.',
            'brand.max' => 'The brand may not be longer than 20 characters.',
            'model.max' => 'The model may not be longer than 20 characters.',
            'device_type.max' => 'The device type may not be longer than 20 characters.',
            'device_type.regex' => 'The device type may contain letters and spaces only.',
            'serial_number.max' => 'The serial number may not be longer than 20 characters.',
        ];
    }
}
