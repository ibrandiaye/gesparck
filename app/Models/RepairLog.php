<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'date_intervention',
        'type_intervention',
        'description',
        'details_travaux',
        'cout_main_oeuvre',
        'cout_pieces',
        'cout_total',
        'kilometrage_vehicule',
        'garage',
        'technicien',
        'statut',
        'date_prochaine_revision',
        'prochain_kilometrage',
        'notes',
        'facture'
    ];

    protected $casts = [
        'date_intervention' => 'date',
        'date_prochaine_revision' => 'date',
        'cout_main_oeuvre' => 'decimal:2',
        'cout_pieces' => 'decimal:2',
        'cout_total' => 'decimal:2',
        'kilometrage_vehicule' => 'integer',
        'prochain_kilometrage' => 'integer'
    ];

    // Relations
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Scopes utiles
    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date_intervention', now()->month)
                    ->whereYear('date_intervention', now()->year);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('statut', $status);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type_intervention', $type);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date_intervention', [$startDate, $endDate]);
    }

    // Accessors et Mutators
    public function getTypeInterventionLabelAttribute()
    {
        $types = [
            'entretien_routine' => 'Entretien Routine',
            'reparation' => 'Réparation',
            'vidange' => 'Vidange',
            'freinage' => 'Freinage',
            'pneumatique' => 'Pneumatique',
            'electrique' => 'Électrique',
            'mecanique' => 'Mécanique',
            'carrosserie' => 'Carrosserie',
            'autre' => 'Autre'
        ];

        return $types[$this->type_intervention] ?? $this->type_intervention;
    }

    public function getStatutLabelAttribute()
    {
        $statuts = [
            'planifie' => 'Planifié',
            'en_cours' => 'En Cours',
            'termine' => 'Terminé',
            'annule' => 'Annulé'
        ];

        return $statuts[$this->statut] ?? $this->statut;
    }

    public function getCoutTotalAttribute($value)
    {
        return $this->cout_main_oeuvre + $this->cout_pieces;
    }

    // Méthode pour calculer automatiquement le coût total
    public static function calculateTotalCost($mainOeuvre, $pieces)
    {
        return $mainOeuvre + $pieces;
    }

    // Méthode pour les couleurs des statuts
    public function getStatutColorAttribute()
    {
        $colors = [
            'planifie' => 'info',
            'en_cours' => 'warning',
            'termine' => 'success',
            'annule' => 'danger'
        ];

        return $colors[$this->statut] ?? 'secondary';
    }

    // Méthode pour les icônes des types d'intervention
    public function getTypeIconAttribute()
    {
        $icons = [
            'entretien_routine' => 'fas fa-cog',
            'reparation' => 'fas fa-tools',
            'vidange' => 'fas fa-oil-can',
            'freinage' => 'fas fa-stop-circle',
            'pneumatique' => 'fas fa-tire',
            'electrique' => 'fas fa-bolt',
            'mecanique' => 'fas fa-cogs',
            'carrosserie' => 'fas fa-car-crash',
            'autre' => 'fas fa-wrench'
        ];

        return $icons[$this->type_intervention] ?? 'fas fa-wrench';
    }
}
