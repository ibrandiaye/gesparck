<?php

namespace App\Http\Controllers;

use App\Models\Client;
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
  /*  public function index(Request $request)
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
                      ->orderBy('vehicle_id', 'desc')
                      ->paginate(20);

        $vehicles = Vehicle::all();
        $motifs = ['livraison', 'client', 'maintenance', 'administratif', 'autre'];

        return view('trips.index', compact('trips', 'vehicles', 'motifs'));
    }*/

          public function index(Request $request)
    {
        $query = Trip::with(['vehicle', 'fuelEntry', 'clients']);

        // Filtres
        if ($request->has('vehicle_id') && $request->vehicle_id) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->has('motif') && $request->motif) {
            $query->where('motif', $request->motif);
        }

        if ($request->has('avec_clients') && $request->avec_clients !== '') {
            if ($request->avec_clients == '1') {
                $query->whereHas('clients');
            } else {
                $query->whereDoesntHave('clients');
            }
        }

        if ($request->has('date_debut') && $request->date_debut) {
            $query->where('date_trajet', '>=', $request->date_debut);
        }

        if ($request->has('date_fin') && $request->date_fin) {
            $query->where('date_trajet', '<=', $request->date_fin);
        }

        $trips = $query
                      ->orderBy('date_trajet', 'desc')
                      ->orderBy('vehicle_id', 'desc')
                      ->limit(1000)
                      ->get();

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

        if ($request->has('fuel_entry_id')) {
            $fuelEntry = FuelEntry::findOrFail($request->fuel_entry_id);
        }

        $vehicles = Vehicle::get();
        $fuelEntries = FuelEntry::with('vehicle')
                            ->orderBy('date_remplissage', 'desc')
                            ->get();
        $clients = Client::orderBy('nom')->get();

        $motifs = [
            'livraison' => 'Tournée de livraison',
            'client' => 'Visites clients',
            'maintenance' => 'Maintenance',
            'administratif' => 'Démarche administrative',
            'autre' => 'Autre'
        ];

        return view('trips.create', compact(
            'vehicles',
            'fuelEntries',
            'fuelEntry',
            'clients',
            'motifs'
        ));
    }
    /*public function create(Request $request)
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


        $clients = Client::all();
        return view('trips.create', compact(
            'vehicles',
            'fuelEntries',
            'fuelEntry',
            'motifs',
            'clients'
        ));
    }*/

    /**
     * Enregistrer un nouveau trajet
     */
   /* public function store(Request $request)
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
            'notes' => 'nullable|string|max:1000',
             'client_id' => 'required|exists:clients,id',
        ]);

        // Vérifier la cohérence des kilométrages avec le véhicule
      /*  $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        if ($validated['km_depart'] < $vehicle->kilometrage_actuel - 1000) {
            return back()->withErrors([
                'km_depart' => 'Le kilométrage de départ semble incohérent avec le kilométrage actuel du véhicule.'
            ])->withInput();
        }/

        DB::beginTransaction();

        try {
            // Créer le trajet
            $trip = Trip::create($validated);

          /*  // Mettre à jour le kilométrage du véhicule
            $vehicle->kilometrage_actuel = $validated['km_arrivee'];
            $vehicle->save();/

            DB::commit();

            return redirect()->route('trips.show', $trip->id)
                           ->with('success', 'Trajet enregistré avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de l\'enregistrement du trajet: ' . $e->getMessage()
            ])->withInput();
        }
    }*/

        public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'fuel_entry_id' => 'required|exists:fuel_entries,id',
            'nom_trajet' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'clients' => 'required|array|min:1', // Au moins un client
            'clients.*.id' => 'required|exists:clients,id',
           // 'clients.*.ordre' => 'required|integer|min:1',
           // 'clients.*.notes_livraison' => 'nullable|string|max:500',
            'destination' => 'required|string|max:255',
            'motif' => 'required|in:livraison,client,maintenance,administratif,autre',
            'nombre_trajets' => 'required|integer|min:1|max:50',
            'date_trajet' => 'required|date',
           /* 'km_depart' => 'required|integer|min:0',
            'km_arrivee' => 'required|integer|gt:km_depart',*/
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();

        try {
            // Créer le trajet
            $trip = Trip::create([
                'vehicle_id' => $validated['vehicle_id'],
                'fuel_entry_id' => $validated['fuel_entry_id'],
              //  'nom_trajet' => $validated['nom_trajet'],
               // 'description' => $validated['description'],
                'destination' => $validated['destination'],
                'motif' => $validated['motif'],
                'nombre_trajets' => $validated['nombre_trajets'],
                'date_trajet' => $validated['date_trajet'],
              //  'km_depart' => $validated['km_depart'],
               // 'km_arrivee' => $validated['km_arrivee'],
              //  'notes' => $validated['notes']
            ]);

            // Attacher les clients avec leurs ordres et notes
            $clientsData = [];
            foreach ($validated['clients'] as $clientData) {
                $clientsData[$clientData['id']] = [
                    'ordre_visite' => $clientData['ordre'] ?? null,
                    'notes_livraison' => $clientData['notes_livraison'] ?? null
                ];
            }

            $trip->clients()->attach($clientsData);

            // Mettre à jour le kilométrage du véhicule
          /*  $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
            $vehicle->kilometrage_actuel = $validated['km_arrivee'];
            $vehicle->save();*/

            DB::commit();

            return redirect()->route('trips.show', $trip->id)
                        ->with('success', 'Trajet avec ' . count($validated['clients']) . ' client(s) créé avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de la création du trajet: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Afficher les détails d'un trajet
     */
    public function show(Trip $trip)
    {
        $trip->load(['vehicle', 'fuelEntry','clients']);

        //dd($trip);

        return view('trips.show', compact('trip'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Trip $trip)
    {
       // dd($trip);
        $trip->load(['vehicle', 'fuelEntry', 'clients']);

        $vehicles = Vehicle::get();
        $fuelEntries = FuelEntry::with('vehicle')
                              ->orderBy('date_remplissage', 'desc')
                              ->where("vehicle_id",$trip->vehicle_id)
                              ->get();
        $clients = Client::orderBy('nom')->get();

        $motifs = [
            'livraison' => 'Tournée de livraison',
            'client' => 'Visites clients',
            'maintenance' => 'Maintenance',
            'administratif' => 'Démarche administrative',
            'autre' => 'Autre'
        ];

        return view('trips.edit', compact(
            'trip',
            'vehicles',
            'fuelEntries',
            'clients',
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
           // 'nom_trajet' => 'nullable|string|max:255',
           // 'description' => 'nullable|string|max:1000',
            'clients' => 'required|array|min:1',
            'clients.*.id' => 'required|exists:clients,id',
           // 'clients.*.ordre' => 'required|integer|min:1',
            //'clients.*.notes_livraison' => 'nullable|string|max:500',
            'destination' => 'required|string|max:255',
            'motif' => 'required|in:livraison,client,maintenance,administratif,autre',
            'nombre_trajets' => 'required|integer|min:1|max:50',
            'date_trajet' => 'required|date',

          //  'notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();

        try {
            // Sauvegarder les anciennes valeurs pour mise à jour du kilométrage
         //   $oldKmArrivee = $trip->km_arrivee;
            $oldVehicleId = $trip->vehicle_id;

            // Mettre à jour le trajet
            $trip->update([
                'vehicle_id' => $validated['vehicle_id'],
                'fuel_entry_id' => $validated['fuel_entry_id'],
              //  'nom_trajet' => $validated['nom_trajet'],
             //   'description' => $validated['description'],
               // 'destination' => $validated['destination'],
                'motif' => $validated['motif'],
                'nombre_trajets' => $validated['nombre_trajets'],
                'date_trajet' => $validated['date_trajet'],
              //  'km_depart' => $validated['km_depart'],
               // 'km_arrivee' => $validated['km_arrivee'],
              //  'notes' => $validated['notes']
            ]);

            // Synchroniser les clients avec leurs ordres et notes
            $clientsData = [];
            foreach ($validated['clients'] as $clientData) {
                $clientsData[$clientData['id']] = [
                    'ordre_visite' => $clientData['ordre'],
                    'notes_livraison' => $clientData['notes_livraison'] ?? null
                ];
            }

            $trip->clients()->sync($clientsData);

            // Mettre à jour le kilométrage du véhicule si nécessaire
           /* if ($oldVehicleId != $validated['vehicle_id'] || $oldKmArrivee != $validated['km_arrivee']) {
                $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
                $vehicle->kilometrage_actuel = $validated['km_arrivee'];
                $vehicle->save();
            }*/

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
            // Détacher tous les clients avant suppression
            $trip->clients()->detach();
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
        $trips = Trip::with(['vehicle', 'clients'])
                    ->where('fuel_entry_id', $fuelEntry->id)
                    ->orderBy('date_trajet', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();

        $stats = [
            'total_trajets' => $trips->count(),
            'total_clients' => $trips->sum(function($trip) {
                return $trip->clients->count();
            }),
            'consommation_moyenne' => $fuelEntry->efficacite ?? 0
        ];

        return view('trips.by-fuel-entry', compact('fuelEntry', 'trips', 'stats'));
    }

    /**
     * Statistiques des trajets
     */
    public function statistics(Request $request)
    {
        //dd($request->all());
        // Statistiques par motif
        $statsByMotif = Trip::selectRaw('motif, COUNT(*) as nombre_trajets, SUM(nombre_trajets) as total_voyages')
         ->when($request->has('date_debut'), function($query) use ($request) {
                        $query->where('date_trajet', '>=', $request->date_debut);
                    })
                    ->when($request->has('date_fin'), function($query) use ($request) {
                        $query->where('date_trajet', '<=', $request->date_fin);
                    })
            ->groupBy('motif')
            ->get();

        // Top destinations
        $topDestinations = Trip::selectRaw('destination, COUNT(*) as nombre_visites')
            ->groupBy('destination')
            ->orderByDesc('nombre_visites')
            ->limit(10)
            ->get();

        // Trajets par mois (12 derniers mois)
        $monthlyStats = Trip::selectRaw('
                YEAR(date_trajet) as annee,
                MONTH(date_trajet) as mois,
                COUNT(*) as nombre_trajets,
                SUM(nombre_trajets) as total_voyages,
                COUNT(DISTINCT vehicle_id) as vehicules_utilises
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

        // Statistiques clients
        $statsClients = [
            'total_trajets_avec_clients' => Trip::has('clients')->count(),
            'moyenne_clients_par_trajet' => DB::table('trip_clients')
                ->selectRaw('COUNT(*) as total_visites, COUNT(DISTINCT trip_id) as total_trajets')
                ->first(),
            'clients_plus_visites' => DB::table('trip_clients')
                ->join('clients', 'trip_clients.client_id', '=', 'clients.id')
                ->selectRaw('clients.nom, COUNT(*) as nombre_visites')
                ->groupBy('client_id', 'clients.nom')
                ->orderByDesc('nombre_visites')
                ->limit(10)
                ->get()
        ];

        return view('trips.statistics', compact(
            'statsByMotif',
            'topDestinations',
            'monthlyStats',
            'topVehicles',
            'statsClients'
        ));
    }

    /**
     * Export CSV des trajets
     */
    public function export(Request $request)
    {
        $trips = Trip::with(['vehicle', 'fuelEntry', 'clients'])
                    ->when($request->has('date_debut'), function($query) use ($request) {
                        $query->where('date_trajet', '>=', $request->date_debut);
                    })
                    ->when($request->has('date_fin'), function($query) use ($request) {
                        $query->where('date_trajet', '<=', $request->date_fin);
                    })
                    ->when($request->has('vehicle_id'), function($query) use ($request) {
                        $query->where('vehicle_id', $request->vehicle_id);
                    })
                    ->when($request->has('avec_clients'), function($query) use ($request) {
                        if ($request->avec_clients == '1') {
                            $query->whereHas('clients');
                        } elseif ($request->avec_clients == '0') {
                            $query->whereDoesntHave('clients');
                        }
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
                'Nom du trajet',
                'Véhicule',
                'Immatriculation',
                'Clients visités',
                'Ordre des clients',
                'Destination',
                'Motif',
                'Nb Trajets',
                'KM Départ',
                'Date Plein',
                'Litres',
                'Coût',
                'Notes'
            ]);

            // Données
            foreach ($trips as $trip) {
                $clientsList = $trip->clients->map(function($client) {
                    return $client->nom . ' (#'.$client->pivot->ordre_visite.')';
                })->implode('; ');

                $ordreClients = $trip->clients->sortBy('pivot.ordre_visite')
                    ->pluck('pivot.ordre_visite')
                    ->implode(', ');

                fputcsv($file, [
                    $trip->date_trajet->format('d/m/Y'),
                    $trip->nom_trajet ?? '',
                    $trip->vehicle->modele,
                    $trip->vehicle->immatriculation,
                    $clientsList,
                    $ordreClients,
                    $trip->destination,
                    $trip->motif,
                    $trip->nombre_trajets,
                    $trip->fuelEntry->date_remplissage->format('d/m/Y'),
                    $trip->fuelEntry->litres,
                    $trip->fuelEntry->cout,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API pour récupérer les trajets d'un client
     */
    public function getTripsByClient(Client $client)
    {
        $trips = $client->trips()
                       ->with(['vehicle', 'fuelEntry'])
                       ->orderBy('date_trajet', 'desc')
                       ->paginate(15);

        return response()->json([
            'client' => $client->only(['id', 'nom', 'ville']),
            'trips' => $trips->items(),
            'pagination' => [
                'current_page' => $trips->currentPage(),
                'last_page' => $trips->lastPage(),
                'total' => $trips->total()
            ]
        ]);
    }

    /**
     * Recherche de trajets
     */
    public function search(Request $request)
    {
        $query = Trip::with(['vehicle', 'clients']);

        if ($request->has('q') && $request->q) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nom_trajet', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('clients', function($q) use ($search) {
                      $q->where('nom', 'like', "%{$search}%");
                  })
                  ->orWhereHas('vehicle', function($q) use ($search) {
                      $q->where('immatriculation', 'like', "%{$search}%")
                        ->orWhere('modele', 'like', "%{$search}%");
                  });
            });
        }

        $trips = $query->orderBy('date_trajet', 'desc')
                      ->limit(20)
                      ->get();

        return response()->json($trips);
    }

}
