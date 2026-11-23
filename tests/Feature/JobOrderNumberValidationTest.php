<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Device;
use App\Models\RepairJobOrder;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobOrderNumberValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_order_number_accepts_20_characters(): void
    {
        $this->actingAs(User::factory()->create());
        $jobOrderNumber = 'RO-12345678901234567';
        [$customer, $device, $technician] = $this->createJobReferences();

        $this->post('/repair-job-orders', [
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
            'job_order_number' => $jobOrderNumber,
            'reported_issue' => 'Test issue',
            'job_status' => 'Pending',
            'parts_cost' => 0,
            'labor_cost' => 0,
        ])->assertRedirect('/repair-job-orders');

        $this->assertDatabaseHas('repair_job_orders', ['job_order_number' => $jobOrderNumber]);
    }

    public function test_job_order_number_rejects_more_than_20_characters_on_create_and_update(): void
    {
        $this->actingAs(User::factory()->create());
        [$customer, $device, $technician] = $this->createJobReferences();
        $jobOrder = RepairJobOrder::create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
            'job_order_number' => 'RO-EXISTING-001',
            'reported_issue' => 'Test issue',
            'job_status' => 'Pending',
        ]);
        $tooLong = 'RO-123456789012345678';

        $this->post('/repair-job-orders', [
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
            'job_order_number' => $tooLong,
            'reported_issue' => 'Test issue',
            'job_status' => 'Pending',
            'parts_cost' => 0,
            'labor_cost' => 0,
        ])->assertSessionHasErrors('job_order_number');

        $this->patch("/repair-job-orders/{$jobOrder->id}", [
            'job_order_number' => $tooLong,
        ])->assertSessionHasErrors('job_order_number');

        $this->assertDatabaseHas('repair_job_orders', [
            'id' => $jobOrder->id,
            'job_order_number' => 'RO-EXISTING-001',
        ]);
    }

    private function createJobReferences(): array
    {
        $customer = Customer::create(['name' => 'Test Customer']);
        $device = Device::create([
            'customer_id' => $customer->id,
            'brand' => 'Test',
            'model' => 'Device',
            'serial_number' => 'TEST-JOB-NUMBER-'.$customer->id,
        ]);
        $technician = Technician::create(['name' => 'Test Technician']);

        return [$customer, $device, $technician];
    }
}
