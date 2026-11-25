<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTechnicianRequest;
use App\Models\Technician;
use App\Services\RepairCenterService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TechnicianController extends Controller
{
    public function __construct(protected RepairCenterService $repairCenterService)
    {
    }

    public function index(): Response
    {
        return Inertia::render('technicians/index', [
            'technicians' => Technician::query()
                ->withCount('repairJobOrders')
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('technicians/create');
    }

    public function edit(Technician $technician): Response
    {
        return Inertia::render('technicians/create', [
            'technician' => $technician,
        ]);
    }

    public function store(StoreTechnicianRequest $request): RedirectResponse
    {
        $this->repairCenterService->createTechnician($request->validated());

        return to_route('technicians.index')->with('success', 'Technician added successfully.');
    }

    public function update(StoreTechnicianRequest $request, Technician $technician): RedirectResponse
    {
        $this->repairCenterService->updateTechnician($technician, $request->validated());

        return to_route('technicians.index')->with('success', 'Technician updated successfully.');
    }

    public function toggleActive(Technician $technician): RedirectResponse
    {
        $technician->update(['is_active' => ! $technician->is_active]);

        $message = $technician->is_active ? 'Technician activated successfully.' : 'Technician deactivated successfully.';

        return to_route('technicians.index')->with('success', $message);
    }
}
