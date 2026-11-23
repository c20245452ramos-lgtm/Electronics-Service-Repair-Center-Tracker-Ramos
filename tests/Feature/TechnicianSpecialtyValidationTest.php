<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechnicianSpecialtyValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_technician_specialty_accepts_20_characters(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/technicians', [
            'name' => 'Test Technician',
            'specialty' => 'Mobile repair expert',
        ])->assertRedirect('/technicians');

        $this->assertDatabaseHas('technicians', [
            'specialty' => 'Mobile repair expert',
        ]);
    }

    public function test_technician_specialty_rejects_numbers_and_more_than_20_characters(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['Laptop repair 2', str_repeat('a', 21)] as $specialty) {
            $this->post('/technicians', [
                'name' => 'Test Technician',
                'specialty' => $specialty,
            ])->assertSessionHasErrors('specialty');
        }
    }
}
