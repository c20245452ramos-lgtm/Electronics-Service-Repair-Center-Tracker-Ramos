<?php

namespace App\Http\Requests;

use App\Models\Device;
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
        $device = $this->route('device');
        $deviceId = $device instanceof Device ? $device->getKey() : (int) $device;

        return [
            'customer_id' => ['sometimes', Rule::exists('customers', 'id')->where('is_active', true)],
            'brand' => ['sometimes', 'string', 'max:20'],
            'model' => ['sometimes', 'string', 'max:20'],
            'device_type' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z ]+$/'],
            'serial_number' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('devices', 'serial_number')->ignore($deviceId),
            ],
            'purchase_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'brand.max' => 'The brand may not be longer than 20 characters.',
            'model.max' => 'The model may not be longer than 20 characters.',
            'device_type.max' => 'The device type may not be longer than 20 characters.',
            'device_type.regex' => 'The device type may contain letters and spaces only.',
            'serial_number.max' => 'The serial number may not be longer than 20 characters.',
        ];
    }
}
