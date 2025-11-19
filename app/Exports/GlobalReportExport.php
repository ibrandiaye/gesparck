<?php

namespace App\Exports;

use App\Models\Vehicle;
use App\Models\FuelEntry;
use App\Models\RepairLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GlobalReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        return Vehicle::with(['fuelEntries', 'repairLogs'])->get();
    }

    public function title(): string
    {
        return 'Rapport Global';
    }

    public function headings(): array
    {
        return [
            'Immatriculation',
            'Marque/Modèle',
            'Type Véhicule',
            'Kilométrage Actuel',
            'État',
            'Coût Total Carburant (FCFA)',
            'Coût Total Entretien (FCFA)',
            'Coût Total (FCFA)',
            'Nombre Remplissages',
            'Nombre Interventions',
            'Consommation Moyenne (L/100km)'
        ];
    }

    public function map($vehicle): array
    {
        $fuelCost = $vehicle->fuelEntries->sum('cout_total');
        $repairCost = $vehicle->repairLogs->sum('cout_total');
        $totalCost = $fuelCost + $repairCost;

        return [
            $vehicle->immatriculation,
            $vehicle->marque . ' ' . $vehicle->modele,
            ucfirst($vehicle->type_vehicule),
            number_format($vehicle->kilometrage_actuel, 0, ',', ' '),
            ucfirst($vehicle->etat),
            number_format($fuelCost, 0, ',', ' '),
            number_format($repairCost, 0, ',', ' '),
            number_format($totalCost, 0, ',', ' '),
            $vehicle->fuelEntries->count(),
            $vehicle->repairLogs->count(),
            $vehicle->consommation_moyenne ? number_format($vehicle->consommation_moyenne, 0, ',', ' ') : 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:K' => ['alignment' => ['wrapText' => true]],
        ];
    }
}
