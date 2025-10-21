<?php

use App\Http\Controllers\CarburantController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FuelEntryController;
use App\Http\Controllers\RepairLogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
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
   // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('vehicles', VehicleController::class);
    Route::resource('fuel-entries', FuelEntryController::class);
    Route::resource('repair-logs', RepairLogController::class);
    Route::resource('carburants', CarburantController::class);
    Route::resource('user', UserController::class);

    Route::get('/modifier/motdepasse',[UserController::class,'modifierMotDePasse'])->name("modifier.motdepasse")->middleware(['auth']);//->middleware(['auth', 'checkMaxSessions']);
    Route::post('/update/password',[UserController::class,'updatePassword'])->name("user.password.update")->middleware(['auth']);//->middleware(["auth","checkMaxSessions"]);


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

    Route::prefix('trips')->group(function () {
        // CRUD de base
        Route::get('/', [TripController::class, 'index'])->name('trips.index');
        Route::get('/create', [TripController::class, 'create'])->name('trips.create');
        Route::post('/', [TripController::class, 'store'])->name('trips.store');
        Route::get('/{trip}', [TripController::class, 'show'])->name('trips.show');
        Route::get('/{trip}/edit', [TripController::class, 'edit'])->name('trips.edit');
        Route::put('/{trip}', [TripController::class, 'update'])->name('trips.update');
        Route::delete('/{trip}', [TripController::class, 'destroy'])->name('trips.destroy');

        // Routes supplémentaires
        Route::get('fuel-entries/{fuel_entry}/trips', [TripController::class, 'byFuelEntry'])
             ->name('trips.by-fuel-entry');


        // API pour les conducteurs (si besoin)
        Route::get('api/conducteurs', [TripController::class, 'getConducteurs'])
             ->name('trips.api.conducteurs');




});
        Route::get('statistics/strips', [TripController::class, 'statistics'])->name('trips.statistics');
        Route::get('export/strips', [TripController::class, 'export'])->name('trips.export');
        Route::get('/dashboard', [DashboardController::class, 'monDasboard'])->name('dashboard');
        Route::post('/dashboard', [DashboardController::class, 'monDasboardFiltre'])->name('dashboard');

});


require __DIR__.'/auth.php';
