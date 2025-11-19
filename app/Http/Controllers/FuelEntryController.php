<?php

namespace App\Http\Controllers;

use App\Models\FuelEntry;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FuelEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['getByVehicle']);
    }



    public function index(Request $request)
    {
        // Récupérer tous les véhicules pour le filtre
        $vehicles = Vehicle::orderBy('immatriculation')->get();

        // Commencer la requête avec les relations
        $query = FuelEntry::with(['vehicle', 'trips']);

        // Appliquer les filtres
        if ($request->has('vehicle') && $request->vehicle != '') {
            $query->where('vehicle_id', $request->vehicle);
        }

        if ($request->has('type_carburant') && $request->type_carburant != '') {
            $query->where('type_carburant', $request->type_carburant);
        }

        if ($request->has('month') && $request->month != '') {
            $query->whereMonth('date_remplissage', $request->month);
        }

        if ($request->has('year') && $request->year != '') {
            $query->whereYear('date_remplissage', $request->year);
        }

        if ($request->has('station') && $request->station != '') {
            $query->where('station', 'like', '%' . $request->station . '%');
        }
        if ($request->has('date_debut') && $request->date_debut) {
            $query->where('date_remplissage', '>=', $request->date_debut);
        }

        if ($request->has('date_fin') && $request->date_fin) {
            $query->where('date_remplissage', '<=', $request->date_fin);
        }

        // Appliquer les filtres pour les statistiques aussi
        $statsQuery = clone $query;

       /* $stats = [
            'totalCoutMois' => $statsQuery->when(!$request->has('month') && !$request->has('year'), function($q) {
                return $q->thisMonth();
            })->sum('cout_total'),

            'totalLitresMois' => $statsQuery->when(!$request->has('month') && !$request->has('year'), function($q) {
                return $q->thisMonth();
            })->sum('litres'),

            'moyennePrixLitre' => $statsQuery->when(!$request->has('month') && !$request->has('year'), function($q) {
                return $q->thisMonth();
            })->avg('prix_litre')
        ];
        */

         $stats = [
            'totalCoutMois' => $statsQuery->sum('cout_total'),

            'totalLitresMois' => $statsQuery->sum('litres'),

            'moyennePrixLitre' => $statsQuery->avg('prix_litre')
        ];

        // Pagination avec conservation des paramètres de filtre
       /* $fuelEntries = $query->orderBy('date_remplissage', 'desc')
                            ->paginate(20)
                            ->appends($request->except('page')); */

        $fuelEntries = $query->orderBy('date_remplissage', 'desc')
                            ->limit(3000)
                            ->get();

        // Mois en français
        $frenchMonths = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        // Préparer les données pour les filtres
        $filters = [
            'vehicles' => $vehicles,
            'typesCarburant' => FuelEntry::select('type_carburant')->distinct()->pluck('type_carburant'),
            'years' => FuelEntry::select(DB::raw('YEAR(date_remplissage) as year'))
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->pluck('year'),
            'months' => $frenchMonths,
            'selectedVehicle' => $request->vehicle,
            'selectedTypeCarburant' => $request->type_carburant,
            'selectedMonth' => $request->month,
            'selectedYear' => $request->year,
            'selectedStation' => $request->station,
        ];

        return view('fuel-entries.index', compact('fuelEntries', 'stats', 'filters'));
    }

    public function create()
    {
        $vehicles = Vehicle::with('carburant')->where('etat', 'disponible')->get();
        //dd($vehicles);
        $thisVehicle = null;
        if (isset($_GET['vehicle_id'])) {
            $thisVehicle = Vehicle::with('carburant')->find($_GET['vehicle_id']);

        }
       // dd($vehicle);
       // dd($_GET['vehicle_id']);
        return view('fuel-entries.create', compact('vehicles','thisVehicle'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'date_remplissage' => 'required|date|before_or_equal:today',
            'prix_litre' => 'required|numeric|min:0|max:1000',
            'litres' => 'required|numeric|min:1|max:1000',
            'kilometrage' => 'required|integer|min:0',
            'station' => 'nullable|string|max:100',
            'type_carburant' => 'required',
            'notes' => 'nullable|string|max:500'
        ]);

        // Calcul automatique du coût total
        $validated['cout_total'] = FuelEntry::calculateTotalCost(
            $validated['prix_litre'],
            $validated['litres']
        );

        try {
            DB::beginTransaction();

            $fuelEntry = FuelEntry::create($validated);

            // Mettre à jour le kilométrage du véhicule
            $vehicle = Vehicle::find($validated['vehicle_id']);
            if ($validated['kilometrage'] > $vehicle->kilometrage_actuel) {
                $vehicle->update(['kilometrage_actuel' => $validated['kilometrage']]);
            }

            DB::commit();

            return redirect()->route('fuel-entries.show', $fuelEntry)
                ->with('success', 'Remplissage enregistré avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(FuelEntry $fuelEntry)
    {
        $fuelEntry->load('vehicle');

        // Calculer les statistiques pour ce véhicule
        $vehicleStats = $this->getVehicleFuelStats($fuelEntry->vehicle_id);

        return view('fuel-entries.show', compact('fuelEntry', 'vehicleStats'));
    }

    public function edit(FuelEntry $fuelEntry)
    {
        $vehicles = Vehicle::with('carburant')->get();
        return view('fuel-entries.edit', compact('fuelEntry', 'vehicles'));
    }

    public function update(Request $request, FuelEntry $fuelEntry)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'date_remplissage' => 'required|date|before_or_equal:today',
            'prix_litre' => 'required|numeric|min:0|max:1000',
            'litres' => 'required|numeric|min:1|max:1000',
            'kilometrage' => 'required|integer|min:0',
            'station' => 'nullable|string|max:100',
            'type_carburant' => 'required|in:diesel,essence,sp95,sp98,gpl',
            'notes' => 'nullable|string|max:500'
        ]);

        $validated['cout_total'] = FuelEntry::calculateTotalCost(
            $validated['prix_litre'],
            $validated['litres']
        );

        try {
            DB::beginTransaction();

            $fuelEntry->update($validated);

            // Mettre à jour le kilométrage du véhicule si nécessaire
            $vehicle = Vehicle::find($validated['vehicle_id']);
            $maxKilometrage = FuelEntry::where('vehicle_id', $vehicle->id)->max('kilometrage');
            $vehicle->update(['kilometrage_actuel' => $maxKilometrage]);

            DB::commit();

            return redirect()->route('fuel-entries.show', $fuelEntry)
                ->with('success', 'Remplissage modifié avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erreur lors de la modification: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(FuelEntry $fuelEntry)
    {
        try {
            DB::beginTransaction();

            $vehicleId = $fuelEntry->vehicle_id;
            $fuelEntry->delete();

            // Recalculer le kilométrage actuel du véhicule
            $maxKilometrage = FuelEntry::where('vehicle_id', $vehicleId)->max('kilometrage');
            $vehicle = Vehicle::find($vehicleId);
            $vehicle->update(['kilometrage_actuel' => $maxKilometrage ?? $vehicle->kilometrage_actuel]);

            DB::commit();

            return redirect()->route('fuel-entries.index')
                ->with('success', 'Remplissage supprimé avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    // Méthode pour les statistiques du véhicule
    private function getVehicleFuelStats($vehicleId)
    {
        $entries = FuelEntry::where('vehicle_id', $vehicleId)
            ->orderBy('date_remplissage')
            ->get();

        if ($entries->count() < 2) {
            return null;
        }

        $stats = [
            'consommation_moyenne' => 0,
            'cout_total' => $entries->sum('cout_total'),
            'distance_totale' => $entries->last()->kilometrage - $entries->first()->kilometrage,
            'nombre_remplissages' => $entries->count()
        ];

        $totalConsommation = 0;
        $calculsValides = 0;

        for ($i = 1; $i < $entries->count(); $i++) {
            $kmParcourus = $entries[$i]->kilometrage - $entries[$i-1]->kilometrage;
            if ($kmParcourus > 0) {
                $consommation = ($entries[$i]->litres / $kmParcourus) * 100;
                $totalConsommation += $consommation;
                $calculsValides++;
            }
        }

        if ($calculsValides > 0) {
            $stats['consommation_moyenne'] = round($totalConsommation / $calculsValides, 2);
        }

        return $stats;
    }

    // API pour les graphiques
    public function apiChartData($vehicleId)
    {
        $entries = FuelEntry::where('vehicle_id', $vehicleId)
            ->orderBy('date_remplissage')
            ->get(['date_remplissage', 'consommation', 'prix_litre', 'cout_total']);

        return response()->json($entries);
    }
    public function getByVehicle(Request $request)
    {
        $vehicleId = $request->input('vehicle_id');
        $fuelEntries = FuelEntry::where('vehicle_id', $vehicleId)->orderBy("id",'desc')->get();
        return response()->json($fuelEntries);
    }
}
