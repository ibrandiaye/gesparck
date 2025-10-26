<?php

// app/Http/Controllers/ClientController.php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Afficher la liste des clients
     */
    public function index(Request $request)
    {
        $query = Client::withCount(['trips as total_trajets', 'trips as total_voyages' => function($q) {
            $q->select(DB::raw('COALESCE(SUM(nombre_trajets), 0)'));
        }]);



        // Recherche
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%");
                 /* ->orWhere('ville', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");*/
            });
        }

        $clients = $query->orderBy('nom')->paginate(20);

        return view('clients.index', compact('clients'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Enregistrer un nouveau client
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:clients',
            'adresse' => 'required|string|max:500',

        ]);

        $client = Client::create($validated);

        return redirect()->route('clients.show', $client->id)
                       ->with('success', 'Client créé avec succès!');
    }

    /**
     * Afficher les détails d'un client
     */
    public function show(Client $client)
    {
        $client->loadCount(['trips as total_trajets', 'trips as total_voyages' => function($q) {
            $q->select(DB::raw('COALESCE(SUM(nombre_trajets), 0)'));
        }]);

        $trips = $client->trips()
                       ->with(['vehicle', 'fuelEntry'])
                       ->orderBy('date_trajet', 'desc')
                       ->paginate(15);

        $statistiques = $client->statistiques;

        return view('clients.show', compact('client', 'trips', 'statistiques'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Mettre à jour un client
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:clients,nom,' . $client->id,
            'adresse' => 'required|string|max:500',

        ]);

        $client->update($validated);

        return redirect()->route('clients.show', $client->id)
                       ->with('success', 'Client mis à jour avec succès!');
    }

    /**
     * Supprimer un client
     */
    public function destroy(Client $client)
    {
        // Vérifier si le client a des trajets associés
        if ($client->trips()->exists()) {
            return back()->withErrors([
                'error' => 'Impossible de supprimer ce client car il a des trajets associés.'
            ]);
        }

        $client->delete();

        return redirect()->route('clients.index')
                       ->with('success', 'Client supprimé avec succès!');
    }

    /**
     * Désactiver un client
     */
    public function desactiver(Client $client)
    {
        $client->update(['actif' => false]);

        return back()->with('success', 'Client désactivé avec succès!');
    }

    /**
     * Activer un client
     */
    public function activer(Client $client)
    {
        $client->update(['actif' => true]);

        return back()->with('success', 'Client activé avec succès!');
    }

    /**
     * Statistiques des clients
     */
  /*  public function statistics()
    {
        $topClients = Client::withCount(['trips as total_trajets', 'trips as total_voyages' => function($q) {
            $q->select(DB::raw('COALESCE(SUM(nombre_trajets), 0)'));
        }])
        ->whereHas('trips')
        ->orderByDesc('total_voyages')
        ->limit(10)
        ->get();

        $clientsParVille = Client::selectRaw('ville, COUNT(*) as nombre_clients, SUM(
            (SELECT COUNT(*) FROM trips WHERE trips.client_id = clients.id)
        ) as total_trajets')
        ->whereNotNull('ville')
        ->groupBy('ville')
        ->orderByDesc('nombre_clients')
        ->get();

        return view('clients.statistics', compact('topClients', 'clientsParVille'));
    }*/

    public function statistics()
    {
        //dd('ok');
        // Top clients par nombre de voyages
        $topClients = Client::withCount(['trips as total_trajets', 'trips as total_voyages' => function($q) {
            $q->select(DB::raw('COALESCE(SUM(nombre_trajets), 0)'));
        }])
        ->whereHas('trips')
        ->orderByDesc('total_voyages')
        ->limit(10)
        ->get();

        // Clients par ville avec statistiques
        $clientsParVille = Client::selectRaw('
            adresse as ville,
            COUNT(*) as nombre_clients,
            SUM(
                (SELECT COUNT(*) FROM trips WHERE trips.client_id = clients.id)
            ) as total_trajets
        ')
        ->whereNotNull('adresse')
        ->where('adresse', '!=', '')
        ->groupBy('adresse')
        ->orderByDesc('nombre_clients')
        ->get();

        return view('clients.statistics', compact('topClients', 'clientsParVille'));
    }
}
