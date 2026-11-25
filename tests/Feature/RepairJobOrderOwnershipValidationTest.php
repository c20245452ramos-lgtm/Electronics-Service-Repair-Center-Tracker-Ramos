<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Device;
use App\Models\RepairJobOrder;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepairJobOrderOwnershipValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_repair_job_creation_rejects_a_device_owned_by_another_customer(): void
    {
        $this->actingAs(User::factory()->create());
        [$customer, $device, $technician] = $this->createCustomerDeviceAndTechnician();
        $otherCustomer = Customer::create(['name' => 'Other Customer']);
        $otherDevice = Device::create([
            'customer_id' => $otherCustomer->id,
            'brand' => 'Other',
            'model' => 'Device',
            'serial_number' => 'OTHER-DEVICE-001',
        ]);

        $this->post('/repair-job-orders', [
            'customer_id' => $customer->id,
            'device_id' => $otherDevice->id,
            'technician_id' => $technician->id,
            'job_order_number' => 'RO-OWNERSHIP-001',
            'reported_issue' => 'Test issue',
            'job_status' => 'Pending',
            'parts_cost' => 0,
            'labor_cost' => 0,
        ])->assertSessionHasErrors('device_id');

        $this->assertDatabaseMissing('repair_job_orders', ['job_order_number' => 'RO-OWNERSHIP-001']);
        $this->assertDatabaseHas('devices', ['id' => $device->id, 'customer_id' => $customer->id]);
    }

    public function test_repair_job_update_rejects_a_customer_that_does_not_own_its_device(): void
    {
        $this->actingAs(User::factory()->create());
        [$customer, $device, $technician] = $this->createCustomerDeviceAndTechnician();
        $jobOrder = RepairJobOrder::create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
            'job_order_number' => 'RO-OWNERSHIP-002',
            'reported_issue' => 'Test issue',
            'job_status' => 'Pending',
        ]);
        $otherCustomer = Customer::create(['name' => 'Other Customer']);

        $this->patch("/repair-job-orders/{$jobOrder->id}", [
            'customer_id' => $otherCustomer->id,
        ])->assertSessionHasErrors('device_id');

        $this->assertDatabaseHas('repair_job_orders', [
            'id' => $jobOrder->id,
            'customer_id' => $customer->id,
            'device_id' => $device->id,
        ]);
    }

    private function createCustomerDeviceAndTechnician(): array
    {
        $customer = Customer::create(['name' => 'Test Customer']);
        $device = Device::create([
            'customer_id' => $customer->id,
            'brand' => 'Test',
            'model' => 'Device',
            'serial_number' => 'TEST-OWNERSHIP-001',
        ]);
        $technician = Technician::create(['name' => 'Test Technician']);

        return [$customer, $device, $technician];
    }
}
