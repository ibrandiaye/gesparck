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

    public function index()
    {
        $vehicle = $_GET['vehicle'] ?? null;
        if ($vehicle) {
            $fuelEntries = FuelEntry::with(['vehicle', 'trips'])
                ->where('vehicle_id', $vehicle)
                ->orderBy('date_remplissage', 'desc')
                ->paginate(20);
             $stats = [
            'totalCoutMois' => FuelEntry::where('vehicle_id', $vehicle)->thisMonth()->sum('cout_total'),
            'totalLitresMois' => FuelEntry::where('vehicle_id', $vehicle)->thisMonth()->sum('litres'),
            'moyennePrixLitre' => FuelEntry::where('vehicle_id', $vehicle)->thisMonth()->avg('prix_litre')
        ];
        }
        else {
             $fuelEntries = FuelEntry::with(['vehicle','trips'])
            ->orderBy('date_remplissage', 'desc')
            ->paginate(20);

        $stats = [
            'totalCoutMois' => FuelEntry::thisMonth()->sum('cout_total'),
            'totalLitresMois' => FuelEntry::thisMonth()->sum('litres'),
            'moyennePrixLitre' => FuelEntry::thisMonth()->avg('prix_litre')
        ];
        }


        //dd($fuelEntries);

        return view('fuel-entries.index', compact('fuelEntries', 'stats'));
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
        $fuelEntries = FuelEntry::where('vehicle_id', $vehicleId)->get();
        return response()->json($fuelEntries);
    }
}
