<?php

namespace App\Http\Requests;

use App\Models\Device;
use App\Models\Technician;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreRepairJobOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', Rule::exists('customers', 'id')->where('is_active', true)],
            'device_id' => [
                'required',
                Rule::exists('devices', 'id')->where(fn ($query) => $query->where('customer_id', $this->input('customer_id'))),
            ],
            'technician_id' => ['required', Rule::exists('technicians', 'id')->where('is_active', true)],
            'job_order_number' => ['required', 'string', 'max:20', 'unique:repair_job_orders,job_order_number'],
            'reported_issue' => ['required', 'string', 'max:2000'],
            'resolution_notes' => ['nullable', 'string', 'max:2000'],
            'job_status' => ['required', 'string', 'in:Pending,Diagnosing,In Progress,Awaiting Parts,On Hold'],
            'parts_cost' => ['required', 'numeric', 'min:0'],
            'labor_cost' => ['required', 'numeric', 'min:0'],
            'completion_date' => ['nullable', 'date'],
            'payments' => ['nullable', 'array'],
            'payments.*.amount' => ['nullable', 'numeric', 'min:0'],
            'payments.*.payment_method' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->has('device_id') || $validator->errors()->has('technician_id')) {
                return;
            }

            $device = Device::query()->find($this->input('device_id'));
            $technician = Technician::query()->find($this->input('technician_id'));

            if ($device && $technician && ! $technician->isSuitableForDeviceType($device->device_type)) {
                $validator->errors()->add('technician_id', 'Choose a technician who specializes in this device type.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'resolution_notes.required' => 'Resolution notes are required when the job is marked as Completed.',
            'device_id.exists' => 'The selected device does not belong to the selected customer.',
            'job_status.in' => 'The selected job status is invalid.',
            'job_status.required' => 'Choose an active status. A repair can only be completed after it has been created.',
            'job_order_number.max' => 'The job order number may not be longer than 20 characters.',
        ];
    }
}
