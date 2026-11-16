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
                ->where('metrics.devices', 1)
                ->where('metrics.customers', 1));
    }
}
