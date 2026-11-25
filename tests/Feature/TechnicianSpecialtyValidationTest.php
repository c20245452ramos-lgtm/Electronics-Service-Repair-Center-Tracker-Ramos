<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechnicianSpecialtyValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_technician_specialty_accepts_100_characters(): void
    {
        $this->actingAs(User::factory()->create());
        $specialty = str_repeat('a', 100);

        $this->post('/technicians', [
            'name' => 'Test Technician',
            'specialty' => $specialty,
        ])->assertRedirect('/technicians');

        $this->assertDatabaseHas('technicians', [
            'specialty' => $specialty,
        ]);
    }

    public function test_technician_specialty_rejects_numbers_and_more_than_100_characters(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['Laptop repair 2', str_repeat('a', 101)] as $specialty) {
            $this->post('/technicians', [
                'name' => 'Test Technician',
                'specialty' => $specialty,
            ])->assertSessionHasErrors('specialty');
        }
    }
}
