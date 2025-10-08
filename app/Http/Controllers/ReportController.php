<?php

namespace App\Http\Controllers;

use App\Exports\FuelEntriesExport;
use App\Exports\GlobalReportExport;
use App\Exports\RepairLogsExport;
use App\Models\Vehicle;
use App\Models\FuelEntry;
use App\Models\RepairLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;


class ReportController extends Controller
{
    public function __construct()
    {
       // $this->middleware('auth');
    }

    public function index()
    {
        $vehicles = Vehicle::all();
        return view('reports.index', compact('vehicles'));
    }

    public function getReportData(Request $request)
{
    try {
        // Debug: Voir ce qui est reçu
        \Log::info('Données reçues pour le rapport:', $request->all());

        // Validation plus flexible
        $filters = $request->validate([
            'periode' => 'required|in:7,30,90,180,365,custom',
            'vehicle_id' => 'nullable|string', // Changé en string pour accepter "all"
            'type_rapport' => 'required|in:global,carburant,entretien,comparatif',
            'date_debut' => 'nullable|date', // Rendre nullable
            'date_fin' => 'nullable|date', // Rendre nullable
        ]);

        // Débogage des filtres validés
        \Log::info('Filtres validés:', $filters);

        $dateRange = $this->getDateRange($filters);
       // dd( $this->getTablesData($dateRange, $filters));

        $reportData = [
            'charts' => $this->getChartsData($dateRange, $filters),
            'statistics' => $this->getStatistics($dateRange, $filters),
            'tables' => $this->getTablesData($dateRange, $filters)
        ];

        return response()->json([
            'success' => true,
            'data' => $reportData
        ]);

    } catch (\Exception $e) {
        \Log::error('Erreur dans getReportData: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la génération du rapport: ' . $e->getMessage(),
            'debug' => $request->all() // Ajout pour débogage
        ], 500);
    }
}

    private function getDateRange($filters)
{
    \Log::info('Calcul de la période avec filtres:', $filters);

    if ($filters['periode'] === 'custom') {
        // Vérifier que les dates custom sont fournies
        if (empty($filters['date_debut']) || empty($filters['date_fin'])) {
            // Fallback sur 30 jours si dates manquantes
            return [
                'start' => now()->subDays(30)->startOfDay(),
                'end' => now()->endOfDay()
            ];
        }

        return [
            'start' => Carbon::parse($filters['date_debut'])->startOfDay(),
            'end' => Carbon::parse($filters['date_fin'])->endOfDay()
        ];
    }

    $days = (int) $filters['periode'];
    return [
        'start' => now()->subDays($days)->startOfDay(),
        'end' => now()->endOfDay()
    ];
}

    private function getChartsData($dateRange, $filters)
    {
        // Données pour la répartition des coûts
        $costDistribution = $this->getCostDistribution($dateRange, $filters);

        // Données pour l'évolution mensuelle
        $monthlyTrends = $this->getMonthlyTrends($dateRange, $filters);

        return [
            'costDistribution' => $costDistribution,
            'monthlyTrends' => $monthlyTrends
        ];
    }

    private function getCostDistribution($dateRange, $filters)
    {
        $fuelQuery = FuelEntry::whereBetween('date_remplissage', [$dateRange['start'], $dateRange['end']]);
        $repairQuery = RepairLog::whereBetween('date_intervention', [$dateRange['start'], $dateRange['end']]);

        if ($filters['vehicle_id'] !== 'all') {
            $fuelQuery->where('vehicle_id', $filters['vehicle_id']);
            $repairQuery->where('vehicle_id', $filters['vehicle_id']);
        }

        return [
            'fuel' => $fuelQuery->sum('cout_total'),
            'repairs' => $repairQuery->sum('cout_total'),
            'other' => 0 // Peut être étendu pour d'autres coûts
        ];
    }

    private function getMonthlyTrends($dateRange, $filters)
    {
        $months = [];
        $fuelCosts = [];
        $repairCosts = [];

        $start = $dateRange['start']->copy();
        $end = $dateRange['end']->copy();

        while ($start <= $end) {
            $monthKey = $start->format('M Y');
            $months[] = $monthKey;

            // Coût carburant du mois
            $fuelQuery = FuelEntry::whereYear('date_remplissage', $start->year)
                ->whereMonth('date_remplissage', $start->month);

            $repairQuery = RepairLog::whereYear('date_intervention', $start->year)
                ->whereMonth('date_intervention', $start->month);

            if ($filters['vehicle_id'] !== 'all') {
                $fuelQuery->where('vehicle_id', $filters['vehicle_id']);
                $repairQuery->where('vehicle_id', $filters['vehicle_id']);
            }

            $fuelCosts[] = $fuelQuery->sum('cout_total');
            $repairCosts[] = $repairQuery->sum('cout_total');

            $start->addMonth();
        }

        return [
            'months' => $months,
            'fuel' => $fuelCosts,
            'repairs' => $repairCosts
        ];
    }

    private function getStatistics($dateRange, $filters)
    {
        $fuelQuery = FuelEntry::whereBetween('date_remplissage', [$dateRange['start'], $dateRange['end']]);
        $repairQuery = RepairLog::whereBetween('date_intervention', [$dateRange['start'], $dateRange['end']]);

        if ($filters['vehicle_id'] !== 'all') {
            $fuelQuery->where('vehicle_id', $filters['vehicle_id']);
            $repairQuery->where('vehicle_id', $filters['vehicle_id']);
        }

        $totalFuelCost = $fuelQuery->sum('cout_total');
        $totalRepairCost = $repairQuery->sum('cout_total');
        $totalInterventions = $repairQuery->count();
        $totalRefuels = $fuelQuery->count();

        // Calcul de la consommation moyenne
        $avgConsumption = $this->calculateAverageConsumption($dateRange, $filters);

        return [
            'totalCost' => $totalFuelCost + $totalRepairCost,
            'avgConsumption' => $avgConsumption,
            'totalInterventions' => $totalInterventions,
            'totalRefuels' => $totalRefuels
        ];
    }

    private function calculateAverageConsumption($dateRange, $filters)
    {
        $vehicles = $filters['vehicle_id'] === 'all'
            ? Vehicle::all()
            : Vehicle::where('id', $filters['vehicle_id'])->get();

        $totalConsumption = 0;
        $vehicleCount = 0;

        foreach ($vehicles as $vehicle) {
            if ($vehicle->consommation_moyenne) {
                $totalConsumption += $vehicle->consommation_moyenne;
                $vehicleCount++;
            }
        }

        return $vehicleCount > 0 ? round($totalConsumption / $vehicleCount, 2) : 0;
    }

    private function getTablesData($dateRange, $filters)
    {
        //dd($this->getDetailedReport($dateRange, $filters));
        return [
            'topVehicles' => $this->getTopVehicles($dateRange),
            'interventionTypes' => $this->getInterventionTypes($dateRange, $filters),
            'detailedReport' => $this->getDetailedReport($dateRange, $filters)
        ];
    }

    private function getTopVehicles($dateRange)
    {
        return Vehicle::with(['fuelEntries' => function($query) use ($dateRange) {
                $query->whereBetween('date_remplissage', [$dateRange['start'], $dateRange['end']]);
            }, 'repairLogs' => function($query) use ($dateRange) {
                $query->whereBetween('date_intervention', [$dateRange['start'], $dateRange['end']]);
            }])
            ->get()
            ->map(function($vehicle) {
                $fuelCost = $vehicle->fuelEntries->sum('cout_total');
                $repairCost = $vehicle->repairLogs->sum('cout_total');

                return [
                    'immatriculation' => $vehicle->immatriculation,
                    'fuel_cost' => $fuelCost,
                    'repair_cost' => $repairCost,
                    'total_cost' => $fuelCost + $repairCost
                ];
            })
            ->sortByDesc('total_cost')
            ->take(5)
            ->values();
    }

    private function getInterventionTypes($dateRange, $filters)
    {
        $query = RepairLog::whereBetween('date_intervention', [$dateRange['start'], $dateRange['end']]);

        if ($filters['vehicle_id'] !== 'all') {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        return $query->select('type_intervention',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(cout_total) as total_cost'),
                DB::raw('AVG(cout_total) as avg_cost')
            )
            ->groupBy('type_intervention')
            ->get()
            ->map(function($item) {
                $types = [
                    'entretien_routine' => 'Entretien Routine',
                    'reparation' => 'Réparation',
                    'vidange' => 'Vidange',
                    'freinage' => 'Freinage',
                    'pneumatique' => 'Pneumatique',
                    'electrique' => 'Électrique',
                    'mecanique' => 'Mécanique',
                    'carrosserie' => 'Carrosserie',
                    'autre' => 'Autre'
                ];

                return [
                    'type_label' => $types[$item->type_intervention] ?? $item->type_intervention,
                    'count' => $item->count,
                    'total_cost' => $item->total_cost,
                    'avg_cost' => round($item->avg_cost, 2)
                ];
            })
            ->sortByDesc('total_cost')
            ->values();
    }

    private function getDetailedReport($dateRange, $filters)
    {
        $fuelEntries = FuelEntry::with('vehicle')
            ->whereBetween('date_remplissage', [$dateRange['start'], $dateRange['end']]);

        $repairLogs = RepairLog::with('vehicle')
            ->whereBetween('date_intervention', [$dateRange['start'], $dateRange['end']]);

        if ($filters['vehicle_id'] !== 'all') {
            $fuelEntries->where('vehicle_id', $filters['vehicle_id']);
            $repairLogs->where('vehicle_id', $filters['vehicle_id']);
        }


        $fuelData = $fuelEntries->get()->map(function($entry) {
            return [
                'date' => $entry->date_remplissage->format('d/m/Y'),
                'vehicle' => $entry->vehicle->immatriculation,
                'type' => 'carburant',
                'type_label' => 'Carburant',
                'description' => $entry->litres . 'L @ ' . $entry->prix_litre . ' FCFA/L',
                'cost' => $entry->cout_total,
                'kilometrage' => $entry->kilometrage
            ];
        });
         //dd($fuelData);

        $repairData = $repairLogs->get()->map(function($log) {
            $types = [
                'entretien_routine' => 'Entretien Routine',
                'reparation' => 'Réparation',
                'vidange' => 'Vidange',
                'freinage' => 'Freinage',
                'pneumatique' => 'Pneumatique',
                'electrique' => 'Électrique',
                'mecanique' => 'Mécanique',
                'carrosserie' => 'Carrosserie',
                'autre' => 'Autre'
            ];

            return [
                'date' => $log->date_intervention->format('d/m/Y'),
                'vehicle' => $log->vehicle->immatriculation,
                'type' => 'entretien',
                'type_label' => $types[$log->type_intervention] ?? $log->type_intervention,
                'description' => $log->description,
                'cost' => $log->cout_total,
                'kilometrage' => $log->kilometrage_vehicule
            ];
        });



        if(sizeof($fuelData )> 0 )
        {
             return $fuelData->merge($repairData)
            ->sortByDesc('date')
            ->values();
        }
        else
        {
            return $fuelData = $repairData;
        }

    }
    public function exportPdf(Request $request)
{
    try {
        $filters = $request->all();
        $dateRange = $this->getDateRange($filters);

        $reportData = [
            'charts' => $this->getChartsData($dateRange, $filters),
            'statistics' => $this->getStatistics($dateRange, $filters),
            'tables' => $this->getTablesData($dateRange, $filters),
            'filters' => $filters,
            'dateRange' => $dateRange,
            'generated_at' => now()->format('d/m/Y H:i')
        ];

        //dd($reportData);
        $pdf = PDF::loadView('reports.pdf.export', $reportData);
        $pdf->setPaper('A4', 'landscape');

        $filename = 'rapport-flotte-' . now()->format('Y-m-d  h:i:s') . '.pdf';

        return $pdf->download($filename);

    } catch (\Exception $e) {
        \Log::error('Erreur export PDF: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()
        ], 500);
    }
}

public function exportExcel(Request $request)
{
    try {
        $filters = $request->all();
        $type = $request->get('export_type', 'global');

        $filename = 'rapport-flotte-' . now()->format('Y-m-d') . '.xlsx';

        switch ($type) {
            case 'carburant':
                return Excel::download(new FuelEntriesExport($filters), $filename);
            case 'entretien':
                return Excel::download(new RepairLogsExport($filters), $filename);
            case 'global':
            default:
                return Excel::download(new GlobalReportExport($filters), $filename);
        }

    } catch (\Exception $e) {
        \Log::error('Erreur export Excel: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'export Excel: ' . $e->getMessage()
        ], 500);
    }
}

public function exportCsv(Request $request)
{
    try {
        $filters = $request->all();
        $type = $request->get('export_type', 'global');

        $filename = 'rapport-flotte-' . now()->format('Y-m-d') . '.csv';

        switch ($type) {
            case 'carburant':
                return Excel::download(new FuelEntriesExport($filters), $filename, \Maatwebsite\Excel\Excel::CSV);
            case 'entretien':
                return Excel::download(new RepairLogsExport($filters), $filename, \Maatwebsite\Excel\Excel::CSV);
            case 'global':
            default:
                return Excel::download(new GlobalReportExport($filters), $filename, \Maatwebsite\Excel\Excel::CSV);
        }

    } catch (\Exception $e) {
        \Log::error('Erreur export CSV: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'export CSV: ' . $e->getMessage()
        ], 500);
    }
}
}
