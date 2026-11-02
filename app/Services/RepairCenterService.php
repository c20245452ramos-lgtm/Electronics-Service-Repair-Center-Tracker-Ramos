<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Customer;
use App\Models\RepairJobOrder;
use App\Models\Technician;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RepairCenterService
{
    public function createCustomer(array $data): Customer
    {
        return Customer::create([
            ...$data,
            'name' => $this->sanitizeText($data['name']),
            'phone' => $this->normalizePhone($data['phone'] ?? null),
            'email' => $this->sanitizeText($data['email'] ?? null),
            'address' => $this->sanitizeText($data['address'] ?? null),
        ]);
    }

    public function createTechnician(array $data): Technician
    {
        return Technician::create([
            ...$data,
            'name' => $this->sanitizeText($data['name']),
            'specialty' => $this->sanitizeText($data['specialty'] ?? null),
            'phone' => $this->normalizePhone($data['phone'] ?? null),
            'email' => $this->sanitizeText($data['email'] ?? null),
        ]);
    }

    public function createDevice(array $data): Device
    {
        return Device::create($this->prepareDeviceData($data));
    }

    public function updateDevice(int $id, array $data): Device
    {
        $device = Device::findOrFail($id);
        $device->fill($this->prepareDeviceData($data));
        $device->save();

        return $device->fresh();
    }

    public function createRepairJobOrder(array $data): RepairJobOrder
    {
        return DB::transaction(function () use ($data) {
            $jobOrder = RepairJobOrder::create($this->prepareRepairJobOrderData($data));

            if (isset($data['payments'])) {
                foreach ($data['payments'] as $payment) {
                    $jobOrder->payments()->create([
                        'amount' => round((float) ($payment['amount'] ?? 0), 2),
                        'payment_method' => $this->sanitizeText($payment['payment_method'] ?? 'cash'),
                        'paid_at' => $payment['paid_at'] ?? now(),
                    ]);
                }
            }

            return $jobOrder->fresh(['customer', 'device', 'technician', 'payments']);
        });
    }

    public function updateRepairJobOrder(int $id, array $data): RepairJobOrder
    {
        return DB::transaction(function () use ($id, $data) {
            $jobOrder = RepairJobOrder::findOrFail($id);
            $jobOrder->fill($this->prepareRepairJobOrderData($data, $jobOrder));
            $jobOrder->save();

            if (isset($data['payments']) && is_array($data['payments'])) {
                $jobOrder->payments()->delete();

                foreach ($data['payments'] as $payment) {
                    $jobOrder->payments()->create([
                        'amount' => round((float) ($payment['amount'] ?? 0), 2),
                        'payment_method' => $this->sanitizeText($payment['payment_method'] ?? 'cash'),
                        'paid_at' => $payment['paid_at'] ?? now(),
                    ]);
                }
            }

            return $jobOrder->fresh(['customer', 'device', 'technician', 'payments']);
        });
    }

    private function prepareDeviceData(array $data): array
    {
        foreach (['brand', 'model', 'device_type'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->sanitizeText($data[$field]);
            }
        }

        if (isset($data['serial_number'])) {
            $data['serial_number'] = strtoupper($this->sanitizeText($data['serial_number']) ?? '');
        }

        return $data;
    }

    private function prepareRepairJobOrderData(array $data, ?RepairJobOrder $jobOrder = null): array
    {
        foreach (['reported_issue', 'resolution_notes'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->sanitizeText($data[$field]);
            }
        }

        $partsCost = (float) ($data['parts_cost'] ?? $jobOrder?->parts_cost ?? 0);
        $laborCost = (float) ($data['labor_cost'] ?? $jobOrder?->labor_cost ?? 0);
        $data['parts_cost'] = round($partsCost, 2);
        $data['labor_cost'] = round($laborCost, 2);
        $data['total_repair_cost'] = $this->calculateTotalRepairCost($partsCost, $laborCost);

        $status = array_key_exists('job_status', $data) ? $data['job_status'] : $jobOrder?->job_status;
        $resolutionNotes = array_key_exists('resolution_notes', $data)
            ? $data['resolution_notes']
            : $jobOrder?->resolution_notes;

        if (strtoupper((string) $status) === 'COMPLETED' && blank($resolutionNotes)) {
            throw ValidationException::withMessages([
                'resolution_notes' => ['Resolution notes are required when the status is Completed.'],
            ]);
        }

        return $data;
    }

    private function sanitizeText(?string $value): ?string
    {
        return $value === null ? null : trim(strip_tags($value));
    }

    private function normalizePhone(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value);

        return $digits === '' ? '' : $digits;
    }

    private function calculateTotalRepairCost(float $partsCost, float $laborCost): float
    {
        return round($partsCost + $laborCost, 2);
    }
}