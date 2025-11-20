<?php

// app/Http/Controllers/PaiementController.php
namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\SuiviFacture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaiementController extends Controller
{
    /**
     * Afficher la liste des paiements
     */
    public function index(Request $request)
    {
        $query = Paiement::with('facture.client');

        // Filtres
        if ($request->has('facture_id') && $request->facture_id) {
            $query->where('suivi_facture_id', $request->facture_id);
        }

        if ($request->has('date_debut') && $request->date_debut) {
            $query->where('date_paiement', '>=', $request->date_debut);
        }

        if ($request->has('date_fin') && $request->date_fin) {
            $query->where('date_paiement', '<=', $request->date_fin);
        }

        if ($request->has('mode_paiement') && $request->mode_paiement) {
            $query->where('mode_paiement', $request->mode_paiement);
        }

        $paiements = $query->orderBy('date_paiement', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(20);

        $factures = SuiviFacture::with('client')->get();
        $modesPaiement = ['virement', 'cheque', 'espece', 'carte'];

        return view('paiements.index', compact('paiements', 'factures', 'modesPaiement'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create(Request $request)
    {
        $facture = null;

        if ($request->has('facture_id')) {
            $facture = SuiviFacture::with('client', 'paiements')->findOrFail($request->facture_id);
        }

        $factures = SuiviFacture::with('client')
                               ->whereDoesntHave('paiements', function($q) {
                                   $q->selectRaw('suivi_facture_id, SUM(montant) as total_paye')
                                     ->groupBy('suivi_facture_id')
                                     ->havingRaw('total_paye >= suivi_factures.montant');
                               })
                               ->orWhereDoesntHave('paiements')
                               ->get();

        $modesPaiement = [
            'virement' => 'Virement',
            'cheque' => 'Chèque',
            'espece' => 'Espèces',
            'carte' => 'Carte bancaire'
        ];

        return view('paiements.create', compact('factures', 'facture', 'modesPaiement'));
    }

    /**
     * Enregistrer un nouveau paiement
     */
   /* public function store(Request $request)
    {
        $validated = $request->validate([
            'suivi_facture_id' => 'required|exists:suivi_factures,id',
            'montant' => 'required|numeric|min:0.01',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|in:virement,cheque,espece,carte',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            // Vérifier que le montant ne dépasse pas le montant restant
            $facture = SuiviFacture::with('paiements')->findOrFail($validated['suivi_facture_id']);
            $montantRestant = $facture->montant - $facture->paiements->sum('montant');

            if ($validated['montant'] > $montantRestant) {
                return back()->withErrors([
                    'montant' => "Le montant du paiement (".number_format($validated['montant'], 2, ',', ' ')." €) dépasse le montant restant (".number_format($montantRestant, 2, ',', ' ')." €)"
                ])->withInput();
            }

            // Déterminer le statut
            $statut = ($validated['montant'] < $montantRestant) ? 'partiel' : 'complet';

            $paiement = Paiement::create($validated + ['statut' => $statut]);

            DB::commit();

            return redirect()->route('paiements.show', $paiement->id)
                           ->with('success', 'Paiement enregistré avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de l\'enregistrement: ' . $e->getMessage()
            ])->withInput();
        }
    }*/

    // Dans PaiementController - Modifier la méthode store pour retourner JSON
public function store(Request $request)
{
    $validated = $request->validate([
        'suivi_facture_id' => 'required|exists:suivi_factures,id',
        'montant' => 'required|numeric|min:0.01',
        'date_paiement' => 'required|date',
        'mode_paiement' => 'required|in:virement,cheque,espece,carte',
        'reference' => 'nullable|string|max:100',
        'notes' => 'nullable|string|max:500'
    ]);

    DB::beginTransaction();

    try {
        // Vérifier que le montant ne dépasse pas le montant restant
        $facture = SuiviFacture::with('paiements')->findOrFail($validated['suivi_facture_id']);
        $montantRestant = $facture->montant - $facture->paiements->sum('montant');

        if ($validated['montant'] > $montantRestant) {
            return response()->json([
                'success' => false,
                'message' => "Le montant du paiement (".number_format($validated['montant'], 2, ',', ' ')." €) dépasse le montant restant (".number_format($montantRestant, 2, ',', ' ')." €)"
            ], 422);
        }

        // Déterminer le statut
        $statut = ($validated['montant'] < $montantRestant) ? 'partiel' : 'complet';

        $paiement = Paiement::create($validated + ['statut' => $statut]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Paiement enregistré avec succès!',
            'paiement_id' => $paiement->id
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue lors de l\'enregistrement: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Afficher les détails d'un paiement
     */
    public function show(Paiement $paiement)
    {
        $paiement->load('facture.client', 'facture.paiements');
        return view('paiements.show', compact('paiement'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Paiement $paiement)
    {
        $paiement->load('facture.client');
        $modesPaiement = [
            'virement' => 'Virement',
            'cheque' => 'Chèque',
            'espece' => 'Espèces',
            'carte' => 'Carte bancaire'
        ];

        return view('paiements.edit', compact('paiement', 'modesPaiement'));
    }

    /**
     * Mettre à jour un paiement
     */
    public function update(Request $request, Paiement $paiement)
    {
        $validated = $request->validate([
            'montant' => 'required|numeric|min:0.01',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|in:virement,cheque,espece,carte',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            // Vérifier que le nouveau montant ne dépasse pas le montant restant (hors ce paiement)
            $facture = $paiement->facture;
            $autresPaiements = $facture->paiements()->where('id', '!=', $paiement->id)->sum('montant');
            $montantRestant = $facture->montant - $autresPaiements;

            if ($validated['montant'] > $montantRestant) {
                return back()->withErrors([
                    'montant' => "Le montant du paiement (".number_format($validated['montant'], 2, ',', ' ')." €) dépasse le montant restant (".number_format($montantRestant, 2, ',', ' ')." €)"
                ])->withInput();
            }

            // Déterminer le statut
            $statut = ($validated['montant'] < $montantRestant) ? 'partiel' : 'complet';

            $paiement->update($validated + ['statut' => $statut]);

            DB::commit();

            return redirect()->route('paiements.show', $paiement->id)
                           ->with('success', 'Paiement mis à jour avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de la mise à jour: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Supprimer un paiement
     */
    public function destroy(Paiement $paiement)
    {
        DB::beginTransaction();

        try {
            $paiement->delete();

            DB::commit();

            return redirect()->route('paiements.index')
                           ->with('success', 'Paiement supprimé avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de la suppression: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Paiements par facture
     */
    public function byFacture(SuiviFacture $suiviFacture)
    {
        $paiements = $suiviFacture->paiements()
                                 ->orderBy('date_paiement', 'desc')
                                 ->paginate(20);

        return view('paiements.by-facture', compact('suiviFacture', 'paiements'));
    }

    /**
     * Statistiques des paiements
     */
    public function statistics()
    {
        $stats = [
            'total_paiements' => Paiement::count(),
            'montant_total' => Paiement::sum('montant'),
            'paiements_par_mois' => Paiement::selectRaw('
                    YEAR(date_paiement) as annee,
                    MONTH(date_paiement) as mois,
                    COUNT(*) as nombre_paiements,
                    SUM(montant) as montant_total
                ')
                ->where('date_paiement', '>=', now()->subMonths(12))
                ->groupBy('annee', 'mois')
                ->orderBy('annee', 'desc')
                ->orderBy('mois', 'desc')
                ->get(),
            'paiements_par_mode' => Paiement::selectRaw('
                    mode_paiement,
                    COUNT(*) as nombre_paiements,
                    SUM(montant) as montant_total
                ')
                ->groupBy('mode_paiement')
                ->get(),
            'factures_impayees' => SuiviFacture::whereHas('paiements', function($q) {
                $q->selectRaw('suivi_facture_id, SUM(montant) as total_paye')
                  ->groupBy('suivi_facture_id')
                  ->havingRaw('total_paye < suivi_factures.montant');
            })->orWhereDoesntHave('paiements')->count()
        ];

        return view('paiements.statistics', compact('stats'));
    }
}
