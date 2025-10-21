<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\FuelEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TripController extends Controller
{
    /**
     * Afficher la liste des trajets
     */
    public function index(Request $request)
    {
        $query = Trip::with(['vehicle', 'fuelEntry']);

        // Filtres
        if ($request->has('vehicle_id') && $request->vehicle_id) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->has('motif') && $request->motif) {
            $query->where('motif', $request->motif);
        }

        if ($request->has('date_debut') && $request->date_debut) {
            $query->where('date_trajet', '>=', $request->date_debut);
        }

        if ($request->has('date_fin') && $request->date_fin) {
            $query->where('date_trajet', '<=', $request->date_fin);
        }

        $trips = $query->orderBy('date_trajet', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->paginate(20);

        $vehicles = Vehicle::all();
        $motifs = ['livraison', 'client', 'maintenance', 'administratif', 'autre'];

        return view('trips.index', compact('trips', 'vehicles', 'motifs'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create(Request $request)
    {
        $fuelEntry = null;

        $motifs =
            [
                'livraison' => 'Livraison',
                'client' => 'Rendez-vous client',
                'maintenance' => 'Maintenance',
                'administratif' => 'Démarche administrative',
                'autre' => 'Autre'
            ];

        if(isset($_GET['vehicle_id'])  && isset($_GET['entry_id']))
        {
            //dd('ok');
            $entry = FuelEntry::findOrFail($_GET['entry_id']);
            $vehicle = Vehicle::findOrFail($_GET['vehicle_id']);
            //dd($entry,$vehicle);
            return view('trips.create-by-fuel', compact(
            'vehicle',
            'entry',
            'motifs'
        ));
        }
        if ($request->has('fuel_entry_id')) {
            $fuelEntry = FuelEntry::findOrFail($request->fuel_entry_id);
        }

        $vehicles = Vehicle::where('etat','disponible')->get();
        $fuelEntries = FuelEntry::with('vehicle')
                              ->orderBy('date_remplissage', 'desc')
                              ->get();



        return view('trips.create', compact(
            'vehicles',
            'fuelEntries',
            'fuelEntry',
            'motifs'
        ));
    }

    /**
     * Enregistrer un nouveau trajet
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'fuel_entry_id' => 'required|exists:fuel_entries,id',
            'destination' => 'required|string|max:255',
            'motif' => 'required|in:livraison,client,maintenance,administratif,autre',
            'nombre_trajets' => 'required|integer|min:1|max:50',
            'date_trajet' => 'required|date',
            //'km_depart' => 'required|integer|min:0',
           // 'km_arrivee' => 'required|integer|gt:km_depart',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Vérifier la cohérence des kilométrages avec le véhicule
      /*  $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        if ($validated['km_depart'] < $vehicle->kilometrage_actuel - 1000) {
            return back()->withErrors([
                'km_depart' => 'Le kilométrage de départ semble incohérent avec le kilométrage actuel du véhicule.'
            ])->withInput();
        }*/

        DB::beginTransaction();

        try {
            // Créer le trajet
            $trip = Trip::create($validated);

          /*  // Mettre à jour le kilométrage du véhicule
            $vehicle->kilometrage_actuel = $validated['km_arrivee'];
            $vehicle->save();*/

            DB::commit();

            return redirect()->route('trips.show', $trip->id)
                           ->with('success', 'Trajet enregistré avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de l\'enregistrement du trajet: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Afficher les détails d'un trajet
     */
    public function show(Trip $trip)
    {
        $trip->load(['vehicle', 'fuelEntry']);

        //dd($trip);

        return view('trips.show', compact('trip'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Trip $trip)
    {
        $trip->load(['vehicle', 'fuelEntry']);

        $vehicles = Vehicle::where('etat','disponible')->get();
        $fuelEntries = FuelEntry::with('vehicle')
                              ->orderBy('date', 'desc')
                              ->get();

        $motifs = [
            'livraison' => 'Livraison',
            'client' => 'Rendez-vous client',
            'maintenance' => 'Maintenance',
            'administratif' => 'Démarche administrative',
            'autre' => 'Autre'
        ];

        return view('trips.edit', compact(
            'trip',
            'vehicles',
            'fuelEntries',
            'motifs'
        ));
    }

    /**
     * Mettre à jour un trajet
     */
    public function update(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'fuel_entry_id' => 'required|exists:fuel_entries,id',
            'destination' => 'required|string|max:255',
            'motif' => 'required|in:livraison,client,maintenance,administratif,autre',
            'nombre_trajets' => 'required|integer|min:1|max:50',
            'date_trajet' => 'required|date',
         //   'km_depart' => 'required|integer|min:0',
          //  'km_arrivee' => 'required|integer|gt:km_depart',
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();

        try {
            // Sauvegarder les anciennes valeurs pour mise à jour du kilométrage
            $oldKmArrivee = $trip->km_arrivee;
            $oldVehicleId = $trip->vehicle_id;

            // Mettre à jour le trajet
            $trip->update($validated);

            // Mettre à jour le kilométrage du véhicule si nécessaire
            if ($oldVehicleId != $validated['vehicle_id'] || $oldKmArrivee != $validated['km_arrivee']) {
                $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
                $vehicle->kilometrage_actuel = $validated['km_arrivee'];
                $vehicle->save();
            }

            DB::commit();

            return redirect()->route('trips.show', $trip->id)
                           ->with('success', 'Trajet mis à jour avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de la mise à jour du trajet: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Supprimer un trajet
     */
    public function destroy(Trip $trip)
    {
        DB::beginTransaction();

        try {
            $trip->delete();

            DB::commit();

            return redirect()->route('trips.index')
                           ->with('success', 'Trajet supprimé avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de la suppression du trajet: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Afficher les trajets pour un plein spécifique
     */
    public function byFuelEntry(FuelEntry $fuelEntry)
    {
        $trips = Trip::with(['vehicle'])
                    ->where('fuel_entry_id', $fuelEntry->id)
                    ->orderBy('date_trajet', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();

        $stats = [
            'total_trajets' => $trips->sum('nombre_trajets'),
            /*'distance_totale' => $trips->sum(function($trip) {
                return $trip->distance_totale;
            }),*/
            'consommation_moyenne' => $fuelEntry->efficacite ?? 0
        ];

        return view('trips.by-fuel-entry', compact('fuelEntry', 'trips', 'stats'));
    }

    /**
     * Statistiques des trajets
     */
    public function statistics()
    {
        //dd("dff");
        // Statistiques par motif
        $statsByMotif = Trip::selectRaw('motif, COUNT(*) as nombre_trajets, SUM(nombre_trajets) as total_voyages')
            ->groupBy('motif')
            ->get();

        //dd($statsByMotif);

        // Top destinations
        $topDestinations = Trip::selectRaw('destination, COUNT(*) as nombre_visites, SUM(nombre_trajets) as total_trajets')
            ->groupBy('destination')
            ->orderByDesc('nombre_visites')
            ->limit(10)
            ->get();

        // Trajets par mois (12 derniers mois)
        $monthlyStats = Trip::selectRaw('
                YEAR(date_trajet) as annee,
                MONTH(date_trajet) as mois,
                COUNT(*) as nombre_trajets,
                SUM(nombre_trajets) as total_voyages
            ')
            ->where('date_trajet', '>=', now()->subMonths(12))
            ->groupBy('annee', 'mois')
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->get();

        // Véhicules les plus utilisés
        $topVehicles = Trip::with('vehicle')
            ->selectRaw('vehicle_id, COUNT(*) as nombre_trajets, SUM(nombre_trajets) as total_voyages')
            ->groupBy('vehicle_id')
            ->orderByDesc('nombre_trajets')
            ->limit(10)
            ->get();

        return view('trips.statistics', compact(
            'statsByMotif',
            'topDestinations',
            'monthlyStats',
            'topVehicles'
        ));
    }

    /**
     * Export CSV des trajets
     */
    public function export(Request $request)
    {
        $trips = Trip::with(['vehicle', 'fuelEntry'])
                    ->when($request->has('date_debut'), function($query) use ($request) {
                        $query->where('date_trajet', '>=', $request->date_debut);
                    })
                    ->when($request->has('date_fin'), function($query) use ($request) {
                        $query->where('date_trajet', '<=', $request->date_fin);
                    })
                    ->when($request->has('vehicle_id'), function($query) use ($request) {
                        $query->where('vehicle_id', $request->vehicle_id);
                    })
                    ->orderBy('date_trajet', 'desc')
                    ->get();

        $fileName = 'trajets_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($trips) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'Date',
                'Véhicule',
                'Immatriculation',
               // 'Conducteur',
                'Destination',
                'Motif',
                'Nb Trajets',
                //'KM Départ',
               // 'KM Arrivée',
               // 'Distance/Trajet',
               // 'Distance Total',
                'Date Plein',
                'Notes'
            ]);

            // Données
            foreach ($trips as $trip) {
                fputcsv($file, [
                    $trip->date_trajet->format('d/m/Y'),
                    $trip->vehicle->modele,
                    $trip->vehicle->immatriculation,
                  //  $trip->vehicle->conducteur ?? 'Non assigné', // Conducteur depuis le véhicule
                    $trip->destination,
                    $trip->motif,
                    $trip->nombre_trajets,
                  //  $trip->km_depart,
                  //  $trip->km_arrivee,
                   // $trip->distance_moyenne,
                  //  $trip->distance_totale,
                    $trip->fuelEntry->date->format('d/m/Y'),
                    $trip->notes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API pour récupérer les conducteurs disponibles (si besoin)
     */
    public function getConducteurs()
    {
        $conducteurs = Vehicle::whereNotNull('immatriculation')
                            ->where('immatriculation', '!=', '')
                            ->distinct()
                            ->pluck('immatriculation')
                            ->sort()
                            ->values();

        return response()->json($conducteurs);
    }

    public function getByVehicle(Request $request)
    {
        $vehicles = Vehicle::where('etat', 'disponible')->get();

        return response()->json($vehicles);
    }
}
