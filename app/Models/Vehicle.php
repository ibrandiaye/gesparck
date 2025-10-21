<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
   protected $fillable = [
        'immatriculation',
        'marque',
        'modele',
        'type_vehicule',
        'kilometrage_actuel',
        'etat',
        'carburant_id',
        'categorie'

    ];

    protected $casts = [
        //'date_mise_en_service' => 'date',
        'kilometrage_actuel' => 'integer'
    ];

    // Relations futures
    public function fuelEntries()
    {
        return $this->hasMany(FuelEntry::class);
    }

    public function repairLogs()
    {
        return $this->hasMany(RepairLog::class);
    }
    public function carburant()
    {
        return $this->belongsTo(Carburant::class);
    }

    // Scopes utiles
    public function scopeDisponible($query)
    {
        return $query->where('etat', 'disponible');
    }

    public function scopeEnEntretien($query)
    {
        return $query->where('etat', 'en_entretien');
    }


public function getCoutTotalEntretienAttribute()
{
    return $this->repairLogs()->sum('cout_total');
}

public function getDernierEntretienAttribute()
{
    return $this->repairLogs()
        ->where('statut', 'termine')
        ->orderBy('date_intervention', 'desc')
        ->first();
}

public function getProchainEntretienAttribute()
{
    return $this->repairLogs()
        ->whereIn('statut', ['planifie', 'en_cours'])
        ->orderBy('date_intervention', 'asc')
        ->first();
}

public function getEntretiensEnCoursAttribute()
{
    return $this->repairLogs()
        ->where('statut', 'en_cours')
        ->count();
}
    public function trips() {
        return $this->hasMany(Trip::class);
    }
    public function getNombreTotalTrajetsAttribute() {
       // dd($this->trips->sum('nombre_trajets'));
        /*$total = 0;
        foreach ($this->trips as $trip) {
            $total += $trip->nombre_trajets;
        }
       return $total; */
       return $this->trips->sum('nombre_trajets');

    }

}
