<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactNameValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_and_technician_names_accept_20_characters(): void
    {
        $this->actingAs(User::factory()->create());
        $name = 'abcdefghijklmnopqrst';

        $this->post('/customers', [
            'name' => $name,
        ])->assertRedirect('/customers');

        $this->post('/technicians', [
            'name' => $name,
        ])->assertRedirect('/technicians');

        $this->assertDatabaseHas('customers', ['name' => $name]);
        $this->assertDatabaseHas('technicians', ['name' => $name]);
    }

    public function test_customer_and_technician_names_reject_more_than_20_characters(): void
    {
        $this->actingAs(User::factory()->create());
        $name = 'abcdefghijklmnopqrstu';

        $this->post('/customers', [
            'name' => $name,
        ])->assertSessionHasErrors('name');

        $this->post('/technicians', [
            'name' => $name,
        ])->assertSessionHasErrors('name');
    }
}
