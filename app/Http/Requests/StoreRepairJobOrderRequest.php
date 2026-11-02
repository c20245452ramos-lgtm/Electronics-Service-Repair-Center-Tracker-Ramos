<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepairJobOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'device_id' => ['required', 'exists:devices,id'],
            'technician_id' => ['required', 'exists:technicians,id'],
            'job_order_number' => ['required', 'string', 'max:100', 'unique:repair_job_orders,job_order_number'],
            'reported_issue' => ['required', 'string', 'max:2000'],
            'resolution_notes' => ['nullable', 'string', 'max:2000'],
            'job_status' => ['required', 'string', 'in:Pending,In Progress,Completed,Awaiting Parts,On Hold'],
            'parts_cost' => ['required', 'numeric', 'min:0'],
            'labor_cost' => ['required', 'numeric', 'min:0'],
            'completion_date' => ['nullable', 'date'],
            'payments' => ['nullable', 'array'],
            'payments.*.amount' => ['nullable', 'numeric', 'min:0'],
            'payments.*.payment_method' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'resolution_notes.required' => 'Resolution notes are required when the job is marked as Completed.',
            'job_status.in' => 'The selected job status is invalid.',
        ];
    }
}
