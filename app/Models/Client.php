<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
     protected $fillable = [
        'nom',
        'adresse'
    ];

    // Relation avec les trajets
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }





    // Statistiques du client
    public function getStatistiquesAttribute()
    {
        return [
            'total_trajets' => $this->trips()->count(),
            'total_voyages' => $this->trips()->sum('nombre_trajets'),
            'distance_totale' => $this->trips()->get()->sum('distance_totale'),
            'dernier_trajet' => $this->trips()->latest('date_trajet')->first()
        ];
    }

}
