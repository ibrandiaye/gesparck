<?php

use App\Http\Controllers\CarburantController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientFactureController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FuelEntryController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\RepairLogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SuiviFactureController;
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


        // === CLIENTS ROUTES ===
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('clients.index');
        Route::get('/create', [ClientController::class, 'create'])->name('clients.create');
        Route::post('/', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

        // Routes supplémentaires clients
       /*  Route::post('/{client}/desactiver', [ClientController::class, 'desactiver'])->name('clients.desactiver');
        Route::post('/{client}/activer', [ClientController::class, 'activer'])->name('clients.activer'); */
    });

    Route::get('/statistics/clients', [ClientController::class, 'statistics'])->name('clients.statistics');

     // === SUIVI FACTURES ROUTES ===
    Route::prefix('suivi-factures')->group(function () {
        Route::get('/', [SuiviFactureController::class, 'index'])->name('suivi-factures.index');
        Route::get('/create', [SuiviFactureController::class, 'create'])->name('suivi-factures.create');
        Route::post('/', [SuiviFactureController::class, 'store'])->name('suivi-factures.store');
        Route::get('/{suiviFacture}', [SuiviFactureController::class, 'show'])->name('suivi-factures.show');
        Route::get('/{suiviFacture}/edit', [SuiviFactureController::class, 'edit'])->name('suivi-factures.edit');
        Route::put('/{suiviFacture}', [SuiviFactureController::class, 'update'])->name('suivi-factures.update');
        Route::delete('/{suiviFacture}', [SuiviFactureController::class, 'destroy'])->name('suivi-factures.destroy');

        // Routes supplémentaires
        Route::get('/client/{client}', [SuiviFactureController::class, 'byClient'])->name('suivi-factures.by-client');
        Route::get('/statistics/general', [SuiviFactureController::class, 'statistics'])->name('suivi-factures.statistics');

         Route::post('/{suiviFacture}/update-etat', [SuiviFactureController::class, 'updateEtat'])->name('suivi-factures.update-etat');
        Route::post('/{suiviFacture}/marquer-livre', [SuiviFactureController::class, 'marquerLivre'])->name('suivi-factures.marquer-livre');
        Route::post('/{suiviFacture}/marquer-non-livre', [SuiviFactureController::class, 'marquerNonLivre'])->name('suivi-factures.marquer-non-livre');

        Route::get('/{suiviFacture}/retour', [SuiviFactureController::class, 'showRetourForm'])->name('suivi-factures.retour');
        Route::post('/{suiviFacture}/retour', [SuiviFactureController::class, 'enregistrerRetour'])->name('suivi-factures.enregistrer-retour');
        Route::post('/{suiviFacture}/annuler-retour', [SuiviFactureController::class, 'annulerRetour'])->name('suivi-factures.annuler-retour');

    });

    Route::prefix('client-factures')->group(function () {
        Route::get('/', [ClientFactureController::class, 'index'])->name('clientfactures.index');
        Route::get('/create', [ClientFactureController::class, 'create'])->name('clientfactures.create');
        Route::post('/', [ClientFactureController::class, 'store'])->name('clientfactures.store');
        Route::get('/{client}', [ClientFactureController::class, 'show'])->name('clientfactures.show');
        Route::get('/{client}/edit', [ClientFactureController::class, 'edit'])->name('clientfactures.edit');
        Route::put('/{client}', [ClientFactureController::class, 'update'])->name('clientfactures.update');
        Route::delete('/{client}', [ClientFactureController::class, 'destroy'])->name('clientfactures.destroy');




    });



    // === PAIEMENTS ROUTES ===
    Route::prefix('paiements')->group(function () {
        Route::get('/', [PaiementController::class, 'index'])->name('paiements.index');
        Route::get('/create', [PaiementController::class, 'create'])->name('paiements.create');
        Route::post('/', [PaiementController::class, 'store'])->name('paiements.store');
        Route::get('/{paiement}', [PaiementController::class, 'show'])->name('paiements.show');
        Route::get('/{paiement}/edit', [PaiementController::class, 'edit'])->name('paiements.edit');
        Route::put('/{paiement}', [PaiementController::class, 'update'])->name('paiements.update');
        Route::delete('/{paiement}', [PaiementController::class, 'destroy'])->name('paiements.destroy');

        // Routes supplémentaires
        Route::get('/facture/{suiviFacture}', [PaiementController::class, 'byFacture'])->name('paiements.by-facture');
        Route::get('/statistics', [PaiementController::class, 'statistics'])->name('paiements.statistics');
    });


});


require __DIR__.'/auth.php';
