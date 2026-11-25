<?php

namespace App\Http\Requests;

use App\Models\Device;
use App\Models\RepairJobOrder;
use App\Models\Technician;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateRepairJobOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $jobOrder = $this->route('repair_job_order');
        $jobId = $jobOrder instanceof RepairJobOrder ? $jobOrder->getKey() : (int) $jobOrder;

        return [
            'customer_id' => ['sometimes', Rule::exists('customers', 'id')->where('is_active', true)],
            'device_id' => ['sometimes', 'exists:devices,id'],
            'technician_id' => ['sometimes', Rule::exists('technicians', 'id')->where('is_active', true)],
            'job_order_number' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('repair_job_orders', 'job_order_number')->ignore($jobId),
            ],
            'reported_issue' => ['sometimes', 'string', 'max:2000'],
            'resolution_notes' => ['nullable', 'string', 'max:2000'],
            'job_status' => ['sometimes', 'string', 'in:Pending,Diagnosing,In Progress,Completed,Awaiting Parts,On Hold'],
            'parts_cost' => ['sometimes', 'numeric', 'min:0'],
            'labor_cost' => ['sometimes', 'numeric', 'min:0'],
            'completion_date' => ['nullable', 'date'],
            'payments' => ['nullable', 'array'],
            'payments.*.amount' => ['nullable', 'numeric', 'min:0'],
            'payments.*.payment_method' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $jobOrder = $this->route('repair_job_order');

            if (! $jobOrder instanceof RepairJobOrder) {
                return;
            }

            $ownershipChanged = $this->exists('customer_id') || $this->exists('device_id');
            $assignmentChanged = $this->exists('device_id') || $this->exists('technician_id');

            if (
                (! $ownershipChanged && ! $assignmentChanged)
                || $validator->errors()->has('customer_id')
                || $validator->errors()->has('device_id')
                || $validator->errors()->has('technician_id')
            ) {
                return;
            }

            $customerId = $this->input('customer_id', $jobOrder->customer_id);
            $deviceId = $this->input('device_id', $jobOrder->device_id);
            $device = Device::query()->find($deviceId);

            if (! $device) {
                return;
            }

            if ($ownershipChanged && (string) $device->customer_id !== (string) $customerId) {
                $validator->errors()->add('device_id', 'The selected device does not belong to the selected customer.');

                return;
            }

            if ($assignmentChanged) {
                $technician = Technician::query()->find($this->input('technician_id', $jobOrder->technician_id));

                if ($technician && ! $technician->isSuitableForDeviceType($device->device_type)) {
                    $validator->errors()->add('technician_id', 'Choose a technician who specializes in this device type.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'job_order_number.max' => 'The job order number may not be longer than 20 characters.',
        ];
    }
}
