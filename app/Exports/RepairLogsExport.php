<?php

namespace App\Exports;

use App\Models\RepairLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RepairLogsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = RepairLog::with('vehicle');

        if (!empty($this->filters['date_debut']) && !empty($this->filters['date_fin'])) {
            $query->whereBetween('date_intervention', [
                $this->filters['date_debut'],
                $this->filters['date_fin']
            ]);
        }

        if (!empty($this->filters['vehicle_id']) && $this->filters['vehicle_id'] !== 'all') {
            $query->where('vehicle_id', $this->filters['vehicle_id']);
        }

        return $query->orderBy('date_intervention', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Date Intervention',
            'Véhicule',
            'Immatriculation',
            'Type Intervention',
            'Description',
            'Détails Travaux',
            'Coût Main d\'Œuvre (FCFA)',
            'Coût Pièces (FCFA)',
            'Coût Total (FCFA)',
            'Kilométrage',
            'Garage',
            'Technicien',
            'Statut'
        ];
    }

    public function map($log): array
    {
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

        $statuts = [
            'planifie' => 'Planifié',
            'en_cours' => 'En Cours',
            'termine' => 'Terminé',
            'annule' => 'Annulé'
        ];

        return [
            $log->date_intervention->format('d/m/Y'),
            $log->vehicle->marque . ' ' . $log->vehicle->modele,
            $log->vehicle->immatriculation,
            $types[$log->type_intervention] ?? $log->type_intervention,
            $log->description,
            $log->details_travaux ?? 'N/A',
            number_format($log->cout_main_oeuvre, 0, ',', ' '),
            number_format($log->cout_pieces, 0, ',', ' '),
            number_format($log->cout_total, 0, ',', ' '),
            number_format($log->kilometrage_vehicule, 0, ',', ' '),
            $log->garage ?? 'N/A',
            $log->technicien ?? 'N/A',
            $statuts[$log->statut] ?? $log->statut
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:M' => ['alignment' => ['wrapText' => true]],
        ];
    }
}
