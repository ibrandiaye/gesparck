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
   /* public function trips()
    {
        return $this->hasMany(Trip::class);
    }*/


    public function factures()
    {
        return $this->hasMany(SuiviFacture::class);
    }



    // Statistiques du client
    public function getStatistiquesAttribute()
    {
        return [
            'total_trajets' => $this->trips()->count(),
            'total_voyages' => $this->trips()->sum('nombre_trajets'),
            'distance_totale' => $this->trips()->get()->sum('distance_totale'),
            'dernier_trajet' => $this->trips()->latest('date_trajet')->first(),
            'frequence_mensuelle' => $this->calculerFrequenceMensuelle(),
            'total_factures' => $this->factures()->count(),
            'montant_total_factures' => $this->factures()->sum('montant')
        ];
    }
        public function trips() {
        return $this->belongsToMany(Trip::class, 'trip_clients')
        ->withPivot('ordre_visite', 'notes_livraison')
                    ->withTimestamps()
                    ->orderBy('date_trajet', 'desc');
    }
      // AJOUTER CETTE MÉTHODE MANQUANTE
    public function calculerFrequenceMensuelle()
    {
        $totalTrajets = $this->trips()->count();
        $premierTrajet = $this->trips()->oldest('date_trajet')->first();

        if (!$premierTrajet || $totalTrajets < 2) {
            return 0;
        }

        $moisEcoules = $premierTrajet->date_trajet->diffInMonths(now());
        return $moisEcoules > 0 ? $totalTrajets / $moisEcoules : $totalTrajets;
    }
}
