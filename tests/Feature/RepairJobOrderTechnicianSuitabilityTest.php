<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Device;
use App\Models\RepairJobOrder;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepairJobOrderTechnicianSuitabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_creation_rejects_a_technician_without_a_matching_device_specialty(): void
    {
        $this->actingAs(User::factory()->create());
        [$customer, $device] = $this->createCustomerAndLaptop();
        $technician = Technician::create(['name' => 'Phone Technician', 'specialty' => 'Cellphone Repair']);

        $this->post('/repair-job-orders', $this->jobOrderData($customer, $device, $technician))
            ->assertSessionHasErrors('technician_id');

        $this->assertDatabaseMissing('repair_job_orders', ['job_order_number' => 'RO-SPECIALTY-001']);
    }

    public function test_creation_accepts_a_technician_with_a_matching_device_specialty(): void
    {
        $this->actingAs(User::factory()->create());
        [$customer, $device] = $this->createCustomerAndLaptop();
        $technician = Technician::create(['name' => 'Laptop Technician', 'specialty' => 'Laptop Repair']);

        $this->post('/repair-job-orders', $this->jobOrderData($customer, $device, $technician))
            ->assertRedirect('/repair-job-orders');

        $this->assertDatabaseHas('repair_job_orders', [
            'job_order_number' => 'RO-SPECIALTY-001',
            'technician_id' => $technician->id,
        ]);
    }

    public function test_update_rejects_a_technician_who_does_not_match_the_existing_device(): void
    {
        $this->actingAs(User::factory()->create());
        [$customer, $device] = $this->createCustomerAndLaptop();
        $laptopTechnician = Technician::create(['name' => 'Laptop Technician', 'specialty' => 'Laptop Repair']);
        $phoneTechnician = Technician::create(['name' => 'Phone Technician', 'specialty' => 'Cellphone Repair']);
        $jobOrder = RepairJobOrder::create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $laptopTechnician->id,
            'job_order_number' => 'RO-SPECIALTY-002',
            'reported_issue' => 'Laptop will not start',
            'job_status' => 'Pending',
        ]);

        $this->patch("/repair-job-orders/{$jobOrder->id}", [
            'technician_id' => $phoneTechnician->id,
        ])->assertSessionHasErrors('technician_id');

        $this->assertDatabaseHas('repair_job_orders', [
            'id' => $jobOrder->id,
            'technician_id' => $laptopTechnician->id,
        ]);
    }

    private function createCustomerAndLaptop(): array
    {
        $customer = Customer::create(['name' => 'Laptop Customer']);
        $device = Device::create([
            'customer_id' => $customer->id,
            'brand' => 'Test',
            'model' => 'Laptop',
            'device_type' => 'Laptop',
            'serial_number' => 'LAPTOP-'.uniqid(),
        ]);

        return [$customer, $device];
    }

    private function jobOrderData(Customer $customer, Device $device, Technician $technician): array
    {
        return [
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
            'job_order_number' => 'RO-SPECIALTY-001',
            'reported_issue' => 'Laptop will not start',
            'job_status' => 'Pending',
            'parts_cost' => 0,
            'labor_cost' => 0,
        ];
    }
}
