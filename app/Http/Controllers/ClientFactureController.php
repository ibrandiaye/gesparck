<?php

namespace App\Http\Controllers;

use App\Models\ClientFacture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientFactureController extends Controller
{
      public function index(Request $request)
    {
        $query = ClientFacture::query();



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

        return view('clientfactures.index', compact('clients'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('clientfactures.create');
    }

    /**
     * Enregistrer un nouveau client
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:client_factures',
            'adresse' => 'required|string|max:500',

        ]);

        $client = ClientFacture::create($validated);

        return redirect()->route('clientfactures.show', $client->id)
                       ->with('success', 'Client créé avec succès!');
    }

    /**
     * Afficher les détails d'un client
     */
    public function show(ClientFacture $client)
    {


       $statistiques = $client->statistiques;

        return view('clientfactures.show', compact('client','statistiques'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(ClientFacture $client)
    {
        return view('clientfactures.edit', compact('client'));
    }

    /**
     * Mettre à jour un client
     */
    public function update(Request $request, ClientFacture $client)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:clients,nom,' . $client->id,
            'adresse' => 'required|string|max:500',

        ]);

        $client->update($validated);

        return redirect()->route('clientfactures.show', $client->id)
                       ->with('success', 'Client mis à jour avec succès!');
    }

    /**
     * Supprimer un client
     */
    public function destroy(ClientFacture $client)
    {
        // Vérifier si le client a des trajets associés
        if ($client->trips()->exists()) {
            return back()->withErrors([
                'error' => 'Impossible de supprimer ce client car il a des trajets associés.'
            ]);
        }

        $client->delete();

        return redirect()->route('clientfactures.index')
                       ->with('success', 'Client supprimé avec succès!');
    }

    /**
     * Désactiver un client
     */
    public function desactiver(ClientFacture $client)
    {
        $client->update(['actif' => false]);

        return back()->with('success', 'Client désactivé avec succès!');
    }

    /**
     * Activer un client
     */
    public function activer(ClientFacture $client)
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
            (SELECT COUNT(*) FROM trips WHERE trips.client_id = clientfactures.id)
        ) as total_trajets')
        ->whereNotNull('ville')
        ->groupBy('ville')
        ->orderByDesc('nombre_clients')
        ->get();

        return view('clientfactures.statistics', compact('topClients', 'clientsParVille'));
    }*/

    public function statistics()
    {
        //dd('ok');
        // Top clients par nombre de voyages
        $topClients = ClientFacture::withCount(['trips as total_trajets', 'trips as total_voyages' => function($q) {
            $q->select(DB::raw('COALESCE(SUM(nombre_trajets), 0)'));
        }])
        ->whereHas('trips')
        ->orderByDesc('total_voyages')
        ->limit(10)
        ->get();

        // Clients par ville avec statistiques
        $clientsParVille = ClientFacture::selectRaw('
            adresse as ville,
            COUNT(*) as nombre_clients,
            SUM(
                (SELECT COUNT(*) FROM trips WHERE trips.client_id = clientfactures.id)
            ) as total_trajets
        ')
        ->whereNotNull('adresse')
        ->where('adresse', '!=', '')
        ->groupBy('adresse')
        ->orderByDesc('nombre_clients')
        ->get();

        return view('clientfactures.statistics', compact('topClients', 'clientsParVille'));
    }
}
