<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\RepairJobOrderController;
use App\Http\Controllers\TechnicianController;
use App\Models\Customer;
use App\Models\Device;
use App\Models\RepairJobOrder;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/dashboard')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        $month = now();
        $historyStart = $month->copy()->startOfMonth()->subMonths(11);
        $historyEnd = $month->copy()->endOfMonth();
        $dailyRevenue = RepairJobOrder::query()
            ->where('job_status', 'Completed')
            ->whereBetween('completion_date', [$historyStart->toDateString(), $historyEnd->toDateString()])
            ->select('completion_date')
            ->selectRaw('SUM(total_repair_cost) as revenue')
            ->groupBy('completion_date')
            ->get();
        $monthlyRevenueByMonth = $dailyRevenue
            ->groupBy(fn ($row) => substr((string) $row->completion_date, 0, 7))
            ->map(fn ($rows) => $rows->sum('revenue'));
        $revenueHistory = collect(range(0, 11))
            ->map(function (int $offset) use ($historyStart, $monthlyRevenueByMonth) {
                $date = $historyStart->copy()->addMonths($offset);

                return [
                    'month' => $date->format('M Y'),
                    'revenue' => (float) $monthlyRevenueByMonth->get($date->format('Y-m'), 0),
                ];
            })
            ->values();

        return Inertia::render('dashboard', [
            'metrics' => [
                'repairJobs' => RepairJobOrder::count(),
                'openRepairJobs' => RepairJobOrder::whereIn('job_status', ['Pending', 'Diagnosing', 'In Progress', 'Awaiting Parts', 'On Hold'])->count(),
                'completedRepairs' => RepairJobOrder::where('job_status', 'Completed')->count(),
                'awaitingParts' => RepairJobOrder::where('job_status', 'Awaiting Parts')->count(),
                'devices' => Device::count(),
                'customers' => Customer::count(),
                'monthlyRevenue' => RepairJobOrder::where('job_status', 'Completed')
                    ->whereBetween('completion_date', [$month->copy()->startOfMonth()->toDateString(), $month->copy()->endOfMonth()->toDateString()])
                    ->sum('total_repair_cost'),
                'revenueHistory' => $revenueHistory,
            ],
        ]);
    })->name('dashboard');

    Route::resource('devices', DeviceController::class)->only(['index', 'create', 'store', 'update']);
    Route::resource('repair-job-orders', RepairJobOrderController::class)->only(['index', 'create', 'store', 'update']);
    Route::patch('customers/{customer}/active', [CustomerController::class, 'toggleActive'])->name('customers.toggle-active');
    Route::resource('customers', CustomerController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::patch('technicians/{technician}/active', [TechnicianController::class, 'toggleActive'])->name('technicians.toggle-active');
    Route::resource('technicians', TechnicianController::class)->only(['index', 'create', 'store', 'edit', 'update']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
