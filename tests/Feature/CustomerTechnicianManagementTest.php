<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Device;
use App\Models\RepairJobOrder;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CustomerTechnicianManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_be_edited_and_deactivated_without_removing_repair_history(): void
    {
        $this->actingAs(User::factory()->create());
        [$customer, $device, $technician, $jobOrder] = $this->createRepairReferences();

        $this->get("/customers/{$customer->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('customers/create')
                ->where('customer.id', $customer->id)
                ->where('customer.name', 'Test Customer'));

        $this->put("/customers/{$customer->id}", [
            'name' => 'Updated Customer',
            'phone' => '09123456789',
            'email' => 'customer@example.com',
            'address' => 'Updated address',
        ])->assertRedirect('/customers');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated Customer',
        ]);

        $this->patch("/customers/{$customer->id}/active")->assertRedirect('/customers');

        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'is_active' => 0]);
        $this->assertDatabaseHas('repair_job_orders', [
            'id' => $jobOrder->id,
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
        ]);

        $this->get('/devices/create')->assertInertia(fn (Assert $page) => $page
            ->component('devices/create')
            ->has('customers', 0));
    }

    public function test_technician_can_be_edited_and_deactivated_without_removing_repair_history(): void
    {
        $this->actingAs(User::factory()->create());
        [$customer, $device, $technician, $jobOrder] = $this->createRepairReferences();

        $this->get("/technicians/{$technician->id}/edit")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('technicians/create')
                ->where('technician.id', $technician->id)
                ->where('technician.name', 'Test Technician'));

        $this->put("/technicians/{$technician->id}", [
            'name' => 'Updated Technician',
            'specialty' => 'Laptop repair',
            'phone' => '09123456789',
            'email' => 'technician@example.com',
        ])->assertRedirect('/technicians');

        $this->assertDatabaseHas('technicians', [
            'id' => $technician->id,
            'name' => 'Updated Technician',
        ]);

        $this->patch("/technicians/{$technician->id}/active")->assertRedirect('/technicians');

        $this->assertDatabaseHas('technicians', ['id' => $technician->id, 'is_active' => 0]);
        $this->assertDatabaseHas('repair_job_orders', [
            'id' => $jobOrder->id,
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
        ]);

        $this->get('/repair-job-orders/create')->assertInertia(fn (Assert $page) => $page
            ->component('repair-job-orders/create')
            ->has('technicians', 0));
    }

    private function createRepairReferences(): array
    {
        $customer = Customer::create(['name' => 'Test Customer']);
        $device = Device::create([
            'customer_id' => $customer->id,
            'brand' => 'Test',
            'model' => 'Device',
            'serial_number' => 'TEST-MANAGEMENT-001',
        ]);
        $technician = Technician::create(['name' => 'Test Technician', 'specialty' => 'General repair']);
        $jobOrder = RepairJobOrder::create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
            'job_order_number' => 'RO-TEST-MANAGEMENT-001',
            'reported_issue' => 'Test issue',
            'job_status' => 'Pending',
        ]);

        return [$customer, $device, $technician, $jobOrder];
    }
}
