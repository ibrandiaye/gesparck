<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelEntry extends Model
{
    use HasFactory;
    protected $fillable = [
        'vehicle_id',
        'date_remplissage',
        'prix_litre',
        'litres',
        'cout_total',
        'kilometrage',
        'station',
        'type_carburant',
        'notes',
        'preuve'
    ];

    protected $casts = [
        'date_remplissage' => 'date',
        'prix_litre' => 'decimal:3',
        'litres' => 'decimal:2',
        'cout_total' => 'decimal:2',
        'kilometrage' => 'integer'
    ];

    // Relations
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Scopes utiles
    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date_remplissage', [$startDate, $endDate]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date_remplissage', now()->month)
                    ->whereYear('date_remplissage', now()->year);
    }

    // Accessors (calculs automatiques)
    public function getConsommationAttribute()
    {
        // Trouver le remplissage précédent pour calculer la consommation
        $previousEntry = self::where('vehicle_id', $this->vehicle_id)
            ->where('date_remplissage', '<', $this->date_remplissage)
            ->orderBy('date_remplissage', 'desc')
            ->first();

        if ($previousEntry) {
            $kmParcourus = $this->kilometrage - $previousEntry->kilometrage;
            if ($kmParcourus > 0) {
                return ($this->litres / $kmParcourus) * 100; // L/100km
            }
        }

        return null;
    }

    public function getKmParcourusAttribute()
    {
        $previousEntry = self::where('vehicle_id', $this->vehicle_id)
            ->where('date_remplissage', '<', $this->date_remplissage)
            ->orderBy('date_remplissage', 'desc')
            ->first();

        return $previousEntry ? $this->kilometrage - $previousEntry->kilometrage : null;
    }

    // Méthode pour calculer automatiquement le coût total
    public static function calculateTotalCost($prixLitre, $litres)
    {
        return round($prixLitre * $litres, 2);
    }
    // Nouvelles méthodes pour les statistiques carburant
    public function getConsommationMoyenneAttribute()
    {
        $entries = $this->fuelEntries()->orderBy('date_remplissage')->get();

        if ($entries->count() < 2) return null;

        $totalConsommation = 0;
        $validEntries = 0;

        for ($i = 1; $i < $entries->count(); $i++) {
            $current = $entries[$i];
            $previous = $entries[$i - 1];

            $kmParcourus = $current->kilometrage - $previous->kilometrage;
            if ($kmParcourus > 0) {
                $consommation = ($current->litres / $kmParcourus) * 100;
                $totalConsommation += $consommation;
                $validEntries++;
            }
        }

        return $validEntries > 0 ? round($totalConsommation / $validEntries, 2) : null;
    }

    public function getCoutMensuelCarburantAttribute()
    {
        return $this->fuelEntries()
            ->whereYear('date_remplissage', now()->year)
            ->whereMonth('date_remplissage', now()->month)
            ->sum('cout_total');
    }

    public function getDernierRemplissageAttribute()
    {
        return $this->fuelEntries()
            ->orderBy('date_remplissage', 'desc')
            ->first();
    }

    public function getKilometrageMoyenMensuelAttribute()
    {
        $entries = $this->fuelEntries()->orderBy('date_remplissage')->get();

        if ($entries->count() < 2) return null;

        $firstEntry = $entries->first();
        $lastEntry = $entries->last();

        $kmTotaux = $lastEntry->kilometrage - $firstEntry->kilometrage;
        $moisEcoules = $firstEntry->date_remplissage->diffInMonths($lastEntry->date_remplissage);

        return $moisEcoules > 0 ? round($kmTotaux / $moisEcoules) : null;
    }
}
