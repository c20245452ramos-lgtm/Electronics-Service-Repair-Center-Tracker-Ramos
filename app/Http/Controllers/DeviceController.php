<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Models\Customer;
use App\Models\Device;
use App\Services\RepairCenterService;
use Illuminate\Http\RedirectResponse;
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
            'devices' => Device::query()
                ->with('customer:id,name')
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('devices/create', [
            'customers' => Customer::query()
                ->where('is_active', true)
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreDeviceRequest $request): RedirectResponse
    {
        $this->repairCenterService->createDevice($request->validated());

        return to_route('devices.index')->with('success', 'Device registered successfully.');
    }

    public function update(UpdateDeviceRequest $request, Device $device): RedirectResponse
    {
        $this->repairCenterService->updateDevice($device, $request->validated());

        return to_route('devices.index')->with('success', 'Device updated successfully.');
    }
}
