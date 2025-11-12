<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuiviFacture extends Model
{
    use HasFactory;
     protected $table = 'suivi_factures';

    protected $fillable = [
        'numero_facture',
        'date_livraison',
        'montant',
        'client_id'
    ];

    protected $casts = [
        'date_livraison' => 'date',
        'montant' => 'decimal:2'
    ];

    /**
     * Relation avec le client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Scope pour les factures d'un client spécifique
     */
    public function scopePourClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope pour les factures par période
     */
    public function scopePeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date_livraison', [$dateDebut, $dateFin]);
    }

    /**
     * Accesseur pour le mois de livraison
     */
    public function getMoisLivraisonAttribute()
    {
        return $this->date_livraison->format('F Y');
    }
}
