<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTechnicianRequest;
use App\Models\Technician;
use App\Services\RepairCenterService;
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
            'technicians' => Technician::withCount('repairJobOrders')->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('technicians/create');
    }

    public function store(StoreTechnicianRequest $request)
    {
        $this->repairCenterService->createTechnician($request->validated());

        return to_route('technicians.index')->with('success', 'Technician added successfully.');
    }
}
