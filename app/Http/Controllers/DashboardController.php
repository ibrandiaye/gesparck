<?php

namespace App\Http\Controllers;

use App\Models\RepairLog;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\FuelEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // ... le haut reste inchangé

    public function index()
    {
        $stats = [
            'totalVehicles' => Vehicle::count(),
            'vehiclesDisponibles' => Vehicle::where('etat', 'disponible')->count(),
            'vehiclesEnEntretien' => Vehicle::where('etat', 'en_entretien')->count(),
            'coutCarburantMois' => FuelEntry::whereYear('date_remplissage', now()->year)
                ->whereMonth('date_remplissage', now()->month)
                ->sum('cout_total') ?? 0,
            'remplissagesMois' => FuelEntry::whereYear('date_remplissage', now()->year)
                ->whereMonth('date_remplissage', now()->month)
                ->count() ?? 0,
                // Nouvelles statistiques dépannages
                'interventionsMois' => RepairLog::whereYear('date_intervention', now()->year)
                    ->whereMonth('date_intervention', now()->month)
                    ->count(),
                'coutEntretienMois' => RepairLog::whereYear('date_intervention', now()->year)
                    ->whereMonth('date_intervention', now()->month)
                    ->sum('cout_total'),
                'interventionsEnCours' => RepairLog::where('statut', 'en_cours')->count(),
                'interventionsPlanifiees' => RepairLog::where('statut', 'planifie')->count()
        ];

        $vehicles = Vehicle::with(['fuelEntries' => function($query) {
            $query->orderBy('date_remplissage', 'desc');
        }])->get();

        $recentVehicles = Vehicle::latest()->take(5)->get();
        $recentFuelEntries = FuelEntry::with(['vehicle' => function($q){
            $q->withDefault([
                'immatriculation' => 'Inconnu',
                'marque' => '',
                'modele' => ''
            ]);
        }])
            ->orderBy('date_remplissage', 'desc')
            ->take(5)
            ->get();

            // Nouvelles données pour les dépannages
            $recentRepairLogs = RepairLog::with('vehicle')
                ->orderBy('date_intervention', 'desc')
                ->take(5)
                ->get();

            $interventionsParType = $this->getInterventionsParType();
            $coutMensuelEntretien = $this->getCoutMensuelEntretien();

        return view('dashboard', compact(
        'stats',
                'vehicles',
                'recentVehicles',
                'recentFuelEntries',
                'recentRepairLogs',
                'interventionsParType',
                'coutMensuelEntretien'
        ));
    }

    public function monDasboard()
    {
      //  dd("ok");
        $vehicle_id = null;
        $date_debut = null;
        $date_fin = null;
        $vehicles = DB::table('vehicles')->get();
        $nbVehicules = DB::table('vehicles')->count();
        $nbEntretiens = DB::table('repair_logs')->whereMonth('date_intervention', now()->month)->count();
        $nbCarburant = DB::table('fuel_entries')->whereMonth('date_remplissage', now()->month)->count();
        $nbLitre = DB::table('fuel_entries')->whereMonth('date_remplissage', now()->month)->sum("litres");
        $nbTrajets = DB::table("trips")->whereMonth('date_trajet', now()->month)->sum("nombre_trajets");
        $montantEntretien = DB::table('repair_logs')->whereMonth('date_intervention', now()->month)->sum("cout_total");
        $montantCarburant = DB::table('fuel_entries')->whereMonth('date_remplissage', now()->month)->sum("cout_total");
        $repairLogs = RepairLog::with("vehicle")->whereMonth('date_intervention', now()->month)->get();
        $fuelEntries  = FuelEntry::whereMonth('date_remplissage', now()->month)->get();
        $trips = Trip::whereMonth("date_trajet",now()->month)->get();
        //dd($fuelEntries);
        foreach ($vehicles as $key => $vehicle) {
            $montantRepairLog = 0;
            $montantFuelEntry = 0;
            $nbTrajet = 0;
            foreach ($repairLogs as $key1 => $repairLog) {

                if($vehicle->id==$repairLog->vehicle_id)
                {
                    $montantRepairLog += $repairLog->cout_total;
                }
            }
            foreach ($fuelEntries as $key2 => $fuelEntrie) {
                if($vehicle->id==$fuelEntrie->vehicle_id)
                {
                    $montantFuelEntry +=  $fuelEntrie->cout_total;
                    //$nbTrajet += $fuelEntrie->nombreTotalTrajets;
                }

            }
            foreach ($trips as $key2 => $trip) {
                if($vehicle->id==$trip->vehicle_id)
                {
                    $nbTrajet += $fuelEntrie->nombre_trajets ?? 0;
                }

            }
            $vehicles[$key]->montantRepairLog = $montantRepairLog;
            $vehicles[$key]->montantFuelEntry = $montantFuelEntry;
            $vehicles[$key]->nbTrajet = $nbTrajet;
        }
        return view('mondashboard', compact('vehicles', 'nbVehicules', 'nbEntretiens', 'nbCarburant', 'nbLitre', 'nbTrajets',
        'montantEntretien', 'montantCarburant','vehicle_id','date_debut','date_fin','repairLogs','fuelEntries'));
    }
     public function monDasboardFiltre(Request $request)
    {
      //  dd("ok");
        $vehicle_id = null;
        $date_debut = null;
        $date_fin = null;
        $vehicles = DB::table('vehicles')->get();
        $nbVehicules = DB::table('vehicles')->count();

        $querynbEntretiens = DB::table('repair_logs');
        $querynbCarburant = DB::table('fuel_entries');
        $querynbLitre = DB::table('fuel_entries');
        $querynbTrajets = DB::table("trips");
        $querymontantEntretien = DB::table('repair_logs');
        $querymontantCarburant = DB::table('fuel_entries');

        $querylisteEntrentiens = RepairLog::with(["vehicle"]);
        $querylistesTrips  = FuelEntry::with(["trips",'vehicle']);
        $queryTrajets = DB::table("trips");

        if (isset($request->date_debut) && isset($request->date_fin)) {
            $dateDebut = Carbon::parse($request->input('date_debut'));
            $dateFin = Carbon::parse($request->input('date_fin'));
            //dd($dateDebut,$dateFin );

            $date_debut = $request->input('date_debut');
            $date_fin = $request->input('date_fin');

            $querynbEntretiens->whereBetween('date_intervention', [$dateDebut, $dateFin]);
            $querynbCarburant->whereBetween('date_remplissage', [$dateDebut, $dateFin]);
            $querynbLitre->whereBetween('date_remplissage', [$dateDebut, $dateFin]);
            $querynbTrajets->whereBetween('date_trajet', [$dateDebut, $dateFin]);
            $querymontantEntretien->whereBetween('date_intervention', [$dateDebut, $dateFin]);
            $querymontantCarburant->whereBetween('date_remplissage', [$dateDebut, $dateFin]);
            $querylisteEntrentiens->whereBetween('date_intervention', [$dateDebut, $dateFin]);
            $querylistesTrips->whereBetween('date_remplissage', [$dateDebut, $dateFin]);
            $queryTrajets->whereBetween('date_trajet', [$dateDebut, $dateFin]);
        }
        if(isset($request->vehicle_id))
        {
            $vehicle_id = $request->vehicle_id;
            $querynbEntretiens->where('vehicle_id',$request->vehicle_id);
            $querynbCarburant->where('vehicle_id',$request->vehicle_id);
            $querynbLitre->where('vehicle_id',$request->vehicle_id);
            $querynbTrajets->where('vehicle_id',$request->vehicle_id);
            $querymontantEntretien->where('vehicle_id',$request->vehicle_id);
            $querymontantCarburant->where('vehicle_id',$request->vehicle_id);
            $querylisteEntrentiens->where('vehicle_id', $request->vehicle_id);
            $querylistesTrips->where('vehicle_id', $request->vehicle_id);
            $queryTrajets->where('vehicle_id', $request->vehicle_id);
        }

        $nbEntretiens = $querynbEntretiens->count();
        $nbCarburant =  $querynbCarburant->count();
        $nbLitre = $querynbLitre->sum("litres");
        $nbTrajets = $querynbTrajets->sum("nombre_trajets");
        $montantEntretien =$querymontantEntretien->sum("cout_total");
        $montantCarburant = $querymontantCarburant->sum("cout_total");
        $repairLogs = $querylisteEntrentiens->get();
        $fuelEntries  = $querylistesTrips->get();
        $trips   = $queryTrajets->get();
       //dd($repairLogs,$listesCarburants);
       foreach ($vehicles as $key => $vehicle) {
            $montantRepairLog = 0;
            $montantFuelEntry = 0;
            $nbTrajet = 0;
            foreach ($repairLogs as $key1 => $repairLog) {

                if($vehicle->id==$repairLog->vehicle_id)
                {
                    $montantRepairLog += $repairLog->cout_total;
                }
            }
            foreach ($fuelEntries as $key2 => $fuelEntrie) {
                if($vehicle->id==$fuelEntrie->vehicle_id)
                {
                    $montantFuelEntry +=  $fuelEntrie->cout_total;
                }

            }
            foreach ($trips as $key3 => $trip) {
                if($vehicle->id==$trip->vehicle_id)
                {
                    $nbTrajet += $trip->nombre_trajets;
                }

            }

            $vehicles[$key]->montantRepairLog = $montantRepairLog;
            $vehicles[$key]->montantFuelEntry = $montantFuelEntry;
            $vehicles[$key]->nbTrajet = $nbTrajet;
        }


        return view('mondashboard', compact('vehicles', 'nbVehicules', 'nbEntretiens', 'nbCarburant', 'nbLitre', 'nbTrajets',
        'montantEntretien', 'montantCarburant','date_debut','date_fin','vehicle_id','repairLogs','fuelEntries'));
    }

   /*  private function calculateStatistics($consumptionData, $monthlyCostData)
    {
        $stats = [];

        // Consommation
        if (!empty($consumptionData['values'])) {
            $stats['avgConsumption'] = round(array_sum($consumptionData['values']) / count($consumptionData['values']), 2);
            $stats['minConsumption'] = round(min($consumptionData['values']), 2);
            $stats['maxConsumption'] = round(max($consumptionData['values']), 2);
        } else {
            $stats['avgConsumption'] = $stats['minConsumption'] = $stats['maxConsumption'] = null;
        }

        // Coût
        if (!empty($monthlyCostData['values'])) {
            $stats['totalCost'] = array_sum($monthlyCostData['values']);
            $stats['avgMonthlyCost'] = round($stats['totalCost'] / max(count($monthlyCostData['values']), 1));

            if ($stats['avgConsumption'] > 0) {
                $reductionPotentielle = $stats['avgConsumption'] * 0.1;
                $stats['potentialSavings'] = round(($reductionPotentielle / $stats['avgConsumption']) * $stats['totalCost'] * 0.3);
            } else {
                $stats['potentialSavings'] = null;
            }
        } else {
            $stats['totalCost'] = $stats['avgMonthlyCost'] = $stats['potentialSavings'] = null;
        }

        return $stats;
    } */

   /* public function getChartData(Request $request)
    {
        try {
            $filters = $request->validate([
                'timeRange' => 'sometimes|in:3,6,12,all',
                'vehicleFilter' => 'sometimes',
                'chartType' => 'sometimes|in:line,bar'
            ]);

            $consumptionData = $this->getFilteredConsumptionData($filters);
            $monthlyCostData = $this->getFilteredMonthlyCostData($filters);
            $statistics = $this->calculateStatistics($consumptionData, $monthlyCostData);

            return response()->json([
                'success' => true,
                'consumption' => $consumptionData,
                'monthlyCost' => $monthlyCostData,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des données',
                'error' => $e->getMessage()
            ], 500);
        }
    }
*/
    public function getChartData(Request $request)
    {
        try {
            $filters = $request->validate([
                'timeRange' => 'sometimes|in:3,6,12,all',
                'vehicleFilter' => 'sometimes',
                'chartType' => 'sometimes|in:line,bar'
            ]);

            $consumptionData = $this->getFilteredConsumptionData($filters);
            $monthlyCostData = $this->getFilteredMonthlyCostData($filters);
            $repairStatsData = $this->getRepairStatsData($filters);
            $statistics = $this->calculateStatistics($consumptionData, $monthlyCostData, $repairStatsData);

            return response()->json([
                'success' => true,
                'consumption' => $consumptionData,
                'monthlyCost' => $monthlyCostData,
                'repairStats' => $repairStatsData,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des données',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getVehicleDetailedChart($vehicleId)
    {
        try {
            $vehicle = Vehicle::findOrFail($vehicleId);
            $entries = $vehicle->fuelEntries()
                ->orderBy('date_remplissage')
                ->get();

            $data = [
                'dates' => [],
                'consumption' => [],
                'prices' => [],
                'costs' => [],
                'kilometrage' => []
            ];

            $previousEntry = null;
            foreach ($entries as $entry) {
                $data['dates'][] = $entry->date_remplissage->format('M Y');
                $data['prices'][] = (float) $entry->prix_litre;
                $data['costs'][] = (float) $entry->cout_total;
                $data['kilometrage'][] = $entry->kilometrage;

                // Calcul de la consommation
                if ($previousEntry) {
                    $kmParcourus = $entry->kilometrage - $previousEntry->kilometrage;
                    if ($kmParcourus > 0) {
                        $consommation = ($entry->litres / $kmParcourus) * 100;
                        $data['consumption'][] = round($consommation, 2);
                    } else {
                        $data['consumption'][] = null;
                    }
                } else {
                    $data['consumption'][] = null;
                }
                $previousEntry = $entry;
            }

            // Supprimer le premier élément de consommation (toujours null)
            if (count($data['consumption']) > 0) {
                array_shift($data['consumption']);
                array_shift($data['dates']);
                array_shift($data['prices']);
                array_shift($data['costs']);
                array_shift($data['kilometrage']);
            }

            return response()->json([
                'success' => true,
                'vehicle' => $vehicle->immatriculation,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des données du véhicule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getFilteredConsumptionData($filters)
    {
        $query = Vehicle::query();

        if (isset($filters['vehicleFilter']) && $filters['vehicleFilter'] !== 'all') {
            $query->where('id', $filters['vehicleFilter']);
        }

        $vehicles = $query->with(['fuelEntries' => function($q) use ($filters) {
            if (isset($filters['timeRange']) && $filters['timeRange'] !== 'all') {
                $q->where('date_remplissage', '>=', now()->subMonths($filters['timeRange']));
            }
            $q->orderBy('date_remplissage');
        }])->get();

        $labels = [];
        $values = [];
        $vehicleIds = [];

        foreach ($vehicles as $vehicle) {
            $consommationMoyenne = $this->calculateVehicleConsumption($vehicle);
            if ($consommationMoyenne !== null) {
                $labels[] = $vehicle->immatriculation;
                $values[] = $consommationMoyenne;
                $vehicleIds[] = $vehicle->id;
            }
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'vehicleIds' => $vehicleIds
        ];
    }

    private function getFilteredMonthlyCostData($filters)
{
    try {
        $query = FuelEntry::query();

        // Filtre période
        if (!empty($filters['timeRange']) && $filters['timeRange'] !== 'all') {
            $months = (int) $filters['timeRange'];
            $startDate = now()->subMonths($months)->startOfMonth();
            $query->where('date_remplissage', '>=', $startDate);
        }

        // Filtre véhicule
        if (!empty($filters['vehicleFilter']) && $filters['vehicleFilter'] !== 'all') {
            $query->where('vehicle_id', $filters['vehicleFilter']);
        }

        $monthlyCosts = $query->select(
                DB::raw('YEAR(date_remplissage) as year'),
                DB::raw('MONTH(date_remplissage) as month'),
                DB::raw('SUM(cout_total) as total_cost')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->keyBy(function ($item) {
                return sprintf('%04d-%02d', $item->year, $item->month);
            });

        // Générer les 12 derniers mois même si vides
        $labels = [];
        $values = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $labels[] = $date->format('M Y');
            $values[] = isset($monthlyCosts[$key]) ? (float) $monthlyCosts[$key]->total_cost : 0;
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];

    } catch (\Exception $e) {
        \Log::error('Erreur getFilteredMonthlyCostData: ' . $e->getMessage());
        return [
            'labels' => ['Erreur'],
            'values' => [0]
        ];
    }
}


    private function calculateVehicleConsumption($vehicle)
    {
        $entries = $vehicle->fuelEntries->sortBy('date_remplissage');

        if ($entries->count() < 2) {
            return null;
        }

        $totalConsommation = 0;
        $validCalculations = 0;

        for ($i = 1; $i < $entries->count(); $i++) {
            $current = $entries[$i];
            $previous = $entries[$i - 1];

            $kmParcourus = $current->kilometrage - $previous->kilometrage;
            if ($kmParcourus > 0) {
                $consommation = ($current->litres / $kmParcourus) * 100;
                $totalConsommation += $consommation;
                $validCalculations++;
            }
        }

        return $validCalculations > 0 ? round($totalConsommation / $validCalculations, 2) : null;
    }



    // Méthode pour les données de démonstration (fallback)
  /*  private function getDemoData()
    {
        return [
            'consumption' => [
                'labels' => ['Véhicule A', 'Véhicule B', 'Véhicule C'],
                'values' => [8.5, 7.2, 9.1],
                'vehicleIds' => [1, 2, 3]
            ],
            'monthlyCost' => [
                'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                'values' => [450000, 520000, 480000, 610000, 580000, 530000]
            ],
            'statistics' => [
                'avgConsumption' => 8.3,
                'minConsumption' => 7.2,
                'maxConsumption' => 9.1,
                'totalCost' => 3170000,
                'avgMonthlyCost' => 528333,
                'potentialSavings' => 475500
            ]
        ];
    }

*/


/*    public function getChartData(Request $request)
    {
        try {
            $filters = $request->validate([
                'timeRange' => 'sometimes|in:3,6,12,all',
                'vehicleFilter' => 'sometimes',
                'chartType' => 'sometimes|in:line,bar'
            ]);

            $consumptionData = $this->getFilteredConsumptionData($filters);
            $monthlyCostData = $this->getFilteredMonthlyCostData($filters);
            $repairStatsData = $this->getRepairStatsData($filters);
            $statistics = $this->calculateStatistics($consumptionData, $monthlyCostData, $repairStatsData);

            return response()->json([
                'success' => true,
                'consumption' => $consumptionData,
                'monthlyCost' => $monthlyCostData,
                'repairStats' => $repairStatsData,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des données',
                'error' => $e->getMessage()
            ], 500);
        }
    }*/

    // ... autres méthodes existantes ...

    private function getInterventionsParType()
    {
        return RepairLog::select('type_intervention', DB::raw('COUNT(*) as count'))
            ->groupBy('type_intervention')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->type_intervention => $item->count];
            });
    }

    private function getCoutMensuelEntretien()
    {
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();

        $monthlyCosts = RepairLog::select(
                DB::raw('YEAR(date_intervention) as year'),
                DB::raw('MONTH(date_intervention) as month'),
                DB::raw('SUM(cout_total) as total_cost')
            )
            ->where('date_intervention', '>=', $sixMonthsAgo)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get()
            ->reverse();

        $months = [];
        $costs = [];

        foreach ($monthlyCosts as $cost) {
            $months[] = Carbon::create($cost->year, $cost->month, 1)->format('M Y');
            $costs[] = $cost->total_cost;
        }

        return [
            'months' => $months,
            'costs' => $costs
        ];
    }

    private function getRepairStatsData($filters)
    {
        $query = RepairLog::query();

        if (isset($filters['timeRange']) && $filters['timeRange'] !== 'all') {
            $startDate = now()->subMonths($filters['timeRange'])->startOfMonth();
            $query->where('date_intervention', '>=', $startDate);
        }

        if (isset($filters['vehicleFilter']) && $filters['vehicleFilter'] !== 'all') {
            $query->where('vehicle_id', $filters['vehicleFilter']);
        }

        // Statistiques par type d'intervention
        $byType = $query->clone()
            ->select('type_intervention', DB::raw('COUNT(*) as count'), DB::raw('SUM(cout_total) as total_cost'))
            ->groupBy('type_intervention')
            ->get();

        // Statistiques par statut
        $byStatus = $query->clone()
            ->select('statut', DB::raw('COUNT(*) as count'))
            ->groupBy('statut')
            ->get();

        return [
            'byType' => $byType,
            'byStatus' => $byStatus,
            'totalInterventions' => $query->count(),
            'totalCout' => $query->sum('cout_total')
        ];
    }

    private function calculateStatistics($consumptionData, $monthlyCostData, $repairStatsData)
    {
        $stats = [];

        // Statistiques de consommation
        if (!empty($consumptionData['values'])) {
            $stats['avgConsumption'] = round(array_sum($consumptionData['values']) / count($consumptionData['values']), 2);
            $stats['minConsumption'] = round(min($consumptionData['values']), 2);
            $stats['maxConsumption'] = round(max($consumptionData['values']), 2);
        } else {
            $stats['avgConsumption'] = null;
            $stats['minConsumption'] = null;
            $stats['maxConsumption'] = null;
        }

        // Statistiques de coût carburant
        if (!empty($monthlyCostData['values'])) {
            $stats['totalCostFuel'] = array_sum($monthlyCostData['values']);
            $stats['avgMonthlyCostFuel'] = round($stats['totalCostFuel'] / count($monthlyCostData['values']));
        } else {
            $stats['totalCostFuel'] = null;
            $stats['avgMonthlyCostFuel'] = null;
        }

        // Statistiques de dépannages
        if ($repairStatsData['totalInterventions'] > 0) {
            $stats['totalInterventions'] = $repairStatsData['totalInterventions'];
            $stats['totalCostRepairs'] = $repairStatsData['totalCout'];
            $stats['avgCostPerRepair'] = round($repairStatsData['totalCout'] / $repairStatsData['totalInterventions']);

            // Type d'intervention le plus fréquent
            $mostFrequentType = $repairStatsData['byType']->sortByDesc('count')->first();
            $stats['mostFrequentRepairType'] = $mostFrequentType ? $mostFrequentType->type_intervention : null;
        } else {
            $stats['totalInterventions'] = 0;
            $stats['totalCostRepairs'] = 0;
            $stats['avgCostPerRepair'] = 0;
            $stats['mostFrequentRepairType'] = null;
        }

        return $stats;
    }

    // Méthode pour les données de démonstration
    private function getDemoData()
    {
        return [
            'consumption' => [
                'labels' => ['Véhicule A', 'Véhicule B', 'Véhicule C'],
                'values' => [8.5, 7.2, 9.1],
                'vehicleIds' => [1, 2, 3]
            ],
            'monthlyCost' => [
                'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                'values' => [450000, 520000, 480000, 610000, 580000, 530000]
            ],
            'repairStats' => [
                'byType' => collect([
                    (object)['type_intervention' => 'entretien_routine', 'count' => 12, 'total_cost' => 1200000],
                    (object)['type_intervention' => 'vidange', 'count' => 8, 'total_cost' => 800000],
                    (object)['type_intervention' => 'reparation', 'count' => 5, 'total_cost' => 2500000]
                ]),
                'byStatus' => collect([
                    (object)['statut' => 'termine', 'count' => 20],
                    (object)['statut' => 'planifie', 'count' => 3],
                    (object)['statut' => 'en_cours', 'count' => 2]
                ]),
                'totalInterventions' => 25,
                'totalCout' => 4500000
            ],
            'statistics' => [
                'avgConsumption' => 8.3,
                'minConsumption' => 7.2,
                'maxConsumption' => 9.1,
                'totalCostFuel' => 3170000,
                'avgMonthlyCostFuel' => 528333,
                'totalInterventions' => 25,
                'totalCostRepairs' => 4500000,
                'avgCostPerRepair' => 180000,
                'mostFrequentRepairType' => 'entretien_routine'
            ]
        ];
    }
}

