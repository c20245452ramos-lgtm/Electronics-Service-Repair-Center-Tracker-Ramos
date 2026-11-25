<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Services\RepairCenterService;
use Illuminate\Http\RedirectResponse;
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
            'customers' => Customer::query()
                ->withCount(['devices', 'repairJobOrders'])
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('customers/create');
    }

    public function edit(Customer $customer): Response
    {
        return Inertia::render('customers/create', [
            'customer' => $customer,
        ]);
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $this->repairCenterService->createCustomer($request->validated());

        return to_route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function update(StoreCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->repairCenterService->updateCustomer($customer, $request->validated());

        return to_route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function toggleActive(Customer $customer): RedirectResponse
    {
        $customer->update(['is_active' => ! $customer->is_active]);

        $message = $customer->is_active ? 'Customer activated successfully.' : 'Customer deactivated successfully.';

        return to_route('customers.index')->with('success', $message);
    }
}
