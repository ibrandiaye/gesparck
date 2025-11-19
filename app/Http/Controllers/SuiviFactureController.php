<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientFacture;
use App\Models\SuiviFacture;
use Illuminate\Http\Request;

class SuiviFactureController extends Controller
{
    /**
     * Afficher la liste des factures
     */
    public function index(Request $request)
    {
        $query = SuiviFacture::with('client');
        $filtre = false;

        // Filtres
        if ($request->has('client_id') && $request->client_id) {
            $query->where('client_id', $request->client_id);
            $filtre = true;
        }

        if ($request->has('date_debut') && $request->date_debut) {
            $query->where('date_livraison', '>=', $request->date_debut);
            $filtre = true;
        }

        if ($request->has('date_fin') && $request->date_fin) {
            $query->where('date_livraison', '<=', $request->date_fin);
            $filtre = true;
        }
        if ($request->has('etat') && $request->etat) {
            $query->where('etat', $request->etat);
            $filtre = true;
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_facture', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('nom', 'like', "%{$search}%");
                  });
            });
            $filtre = true;
        }
        if($filtre==true)
        {
             $factures = $query->orderBy('date_facture', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->get();
        }
        else{
            $factures = $query->orderBy('date_facture', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->limit(5000)
                         ->get();
        }



        $clients = ClientFacture::orderBy('nom')->get();

        return view('suivi-factures.index', compact('factures', 'clients'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $clients = ClientFacture::orderBy('nom')->get();
        return view('suivi-factures.create', compact('clients'));
    }

    /**
     * Enregistrer une nouvelle facture
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_facture' => 'required|string|max:50|unique:suivi_factures',
            //'date_livraison' => 'required|date',
            'montant' => 'required|numeric|min:0',
            'client_id' => 'required|exists:clients,id',
            'date_facture' =>  'required|date',
        ]);

        try {
            SuiviFacture::create($validated);

            return redirect()->route('suivi-factures.index')
                           ->with('success', 'Facture enregistrée avec succès!');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de l\'enregistrement: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Afficher les détails d'une facture
     */
    public function show(SuiviFacture $suiviFacture)
    {
        $suiviFacture->load('client');
        return view('suivi-factures.show', compact('suiviFacture'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(SuiviFacture $suiviFacture)
    {
        $clients = ClientFacture::orderBy('nom')->get();
        return view('suivi-factures.edit', compact('suiviFacture', 'clients'));
    }

    /**
     * Mettre à jour une facture
     */
    public function update(Request $request, SuiviFacture $suiviFacture)
    {
        $validated = $request->validate([
            'numero_facture' => 'required|string|max:50|unique:suivi_factures,numero_facture,' . $suiviFacture->id,
            //'date_livraison' => 'required|date',
            'montant' => 'required|numeric|min:0',
            'client_id' => 'required|exists:clients,id',
            'date_facture' =>  'required|date',
        ]);

        try {
            $suiviFacture->update($validated);

            return redirect()->route('suivi-factures.show', $suiviFacture->id)
                           ->with('success', 'Facture mise à jour avec succès!');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de la mise à jour: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Supprimer une facture
     */
    public function destroy(SuiviFacture $suiviFacture)
    {
        try {
            $suiviFacture->delete();

            return redirect()->route('suivi-factures.index')
                           ->with('success', 'Facture supprimée avec succès!');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de la suppression: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Statistiques des factures
     */
    public function statistics()
    {
        $stats = [
            'total_factures' => SuiviFacture::count(),
            'montant_total' => SuiviFacture::sum('montant'),
            'moyenne_montant' => SuiviFacture::avg('montant'),
            'factures_par_mois' => SuiviFacture::selectRaw('
                    YEAR(date_livraison) as annee,
                    MONTH(date_livraison) as mois,
                    COUNT(*) as nombre_factures,
                    SUM(montant) as montant_total
                ')
                ->where('date_livraison', '>=', now()->subMonths(12))
                ->groupBy('annee', 'mois')
                ->orderBy('annee', 'desc')
                ->orderBy('mois', 'desc')
                ->get(),
            'top_clients' => SuiviFacture::with('client')
                ->selectRaw('client_id, COUNT(*) as nombre_factures, SUM(montant) as montant_total')
                ->groupBy('client_id')
                ->orderByDesc('montant_total')
                ->limit(10)
                ->get()
        ];

        return view('suivi-factures.statistics', compact('stats'));
    }

    /**
     * Factures par client
     */
    public function byClient(ClientFacture $client)
    {
        $factures = $client->factures()
                          ->orderBy('date_livraison', 'desc')
                          ->paginate(20);

        return view('suivi-factures.by-client', compact('client', 'factures'));
    }

     public function updateEtat(Request $request, SuiviFacture $suiviFacture)
    {
        $request->validate([
            'etat' => 'required|in:livré,non livré',
            'date_livraison' => 'nullable|date|required_if:etat,livré'
        ]);

        try {
            $suiviFacture->update([
                'etat' => $request->etat,
                'date_livraison' => $request->etat === 'livré' ? $request->date_livraison : null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'État de livraison mis à jour avec succès',
                'facture' => [
                    'id' => $suiviFacture->id,
                    'etat' => $suiviFacture->etat,
                    'date_livraison' => $suiviFacture->date_livraison ? $suiviFacture->date_livraison->format('d/m/Y') : null,
                    'etat_badge' => $suiviFacture->etat === 'livré' ? 'success' : 'warning'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marquer comme livré (action rapide)
     */
    public function marquerLivre(SuiviFacture $suiviFacture)
    {
        try {
            $suiviFacture->marquerCommeLivre();

            return back()->with('success', 'Facture marquée comme livrée avec succès!');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Marquer comme non livré (action rapide)
     */
    public function marquerNonLivre(SuiviFacture $suiviFacture)
    {
        try {
            $suiviFacture->marquerCommeNonLivre();

            return back()->with('success', 'Facture marquée comme non livrée avec succès!');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ]);
        }
    }

}
