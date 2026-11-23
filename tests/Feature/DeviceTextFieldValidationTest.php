<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceTextFieldValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_device_text_fields_accept_20_characters(): void
    {
        $this->actingAs(User::factory()->create());
        $customer = Customer::create(['name' => 'Test Customer']);
        $deviceData = [
            'customer_id' => $customer->id,
            'brand' => 'Brand123456789012345',
            'model' => 'Model123456789012345',
            'device_type' => 'LaptopTypeabcdefghij',
            'serial_number' => 'Serial12345678901234',
        ];

        $this->post('/devices', $deviceData)->assertRedirect('/devices');

        $this->assertDatabaseHas('devices', [
            'brand' => $deviceData['brand'],
            'model' => $deviceData['model'],
            'device_type' => $deviceData['device_type'],
            'serial_number' => strtoupper($deviceData['serial_number']),
        ]);
    }

    public function test_device_text_fields_reject_more_than_20_characters_on_create_and_update(): void
    {
        $this->actingAs(User::factory()->create());
        $customer = Customer::create(['name' => 'Test Customer']);
        $device = Device::create([
            'customer_id' => $customer->id,
            'brand' => 'Existing',
            'model' => 'Device',
            'serial_number' => 'EXISTING-SERIAL',
        ]);

        foreach (['brand', 'model', 'device_type', 'serial_number'] as $field) {
            $validData = [
                'customer_id' => $customer->id,
                'brand' => 'Test',
                'model' => 'Device',
                'device_type' => 'Laptop',
                'serial_number' => "TEST-SERIAL-$field",
            ];
            $tooLong = str_repeat('x', 21);

            $this->post('/devices', [...$validData, $field => $tooLong])
                ->assertSessionHasErrors($field);

            $this->put("/devices/{$device->id}", [$field => $tooLong])
                ->assertSessionHasErrors($field);
        }

        $this->assertDatabaseHas('devices', [
            'id' => $device->id,
            'brand' => 'Existing',
        ]);
    }

    public function test_device_type_allows_letters_and_spaces_only(): void
    {
        $this->actingAs(User::factory()->create());
        $customer = Customer::create(['name' => 'Test Customer']);
        $device = Device::create([
            'customer_id' => $customer->id,
            'brand' => 'Test',
            'model' => 'Device',
            'serial_number' => 'TEST-DEVICE-TYPE',
        ]);
        $validData = [
            'customer_id' => $customer->id,
            'brand' => 'Test',
            'model' => 'Device',
            'device_type' => 'Smart Phone',
            'serial_number' => 'TEST-DEVICE-TYPE-2',
        ];

        $this->post('/devices', $validData)->assertRedirect('/devices');
        $this->assertDatabaseHas('devices', ['device_type' => 'Smart Phone']);

        $this->post('/devices', [...$validData, 'serial_number' => 'TEST-DEVICE-TYPE-3', 'device_type' => 'Phone 2'])
            ->assertSessionHasErrors('device_type');

        $this->put("/devices/{$device->id}", ['device_type' => 'Phone 2'])
            ->assertSessionHasErrors('device_type');
    }
}
