<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRepairJobOrderRequest;
use App\Http\Requests\UpdateRepairJobOrderRequest;
use App\Models\Customer;
use App\Models\Device;
use App\Models\RepairJobOrder;
use App\Models\Technician;
use App\Services\RepairCenterService;
use Inertia\Inertia;
use Inertia\Response;

class RepairJobOrderController extends Controller
{
    public function __construct(protected RepairCenterService $repairCenterService)
    {
    }

    public function index(): Response
    {
        $showCompletedHistory = request()->query('view') === 'history';
        $jobOrders = RepairJobOrder::with(['customer', 'device', 'technician']);

        if ($showCompletedHistory) {
            $jobOrders->where('job_status', 'Completed')->orderByDesc('completion_date');
        }

        return Inertia::render('repair-job-orders/index', [
            'jobOrders' => $jobOrders->latest()->paginate(15)->withQueryString(),
            'showCompletedHistory' => $showCompletedHistory,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('repair-job-orders/create', [
            'customers' => Customer::select('id', 'name')->get(),
            'devices' => Device::select('id', 'brand', 'model', 'serial_number')->get(),
            'technicians' => Technician::select('id', 'name', 'specialty')->get(),
        ]);
    }

    public function store(StoreRepairJobOrderRequest $request)
    {
        $this->repairCenterService->createRepairJobOrder($request->validated());

        return to_route('repair-job-orders.index')->with('success', 'Repair job order created successfully.');
    }

    public function update(UpdateRepairJobOrderRequest $request, $id)
    {
        $this->repairCenterService->updateRepairJobOrder((int) $id, $request->validated());

        return to_route('repair-job-orders.index')->with('success', 'Repair job order updated successfully.');
    }
}
