<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;
    protected $fillable = [
        'suivi_facture_id',
        'montant',
        'date_paiement',
        'mode_paiement',
        'reference',
        'notes',
        'statut'
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'montant' => 'decimal:2'
    ];

    /**
     * Relation avec la facture
     */
    public function facture()
    {
        return $this->belongsTo(SuiviFacture::class, 'suivi_facture_id');
    }

    /**
     * Scope pour les paiements par période
     */
    public function scopePeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date_paiement', [$dateDebut, $dateFin]);
    }

    /**
     * Scope pour les paiements par mode
     */
    public function scopeMode($query, $mode)
    {
        return $query->where('mode_paiement', $mode);
    }

    /**
     * Accesseur pour le libellé du mode de paiement
     */
    public function getModePaiementLibelleAttribute()
    {
        $modes = [
            'virement' => 'Virement',
            'cheque' => 'Chèque',
            'espece' => 'Espèces',
            'carte' => 'Carte bancaire'
        ];

        return $modes[$this->mode_paiement] ?? $this->mode_paiement;
    }
}
