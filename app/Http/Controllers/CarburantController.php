<?php

namespace App\Http\Controllers;

use App\Models\Carburant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarburantController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $carburants = Carburant::paginate(20);;

        return view('carburants.index', compact('carburants'));
    }

    public function create()
    {
        return view('carburants.create');
    }

    public function store(Request $request)
    {
       $validated = $request->validate([
            'libelle' => 'required|max:200',
            'montant' => 'required|integer|min:0',

        ]);

        try {
            DB::beginTransaction();

            Carburant::create($validated);

            DB::commit();

            return redirect()->route('carburants.index')
                ->with('success', 'Véhicule ajouté avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erreur lors de la création du véhicule: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Carburant $carburant)
    {

    }

    public function edit(Carburant $carburant)
    {
        return view('carburants.edit', compact('carburant'));
    }

    public function update(Request $request, Carburant $carburant)
    {
        $validated = $request->validate([
            'libelle' => 'required|max:200',
            'montant' => 'required|integer|min:0',

        ]);

        try {
            DB::beginTransaction();

            $carburant->update($validated);

            DB::commit();

            return redirect()->route('carburants.index', $carburant)
                ->with('success', 'Véhicule modifié avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erreur lors de la modification du véhicule: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Carburant $carburant)
    {
        try {
            DB::beginTransaction();

            // Vérifier s'il y a des données associées (à compléter plus tard)
            if ($carburant->fuelEntries()->count() > 0 || $carburant->repairLogs()->count() > 0) {
                return redirect()->back()
                    ->with('warning', 'Impossible de supprimer ce véhicule car il possède des données associées.');
            }

            $carburant->delete();

            DB::commit();

            return redirect()->route('carburants.index')
                ->with('success', 'Véhicule supprimé avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du véhicule: ' . $e->getMessage());
        }
    }
}
