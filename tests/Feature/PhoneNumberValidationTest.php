<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhoneNumberValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_and_technician_phone_numbers_accept_11_digits(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/customers', [
            'name' => 'Test Customer',
            'phone' => '09123456789',
        ])->assertRedirect('/customers');

        $this->post('/technicians', [
            'name' => 'Test Technician',
            'phone' => '09123456789',
        ])->assertRedirect('/technicians');

        $this->assertDatabaseHas('customers', ['phone' => '09123456789']);
        $this->assertDatabaseHas('technicians', ['phone' => '09123456789']);
    }

    public function test_customer_and_technician_phone_numbers_reject_non_digits_and_more_than_11_digits(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['09123abc789', '091234567890'] as $phone) {
            $this->post('/customers', [
                'name' => 'Test Customer',
                'phone' => $phone,
            ])->assertSessionHasErrors('phone');

            $this->post('/technicians', [
                'name' => 'Test Technician',
                'phone' => $phone,
            ])->assertSessionHasErrors('phone');
        }
    }
}
