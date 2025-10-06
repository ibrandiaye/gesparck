<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FuelEntryController;
use App\Http\Controllers\RepairLogController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('vehicles', VehicleController::class);
    Route::resource('fuel-entries', FuelEntryController::class);
    Route::resource('repair-logs', RepairLogController::class);
    Route::get('/api/vehicle-consumption/{vehicle}', [FuelEntryController::class, 'apiChartData'])
        ->name('api.vehicle.consumption');
    Route::post('/dashboard-charts', [DashboardController::class, 'getChartData']);
    Route::get('/vehicle-detailed-chart/{vehicle}', [DashboardController::class, 'getVehicleDetailedChart']);

    Route::post('/dashboard/charts', [DashboardController::class, 'getChartData'])->name('dashboard.charts');
    Route::get('/dashboard/vehicle-chart/{vehicle}', [DashboardController::class, 'getVehicleDetailedChart'])->name('dashboard.vehicle-chart');
    Route::get('repair-logs/{repairLog}/download-facture', [RepairLogController::class, 'downloadFacture'])
        ->name('repair-logs.download-facture');

});


require __DIR__.'/auth.php';
