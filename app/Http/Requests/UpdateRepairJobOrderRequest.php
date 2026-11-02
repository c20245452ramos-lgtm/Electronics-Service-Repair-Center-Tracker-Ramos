<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRepairJobOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $jobId = (int) $this->route('repair_job_order');

        return [
            'customer_id' => ['sometimes', 'exists:customers,id'],
            'device_id' => ['sometimes', 'exists:devices,id'],
            'technician_id' => ['sometimes', 'exists:technicians,id'],
            'job_order_number' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('repair_job_orders', 'job_order_number')->ignore($jobId),
            ],
            'reported_issue' => ['sometimes', 'string', 'max:2000'],
            'resolution_notes' => ['nullable', 'string', 'max:2000'],
            'job_status' => ['sometimes', 'string', 'in:Pending,In Progress,Completed,Awaiting Parts,On Hold'],
            'parts_cost' => ['sometimes', 'numeric', 'min:0'],
            'labor_cost' => ['sometimes', 'numeric', 'min:0'],
            'completion_date' => ['nullable', 'date'],
            'payments' => ['nullable', 'array'],
            'payments.*.amount' => ['nullable', 'numeric', 'min:0'],
            'payments.*.payment_method' => ['nullable', 'string', 'max:50'],
        ];
    }
}
