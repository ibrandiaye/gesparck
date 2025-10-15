<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;
     protected $fillable = [
        'vehicle_id',
        'fuel_entry_id',
        'destination',
        'motif',
        'nombre_trajets', // Nouveau champ
        'date_trajet',
        'notes'
    ];
     protected $casts = [
        'date_trajet' => 'date'
    ];

     public function vehicle() {
        return $this->belongsTo(Vehicle::class);
    }

    public function fuelEntry() {
        return $this->belongsTo(FuelEntry::class);
    }

    // Accès au conducteur via le véhicule
    public function getConducteurAttribute() {
        return $this->vehicle->immatriculation;
    }
}
