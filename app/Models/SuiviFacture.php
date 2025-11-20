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
        'etat', // Nouveau
        'montant_retour', // Nouveau
        'raison_retour',  // Nouveau
        'date_retour',    // Nouveau
    ];

    protected $casts = [
        'date_livraison' => 'date',
        'montant' => 'decimal:2',
        'date_facture' => 'date',
        'montant_retour' => 'decimal:2', // Nouveau
        'date_retour' =>'date'
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

     /**
     * Relation avec les paiements
     */
    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    /**
     * Accesseur pour le montant payé
     */
  /*  public function getMontantPayeAttribute()
    {
        return $this->paiements()->sum('montant');
    }*/

    /**
     * Accesseur pour le montant restant
     */
   /* public function getMontantRestantAttribute()
    {
        return $this->montant - $this->montant_paye;
    }*/

    /**
     * Accesseur pour le statut de paiement
     */
    public function getStatutPaiementAttribute()
    {
        $montantPaye = $this->montant_paye;

        if ($montantPaye == 0) {
            return 'impayé';
        } elseif ($montantPaye < $this->montant) {
            return 'partiel';
        } else {
            return 'payé';
        }
    }

    /**
     * Accesseur pour le pourcentage de paiement
     */
  /*  public function getPourcentagePaiementAttribute()
    {
        if ($this->montant == 0) {
            return 0;
        }

        return ($this->montant_paye / $this->montant) * 100;
    }*/

    /**
     * Vérifier si la facture est complètement payée
     */
    public function getEstCompletementPayeeAttribute()
    {
        return $this->montant_paye >= $this->montant;
    }

    /**
     * Scope pour les factures payées
     */
    public function scopePayees($query)
    {
        return $query->whereHas('paiements', function($q) {
            $q->selectRaw('suivi_facture_id, SUM(montant) as total_paye')
              ->groupBy('suivi_facture_id')
              ->havingRaw('total_paye >= suivi_factures.montant');
        });
    }

    /**
     * Scope pour les factures impayées
     */
    public function scopeImpayees($query)
    {
        return $query->whereDoesntHave('paiements')
                    ->orWhereHas('paiements', function($q) {
                        $q->selectRaw('suivi_facture_id, SUM(montant) as total_paye')
                          ->groupBy('suivi_facture_id')
                          ->havingRaw('total_paye < suivi_factures.montant');
                    });
    }

     /**
     * Accesseur pour le montant net (montant - retour)
     */
    public function getMontantNetAttribute()
    {
        return max(0, $this->montant - $this->montant_retour);
    }

    /**
     * Accesseur pour le montant payé (basé sur le montant net)
     */
    public function getMontantPayeAttribute()
    {
        return min($this->paiements()->sum('montant'), $this->montant_net);
    }

    /**
     * Accesseur pour le montant restant (basé sur le montant net)
     */
    public function getMontantRestantAttribute()
    {
        return max(0, $this->montant_net - $this->montant_paye);
    }

    /**
     * Accesseur pour le pourcentage de paiement (basé sur le montant net)
     */
    public function getPourcentagePaiementAttribute()
    {
        if ($this->montant_net == 0) {
            return 0;
        }

        return ($this->montant_paye / $this->montant_net) * 100;
    }

    /**
     * Vérifier s'il y a un retour sur cette facture
     */
    public function getARetourAttribute()
    {
        return $this->montant_retour > 0;
    }

    /**
     * Scope pour les factures avec retour
     */
    public function scopeAvecRetour($query)
    {
        return $query->where('montant_retour', '>', 0);
    }

    /**
     * Scope pour les factures sans retour
     */
    public function scopeSansRetour($query)
    {
        return $query->where('montant_retour', 0);
    }

    /**
     * Méthode pour enregistrer un retour
     */
    public function enregistrerRetour($montant, $raison = null, $dateRetour = null)
    {
        if ($montant > $this->montant) {
            throw new \Exception("Le montant du retour ne peut pas dépasser le montant total de la facture");
        }

        $this->update([
            'montant_retour' => $montant,
            'raison_retour' => $raison,
            'date_retour' => $dateRetour ?? now()
        ]);

        return $this;
    }

    /**
     * Méthode pour annuler un retour
     */
    public function annulerRetour()
    {
        $this->update([
            'montant_retour' => 0,
            'raison_retour' => null,
            'date_retour' => null
        ]);

        return $this;
    }

}
