<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\Customer;
use App\Models\Device;
use App\Services\RepairCenterService;
use Inertia\Inertia;
use Inertia\Response;

class DeviceController extends Controller
{
    public function __construct(protected RepairCenterService $repairCenterService)
    {
    }

    public function index(): Response
    {
        return Inertia::render('devices/index', [
            'devices' => Device::with('customer')->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('devices/create', [
            'customers' => Customer::select('id', 'name')->get(),
        ]);
    }

    public function store(StoreDeviceRequest $request)
    {
        $this->repairCenterService->createDevice($request->validated());

        return to_route('devices.index')->with('success', 'Device registered successfully.');
    }

    public function update(UpdateDeviceRequest $request, $id)
    {
        $this->repairCenterService->updateDevice((int) $id, $request->validated());

        return to_route('devices.index')->with('success', 'Device updated successfully.');
    }
}
