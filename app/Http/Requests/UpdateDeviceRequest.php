<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $deviceId = (int) $this->route('device');

        return [
            'customer_id' => ['sometimes', 'exists:customers,id'],
            'brand' => ['sometimes', 'string', 'max:255'],
            'model' => ['sometimes', 'string', 'max:255'],
            'device_type' => ['nullable', 'string', 'max:100'],
            'serial_number' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('devices', 'serial_number')->ignore($deviceId),
            ],
            'purchase_date' => ['nullable', 'date'],
        ];
    }
}
