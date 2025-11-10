<?php

namespace App\Http\Controllers;

use App\Models\RepairLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RepairLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Récupérer tous les véhicules pour le filtre
        $vehicles = Vehicle::orderBy('immatriculation')->get();

        // Commencer la requête avec les relations

        $query = RepairLog::with('vehicle');


        // Filtre par statut
        if ($request->has('categorie') && $request->categorie != '') {
           $query->whereHas('vehicle', function($q) use ($request) {
            $q->where('categorie', $request->categorie);
        });
        }


        // Filtre par type d'intervention
        if ($request->has('type') && $request->type != '') {
            $query->where('type_intervention', $request->type);
        }

        // Filtre par véhicule
        if ($request->has('vehicle') && $request->vehicle != '') {
            $query->where('vehicle_id', $request->vehicle);
        }

        // Filtre par période (date de début)
        if ($request->has('date_debut') && $request->date_debut != '') {
            $query->whereDate('date_intervention', '>=', $request->date_debut);
        }

        // Filtre par période (date de fin)
        if ($request->has('date_fin') && $request->date_fin != '') {
            $query->whereDate('date_intervention', '<=', $request->date_fin);
        }

        // Appliquer les filtres pour les statistiques aussi
        $statsQuery = clone $query;

        $stats = [
            'totalInterventions' => $statsQuery->count(),
            'interventionsMois' => $statsQuery->when(!$request->has('date_debut') && !$request->has('date_fin'), function($q) {
                return $q->thisMonth();
            })->count(),
            'coutTotalMois' => $statsQuery->when(!$request->has('date_debut') && !$request->has('date_fin'), function($q) {
                return $q->thisMonth();
            })->sum('cout_total'),
            'enCours' => RepairLog::where('statut', 'en_cours')->count()
        ];

        // Pagination avec conservation des paramètres de filtre
        $repairLogs = $query->orderBy('date_intervention', 'desc')
                            ->paginate(20)
                            ->appends($request->except('page'));

       // dd($repairLogs);

        return view('repair-logs.index', compact('repairLogs', 'stats', 'vehicles'));
    }

    public function create()
    {
        $vehicles = Vehicle::where('etat', '!=', 'hors_service')->get();
        $thisVehicle = null;
        if (isset($_GET['vehicle_id'])) {
            $thisVehicle = Vehicle::find($_GET['vehicle_id']);

        }
        return view('repair-logs.create', compact('vehicles','thisVehicle'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'date_intervention' => 'required|date|before_or_equal:today',
            'type_intervention' => 'required',
            'description' => 'required|string|max:255',
            'details_travaux' => 'nullable|string',
            'cout_main_oeuvre' => 'required|numeric|min:0',
            'cout_pieces' => 'required|numeric|min:0',
            'kilometrage_vehicule' => 'required|integer|min:0',
            'garage' => 'nullable|string|max:100',
            'technicien' => 'nullable|string|max:100',
            'statut' => 'required|in:planifie,en_cours,termine,annule',
            'date_prochaine_revision' => 'nullable|date|after:date_intervention',
            'prochain_kilometrage' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'facture' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        // Calcul automatique du coût total
        $validated['cout_total'] = RepairLog::calculateTotalCost(
            $validated['cout_main_oeuvre'],
            $validated['cout_pieces']
        );

        try {
            DB::beginTransaction();

            // Gestion de l'upload de facture
            if ($request->hasFile('facture')) {
                $file = $request->file('facture');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('factures', $fileName, 'public');
                $validated['facture'] = $filePath;
            }

            $repairLog = RepairLog::create($validated);

            // Mettre à jour le kilométrage du véhicule si nécessaire
            $vehicle = Vehicle::find($validated['vehicle_id']);
            if ($validated['kilometrage_vehicule'] > $vehicle->kilometrage_actuel) {
                $vehicle->update(['kilometrage_actuel' => $validated['kilometrage_vehicule']]);
            }

            // Mettre à jour l'état du véhicule si l'intervention est en cours
            if ($validated['statut'] === 'en_cours') {
                $vehicle->update(['etat' => 'en_entretien']);
            }

            DB::commit();

            return redirect()->route('repair-logs.show', $repairLog)
                ->with('success', 'Intervention enregistrée avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(RepairLog $repairLog)
    {
        $repairLog->load('vehicle');
        $historiqueVehicule = RepairLog::where('vehicle_id', $repairLog->vehicle_id)
            ->where('id', '!=', $repairLog->id)
            ->orderBy('date_intervention', 'desc')
            ->take(5)
            ->get();

        return view('repair-logs.show', compact('repairLog', 'historiqueVehicule'));
    }

    public function edit(RepairLog $repairLog)
    {
        $vehicles = Vehicle::all();
        return view('repair-logs.edit', compact('repairLog', 'vehicles'));
    }

    public function update(Request $request, RepairLog $repairLog)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'date_intervention' => 'required|date|before_or_equal:today',
            'type_intervention' => 'required',
            'description' => 'required|string|max:255',
            'details_travaux' => 'nullable|string',
            'cout_main_oeuvre' => 'required|numeric|min:0',
            'cout_pieces' => 'required|numeric|min:0',
            'kilometrage_vehicule' => 'required|integer|min:0',
            'garage' => 'nullable|string|max:100',
            'technicien' => 'nullable|string|max:100',
            'statut' => 'required|in:planifie,en_cours,termine,annule',
            'date_prochaine_revision' => 'nullable|date|after:date_intervention',
            'prochain_kilometrage' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'facture' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $validated['cout_total'] = RepairLog::calculateTotalCost(
            $validated['cout_main_oeuvre'],
            $validated['cout_pieces']
        );

        try {
            DB::beginTransaction();

            // Gestion de l'upload de facture
            if ($request->hasFile('facture')) {
                // Supprimer l'ancienne facture si elle existe
                if ($repairLog->facture) {
                    Storage::disk('public')->delete($repairLog->facture);
                }

                $file = $request->file('facture');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('factures', $fileName, 'public');
                $validated['facture'] = $filePath;
            }

            $ancienStatut = $repairLog->statut;
            $repairLog->update($validated);

            // Mettre à jour le kilométrage du véhicule
            $vehicle = Vehicle::find($validated['vehicle_id']);
            $maxKilometrage = RepairLog::where('vehicle_id', $vehicle->id)->max('kilometrage_vehicule');
            $vehicle->update(['kilometrage_actuel' => $maxKilometrage]);

            // Gérer l'état du véhicule selon le statut
            if ($validated['statut'] === 'en_cours') {
                $vehicle->update(['etat' => 'en_entretien']);
            } elseif ($ancienStatut === 'en_cours' && $validated['statut'] !== 'en_cours') {
                // Si l'intervention n'est plus en cours, vérifier s'il y a d'autres interventions en cours
                $autresInterventionsEnCours = RepairLog::where('vehicle_id', $vehicle->id)
                    ->where('statut', 'en_cours')
                    ->where('id', '!=', $repairLog->id)
                    ->exists();

                if (!$autresInterventionsEnCours) {
                    $vehicle->update(['etat' => 'disponible']);
                }
            }

            DB::commit();

            return redirect()->route('repair-logs.show', $repairLog)
                ->with('success', 'Intervention modifiée avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erreur lors de la modification: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(RepairLog $repairLog)
    {
        try {
            DB::beginTransaction();

            $vehicleId = $repairLog->vehicle_id;

            // Supprimer la facture si elle existe
            if ($repairLog->facture) {
                Storage::disk('public')->delete($repairLog->facture);
            }

            $repairLog->delete();

            // Recalculer le kilométrage actuel du véhicule
            $maxKilometrage = RepairLog::where('vehicle_id', $vehicleId)->max('kilometrage_vehicule');
            $vehicle = Vehicle::find($vehicleId);
            $vehicle->update(['kilometrage_actuel' => $maxKilometrage ?? $vehicle->kilometrage_actuel]);

            DB::commit();

            return redirect()->route('repair-logs.index')
                ->with('success', 'Intervention supprimée avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    public function downloadFacture(RepairLog $repairLog)
    {
        if (!$repairLog->facture) {
            return redirect()->back()->with('error', 'Aucune facture disponible.');
        }

        if (!Storage::disk('public')->exists($repairLog->facture)) {
            return redirect()->back()->with('error', 'Le fichier facture n\'existe pas.');
        }

        return Storage::disk('public')->download($repairLog->facture);
    }

    // API pour les statistiques
    public function apiStats()
    {
        $stats = [
            'totalCout' => RepairLog::sum('cout_total'),
            'moyenneMensuelle' => RepairLog::thisMonth()->avg('cout_total'),
            'parType' => RepairLog::select('type_intervention', DB::raw('COUNT(*) as count'))
                ->groupBy('type_intervention')
                ->get(),
            'dernieresInterventions' => RepairLog::with('vehicle')
                ->orderBy('date_intervention', 'desc')
                ->take(5)
                ->get()
        ];

        return response()->json($stats);
    }
}
