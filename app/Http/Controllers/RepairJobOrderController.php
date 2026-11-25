<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRepairJobOrderRequest;
use App\Http\Requests\UpdateRepairJobOrderRequest;
use App\Models\Customer;
use App\Models\Device;
use App\Models\RepairJobOrder;
use App\Models\Technician;
use App\Services\RepairCenterService;
use Illuminate\Http\RedirectResponse;
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
        $jobOrders = RepairJobOrder::query()
            ->with([
                'customer:id,name',
                'device:id,brand,model,serial_number',
                'technician:id,name',
            ]);

        if ($showCompletedHistory) {
            $jobOrders
                ->where('job_status', 'Completed')
                ->orderByDesc('completion_date');
        } else {
            $jobOrders
                ->orderByRaw("
                    CASE job_status
                        WHEN 'In Progress' THEN 0
                        WHEN 'Diagnosing' THEN 1
                        WHEN 'Pending' THEN 2
                        WHEN 'Awaiting Parts' THEN 3
                        WHEN 'On Hold' THEN 4
                        WHEN 'Completed' THEN 5
                        ELSE 6
                    END
                ")
                ->oldest()
                ->orderBy('id');
        }

        return Inertia::render('repair-job-orders/index', [
            'jobOrders' => $jobOrders->paginate(15)->withQueryString(),
            'showCompletedHistory' => $showCompletedHistory,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('repair-job-orders/create', [
            'customers' => Customer::where('is_active', true)->select('id', 'name')->orderBy('name')->get(),
            'devices' => Device::select('id', 'customer_id', 'brand', 'model', 'device_type', 'serial_number')->orderBy('brand')->get(),
            'technicians' => Technician::where('is_active', true)->select('id', 'name', 'specialty')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreRepairJobOrderRequest $request): RedirectResponse
    {
        $this->repairCenterService->createRepairJobOrder($request->validated());

        return to_route('repair-job-orders.index')->with('success', 'Repair job order created successfully.');
    }

    public function update(UpdateRepairJobOrderRequest $request, RepairJobOrder $repairJobOrder): RedirectResponse
    {
        $this->repairCenterService->updateRepairJobOrder($repairJobOrder, $request->validated());

        return to_route('repair-job-orders.index')->with('success', 'Repair job order updated successfully.');
    }
}
