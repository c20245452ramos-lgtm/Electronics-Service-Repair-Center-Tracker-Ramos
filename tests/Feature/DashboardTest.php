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

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_users_can_visit_the_dashboard()
    {
        $this->actingAs($user = User::factory()->create());

        $customer = Customer::create(['name' => 'Test Customer']);
        $device = Device::create([
            'customer_id' => $customer->id,
            'brand' => 'Test',
            'model' => 'Device',
            'serial_number' => 'TEST-DASHBOARD-001',
        ]);
        $technician = Technician::create(['name' => 'Test Technician']);
        RepairJobOrder::create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
            'job_order_number' => 'RO-TEST-DASHBOARD-001',
            'reported_issue' => 'Test issue',
            'job_status' => 'Pending',
        ]);

        $this->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard')
                ->where('metrics.repairJobs', 1)
                ->where('metrics.openRepairJobs', 1)
                ->where('metrics.completedRepairs', 0)
                ->where('metrics.awaitingParts', 0)
                ->where('metrics.devices', 1)
                ->where('metrics.customers', 1)
                ->where('metrics.monthlyRevenue', 0));
    }

    public function test_monthly_revenue_includes_completed_repair_totals(): void
    {
        $this->actingAs(User::factory()->create());

        $customer = Customer::create(['name' => 'Revenue Customer']);
        $device = Device::create([
            'customer_id' => $customer->id,
            'brand' => 'Test',
            'model' => 'Device',
            'serial_number' => 'TEST-REVENUE-001',
        ]);
        $technician = Technician::create(['name' => 'Revenue Technician']);

        $completedJob = RepairJobOrder::create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
            'job_order_number' => 'RO-TEST-REVENUE-001',
            'reported_issue' => 'Test issue',
            'job_status' => 'Pending',
            'parts_cost' => 200,
            'labor_cost' => 300,
            'total_repair_cost' => 500,
        ]);

        RepairJobOrder::create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
            'job_order_number' => 'RO-TEST-REVENUE-002',
            'reported_issue' => 'Pending test issue',
            'job_status' => 'Pending',
            'parts_cost' => 500,
            'labor_cost' => 500,
            'total_repair_cost' => 1000,
        ]);

        RepairJobOrder::create([
            'customer_id' => $customer->id,
            'device_id' => $device->id,
            'technician_id' => $technician->id,
            'job_order_number' => 'RO-TEST-REVENUE-003',
            'reported_issue' => 'Previous month repair',
            'job_status' => 'Completed',
            'total_repair_cost' => 700,
            'completion_date' => now()->subMonth()->startOfMonth()->toDateString(),
        ]);

        $this->patch("/repair-job-orders/{$completedJob->id}", [
            'job_status' => 'Completed',
            'resolution_notes' => 'Repair completed.',
        ])->assertRedirect('/repair-job-orders');

        $this->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard')
                ->where('metrics.monthlyRevenue', 500)
                ->where('metrics.revenueHistory.10.revenue', 700)
                ->where('metrics.revenueHistory.11.revenue', 500));
    }
}
