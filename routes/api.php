<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FuelEntryController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware(['auth'])->group(function () {

});
 Route::post('/dashboard-charts', [DashboardController::class, 'getChartData']);
Route::get('/vehicle-detailed-chart/{vehicle}', [DashboardController::class, 'getVehicleDetailedChart']);
Route::post('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
Route::post('/entry/by/vehicle', [FuelEntryController::class, 'getByVehicle'])->name('fuel.by.vehicle');
