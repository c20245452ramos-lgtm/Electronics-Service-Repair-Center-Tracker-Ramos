<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactEmailValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_and_technician_emails_accept_30_characters(): void
    {
        $this->actingAs(User::factory()->create());
        $email = 'abcdefghijklmnopqrstuvwxy@x.co';

        $this->post('/customers', [
            'name' => 'Test Customer',
            'email' => $email,
        ])->assertRedirect('/customers');

        $this->post('/technicians', [
            'name' => 'Test Technician',
            'email' => $email,
        ])->assertRedirect('/technicians');

        $this->assertDatabaseHas('customers', ['email' => $email]);
        $this->assertDatabaseHas('technicians', ['email' => $email]);
    }

    public function test_customer_and_technician_emails_reject_more_than_30_characters(): void
    {
        $this->actingAs(User::factory()->create());
        $email = 'abcdefghijklmnopqrstuvwxyz@x.co';

        $this->post('/customers', [
            'name' => 'Test Customer',
            'email' => $email,
        ])->assertSessionHasErrors('email');

        $this->post('/technicians', [
            'name' => 'Test Technician',
            'email' => $email,
        ])->assertSessionHasErrors('email');
    }
}
