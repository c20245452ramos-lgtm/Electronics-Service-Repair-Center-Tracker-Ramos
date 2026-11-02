<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Services\RepairCenterService;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function __construct(protected RepairCenterService $repairCenterService)
    {
    }

    public function index(): Response
    {
        return Inertia::render('customers/index', [
            'customers' => Customer::withCount(['devices', 'repairJobOrders'])->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('customers/create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $this->repairCenterService->createCustomer($request->validated());

        return to_route('customers.index')->with('success', 'Customer created successfully.');
    }
}
