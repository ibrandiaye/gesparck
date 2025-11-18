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
        'client_id',
        'date_facture',
        'etat' // Nouveau
    ];

    protected $casts = [
        'date_livraison' => 'date',
        'montant' => 'decimal:2',
        'date_facture' => 'date'
    ];

    /**
     * Relation avec le client
     */
    public function client()
    {
        return $this->belongsTo(ClientFacture::class);
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
        if($this->date_livraison)
            return $this->date_livraison->format('F Y');
        else
            return null;
    }

     /**
     * Scope pour les factures non livrées
     */
    public function scopeNonLivrees($query)
    {
        return $query->where('etat', 'non livré');
    }


    /**
     * Méthode pour marquer comme livré
     */
    public function marquerCommeLivre($dateLivraison = null)
    {
        $this->update([
            'etat' => 'livré',
            'date_livraison' => $dateLivraison ?? now()
        ]);
    }

    /**
     * Méthode pour marquer comme non livré
     */
    public function marquerCommeNonLivre()
    {
        $this->update([
            'etat' => 'non livré',
            'date_livraison' => null
        ]);
    }



}
