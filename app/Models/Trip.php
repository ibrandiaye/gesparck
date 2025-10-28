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
        'notes',
       // 'client_id'
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
    /*public function client() {
        return $this->belongsTo(Client::class);
    }*/
    public function clients() {
        return $this->belongsToMany(Client::class, 'trip_clients')
        ->withPivot('ordre_visite', 'notes_livraison')
                    ->withTimestamps();
    }
    // Accès au conducteur via le véhicule
    public function getConducteurAttribute() {
        return $this->vehicle->immatriculation;
    }
      // Scope pour les trajets d'un client spécifique
    public function scopePourClient($query, $clientId) {
        return $query->where('client_id', $clientId);
    }
}
