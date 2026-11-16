<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Device;
use App\Models\RepairJobOrder;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepairJobOrderStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_update_a_repair_job_status(): void
    {
        $this->actingAs(User::factory()->create());

        $customer = Customer::create(['name' => 'Test Customer']);
        $device = Device::create([
            'customer_id' => $customer->id,
            'brand' => 'Test',
            'model' => 'Device',
            'serial_number' => 'TEST-STATUS-001',
        ]);
        $technician = Technician::create(['name' => 'Test Technician']);
        $jobOrder = RepairJobOrder::create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
            'job_order_number' => 'RO-TEST-STATUS-001',
            'reported_issue' => 'Test issue',
            'job_status' => 'Pending',
        ]);

        $this->patch("/repair-job-orders/{$jobOrder->id}", [
            'job_status' => 'Diagnosing',
        ])->assertRedirect('/repair-job-orders');

        $this->assertDatabaseHas('repair_job_orders', [
            'id' => $jobOrder->id,
            'job_status' => 'Diagnosing',
        ]);

        $this->patch("/repair-job-orders/{$jobOrder->id}", [
            'job_status' => 'In Progress',
        ])->assertRedirect('/repair-job-orders');

        $this->assertDatabaseHas('repair_job_orders', [
            'id' => $jobOrder->id,
            'job_status' => 'In Progress',
        ]);
    }
}
