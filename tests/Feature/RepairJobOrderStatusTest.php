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

    public function test_completed_repair_jobs_cannot_be_updated(): void
    {
        $this->actingAs(User::factory()->create());
        $jobOrder = $this->createJobOrder('Completed');

        $this->patch("/repair-job-orders/{$jobOrder->id}", [
            'job_status' => 'In Progress',
        ])->assertSessionHasErrors('job_status');

        $this->assertDatabaseHas('repair_job_orders', [
            'id' => $jobOrder->id,
            'job_status' => 'Completed',
        ]);
    }

    public function test_completed_repairs_appear_in_history_with_a_completion_date(): void
    {
        $this->actingAs(User::factory()->create());
        $completedJob = $this->createJobOrder();
        $openJob = $this->createJobOrder();

        $this->patch("/repair-job-orders/{$completedJob->id}", [
            'job_status' => 'Completed',
            'resolution_notes' => 'Replaced the damaged screen.',
        ])->assertRedirect('/repair-job-orders');

        $this->assertSame(now()->toDateString(), $completedJob->fresh()->completion_date->toDateString());
        $this->assertDatabaseHas('repair_job_orders', [
            'id' => $completedJob->id,
            'job_status' => 'Completed',
        ]);

        $this->get('/repair-job-orders?view=history')->assertInertia(fn (Assert $page) => $page
            ->component('repair-job-orders/index')
            ->where('showCompletedHistory', true)
            ->has('jobOrders.data', 1)
            ->where('jobOrders.data.0.id', $completedJob->id)
            ->where('jobOrders.data.0.job_status', 'Completed')
        );

        $this->assertDatabaseHas('repair_job_orders', [
            'id' => $openJob->id,
            'job_status' => 'Pending',
        ]);
    }

    public function test_repair_job_cannot_be_created_as_completed(): void
    {
        $this->actingAs(User::factory()->create());
        $customer = Customer::create(['name' => 'Test Customer']);
        $device = Device::create([
            'customer_id' => $customer->id,
            'brand' => 'Test',
            'model' => 'Device',
            'serial_number' => 'TEST-CREATE-COMPLETED-001',
        ]);
        $technician = Technician::create(['name' => 'Test Technician']);

        $this->post('/repair-job-orders', [
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
            'job_order_number' => 'RO-CREATE-DONE-001',
            'reported_issue' => 'Test issue',
            'job_status' => 'Completed',
            'resolution_notes' => 'Repair completed.',
            'parts_cost' => 0,
            'labor_cost' => 0,
        ])->assertSessionHasErrors('job_status');

        $this->assertDatabaseMissing('repair_job_orders', [
            'job_order_number' => 'RO-CREATE-DONE-001',
        ]);
    }

    public function test_active_jobs_are_ordered_by_priority_before_completed_jobs(): void
    {
        $this->actingAs(User::factory()->create());
        $newerPendingJob = $this->createJobOrder('Pending');
        $olderPendingJob = $this->createJobOrder('Pending');
        $newerPendingJob->forceFill(['created_at' => now()->subDay()])->save();
        $olderPendingJob->forceFill(['created_at' => now()->subDays(2)])->save();
        $completedJob = $this->createJobOrder('Completed');
        $onHoldJob = $this->createJobOrder('On Hold');
        $awaitingPartsJob = $this->createJobOrder('Awaiting Parts');
        $diagnosingJob = $this->createJobOrder('Diagnosing');
        $inProgressJob = $this->createJobOrder('In Progress');

        $this->get('/repair-job-orders')->assertInertia(fn (Assert $page) => $page
            ->component('repair-job-orders/index')
            ->where('jobOrders.data.0.id', $inProgressJob->id)
            ->where('jobOrders.data.1.id', $diagnosingJob->id)
            ->where('jobOrders.data.2.id', $olderPendingJob->id)
            ->where('jobOrders.data.3.id', $newerPendingJob->id)
            ->where('jobOrders.data.4.id', $awaitingPartsJob->id)
            ->where('jobOrders.data.5.id', $onHoldJob->id)
            ->where('jobOrders.data.6.id', $completedJob->id)
        );
    }

    private function createJobOrder(string $status = 'Pending'): RepairJobOrder
    {
        $customer = Customer::create(['name' => 'Test Customer']);
        $device = Device::create([
            'customer_id' => $customer->id,
            'brand' => 'Test',
            'model' => 'Device',
            'serial_number' => 'TEST-STATUS-'.$customer->id,
        ]);
        $technician = Technician::create(['name' => 'Test Technician']);

        return RepairJobOrder::create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
            'job_order_number' => 'RO-TEST-STATUS-'.$customer->id,
            'reported_issue' => 'Test issue',
            'resolution_notes' => $status === 'Completed' ? 'Repair completed.' : null,
            'job_status' => $status,
        ]);
    }
}
