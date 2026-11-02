<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RepairJobOrderController;
use App\Http\Controllers\TechnicianController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/dashboard')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::resource('devices', DeviceController::class)->only(['index', 'create', 'store', 'update']);
    Route::resource('repair-job-orders', RepairJobOrderController::class)->only(['index', 'create', 'store', 'update']);
    Route::resource('customers', CustomerController::class)->only(['index', 'create', 'store']);
    Route::resource('technicians', TechnicianController::class)->only(['index', 'create', 'store']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
