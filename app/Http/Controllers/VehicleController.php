<?php

namespace App\Http\Controllers;

use App\Models\Carburant;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $vehicles = Vehicle::withCount(['fuelEntries', 'repairLogs'])
            ->latest()
            ->get();

        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $carburants = Carburant::get();
        return view('vehicles.create',compact('carburants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'immatriculation' => 'required|unique:vehicles|max:100',
            'marque' => 'required|max:50',
            'modele' => 'required|max:50',
            'type_vehicule' => 'required|in:voiture,camion,utilitaire,moto',
            'kilometrage_actuel' => 'required|integer|min:0',
          //  'date_mise_en_service' => 'required|date|before_or_equal:today',
             'carburant_id' => 'required|exists:carburants,id',
            'notes' => 'nullable|string|max:1000',
            'categorie' => 'string|required|in:sedipal-nestle,cdn-nestle,vehicule-livraison-sedipal',
        ]);

        try {
            DB::beginTransaction();

            Vehicle::create($validated);

            DB::commit();

            return redirect()->route('vehicles.index')
                ->with('success', 'Véhicule ajouté avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erreur lors de la création du véhicule: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Vehicle $vehicle)
{
    // Charger les relations avec tri et limite
    $vehicle->load([
        'fuelEntries' => function($query) {
            $query->orderBy('date_remplissage', 'desc');
        },
        'repairLogs' => function($query) {
            $query->orderBy('date_intervention', 'desc');
        },
    ]);

    return view('vehicles.show', compact('vehicle'));
}

    public function edit(Vehicle $vehicle)
    {
         $carburants = Carburant::get();
        return view('vehicles.edit', compact('vehicle','carburants'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'immatriculation' => 'required|max:100|unique:vehicles,immatriculation,' . $vehicle->id,
            'marque' => 'required|max:50',
            'modele' => 'required|max:50',
            'type_vehicule' => 'required|in:voiture,camion,utilitaire,moto',
            'kilometrage_actuel' => 'required|integer|min:0',
            'etat' => 'required|in:disponible,en_entretien,hors_service',
           // 'date_mise_en_service' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:1000',
            'carburant_id' => 'required|exists:carburants,id',
            'categorie' => 'string|required|in:sedipal-nestle,cdn-nestle,vehicule-livraison-sedipal',
        ]);

        try {
            DB::beginTransaction();

            $vehicle->update($validated);

            DB::commit();

            return redirect()->route('vehicles.show', $vehicle)
                ->with('success', 'Véhicule modifié avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erreur lors de la modification du véhicule: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Vehicle $vehicle)
    {
        try {
            DB::beginTransaction();

            // Vérifier s'il y a des données associées (à compléter plus tard)
            if ($vehicle->fuelEntries()->count() > 0 || $vehicle->repairLogs()->count() > 0) {
                return redirect()->back()
                    ->with('warning', 'Impossible de supprimer ce véhicule car il possède des données associées.');
            }

            $vehicle->delete();

            DB::commit();

            return redirect()->route('vehicles.index')
                ->with('success', 'Véhicule supprimé avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du véhicule: ' . $e->getMessage());
        }
    }

    // API pour les statistiques (optionnel)
    public function apiStats()
    {
        $stats = [
            'total' => Vehicle::count(),
            'disponible' => Vehicle::where('etat', 'disponible')->count(),
            'en_entretien' => Vehicle::where('etat', 'en_entretien')->count(),
            'hors_service' => Vehicle::where('etat', 'hors_service')->count(),
            'par_type' => Vehicle::select('type_vehicule', DB::raw('count(*) as total'))
                ->groupBy('type_vehicule')
                ->get()
        ];

        return response()->json($stats);
    }
}
