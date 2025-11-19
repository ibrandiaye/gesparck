<?php

namespace App\Exports;

use App\Models\FuelEntry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FuelEntriesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = FuelEntry::with('vehicle');

        if (!empty($this->filters['date_debut']) && !empty($this->filters['date_fin'])) {
            $query->whereBetween('date_remplissage', [
                $this->filters['date_debut'],
                $this->filters['date_fin']
            ]);
        }

        if (!empty($this->filters['vehicle_id']) && $this->filters['vehicle_id'] !== 'all') {
            $query->where('vehicle_id', $this->filters['vehicle_id']);
        }


        return $query->orderBy('date_remplissage', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Véhicule',
            'Immatriculation',
            'Station',
            'Type Carburant',
            'Prix Litre (FCFA)',
            'Quantité (L)',
            'Coût Total (FCFA)',
            'Kilométrage',
            'Consommation (L/100km)'
        ];
    }

    public function map($entry): array
    {
        return [
            $entry->date_remplissage->format('d/m/Y'),
            $entry->vehicle->marque . ' ' . $entry->vehicle->modele,
            $entry->vehicle->immatriculation,
            $entry->station ?? 'N/A',
            strtoupper($entry->type_carburant),
            number_format($entry->prix_litre, 0, ',', ' '),
            number_format($entry->litres, 1, ',', ' '),
            number_format($entry->cout_total, 0, ',', ' '),
            number_format($entry->kilometrage, 0, ',', ' '),
            $entry->consommation ? number_format($entry->consommation, 0, ',', ' ') . ' L/100km' : 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:J' => ['alignment' => ['wrapText' => true]],
        ];
    }
}
