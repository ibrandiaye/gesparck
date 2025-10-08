<?php

use App\Http\Controllers\CarburantController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FuelEntryController;
use App\Http\Controllers\RepairLogController;
use App\Http\Controllers\ReportController;
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
    Route::resource('carburant', CarburantController::class);

    Route::get('/api/vehicle-consumption/{vehicle}', [FuelEntryController::class, 'apiChartData'])
        ->name('api.vehicle.consumption');
    Route::post('/dashboard-charts', [DashboardController::class, 'getChartData']);
    Route::get('/vehicle-detailed-chart/{vehicle}', [DashboardController::class, 'getVehicleDetailedChart']);

    Route::post('/dashboard/charts', [DashboardController::class, 'getChartData'])->name('dashboard.charts');
    Route::get('/dashboard/vehicle-chart/{vehicle}', [DashboardController::class, 'getVehicleDetailedChart'])->name('dashboard.vehicle-chart');
    Route::get('repair-logs/{repairLog}/download-facture', [RepairLogController::class, 'downloadFacture'])
        ->name('repair-logs.download-facture');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/data', [ReportController::class, 'getReportData'])->name('reports.data');
    // Rapports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/data', [ReportController::class, 'getReportData'])->name('reports.data');

    // Exports
    Route::post('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::post('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::post('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');

});


require __DIR__.'/auth.php';
