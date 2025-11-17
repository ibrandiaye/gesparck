<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFacture extends Model
{
    use HasFactory;
      protected $fillable = [
        'nom',
        'adresse'
    ];

    public function factures()
    {
        return $this->hasMany(SuiviFacture::class,'client_id');
    }
    public function getStatistiquesAttribute()
    {
        return [

            'total_factures' => $this->factures()->count(),
            'montant_total_factures' => $this->factures()->sum('montant')
        ];
    }
}
