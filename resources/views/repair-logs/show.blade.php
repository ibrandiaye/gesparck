@extends('layouts.app')

@section('title', 'Détails de l\'Intervention')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-tools"></i> Détails de l'Intervention</h4>
                <div>
                    <a href="{{ route('repair-logs.edit', $repairLog) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('repair-logs.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Informations Générales</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Date:</th>
                                <td>{{ $repairLog->date_intervention->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Véhicule:</th>
                                <td>
                                    <strong>{{ $repairLog->vehicle->immatriculation }}</strong><br>
                                    <small>{{ $repairLog->vehicle->marque }} {{ $repairLog->vehicle->modele }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Type:</th>
                                <td>
                                    <i class="{{ $repairLog->type_icon }}"></i>
                                    {{ $repairLog->type_intervention_label }}
                                </td>
                            </tr>
                            <tr>
                                <th>Statut:</th>
                                <td>
                                    <span class="badge bg-{{ $repairLog->statut_color }}">
                                        {{ $repairLog->statut_label }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Kilométrage:</th>
                                <td>{{ number_format($repairLog->kilometrage_vehicule, 0, ',', ' ') }} km</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h5>Coûts et Prestataire</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Main d'œuvre:</th>
                                <td class="fw-bold text-primary">
                                    {{ number_format($repairLog->cout_main_oeuvre, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                            <tr>
                                <th>Pièces:</th>
                                <td class="fw-bold text-info">
                                    {{ number_format($repairLog->cout_pieces, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                            <tr>
                                <th>Coût total:</th>
                                <td class="fw-bold text-success">
                                    {{ number_format($repairLog->cout_total, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                            <tr>
                                <th>Garage:</th>
                                <td>{{ $repairLog->garage ?? 'Non spécifié' }}</td>
                            </tr>
                            <tr>
                                <th>Technicien:</th>
                                <td>{{ $repairLog->technicien ?? 'Non spécifié' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Description</h5>
                        <div class="alert alert-light border">
                            <strong>{{ $repairLog->description }}</strong>
                        </div>
                    </div>
                </div>

                @if($repairLog->details_travaux)
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Détails des Travaux</h5>
                        <div class="alert alert-light border">
                            {{ $repairLog->details_travaux }}
                        </div>
                    </div>
                </div>
                @endif

                @if($repairLog->date_prochaine_revision || $repairLog->prochain_kilometrage)
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Prochaine Intervention</h5>
                        <div class="alert alert-warning">
                            @if($repairLog->date_prochaine_revision)
                            <strong>Date:</strong> {{ $repairLog->date_prochaine_revision->format('d/m/Y') }}
                            @endif
                            @if($repairLog->prochain_kilometrage)
                            <br><strong>Kilométrage:</strong> {{ number_format($repairLog->prochain_kilometrage, 0, ',', ' ') }} km
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                @if($repairLog->notes)
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Notes & Observations</h5>
                        <div class="alert alert-info">
                            {{ $repairLog->notes }}
                        </div>
                    </div>
                </div>
                @endif

                @if($repairLog->facture)
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Facture</h5>
                        <a href="{{ route('repair-logs.download-facture', $repairLog) }}" class="btn btn-outline-primary">
                            <i class="fas fa-download"></i> Télécharger la Facture
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Actions Rapides -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bolt"></i> Actions Rapides</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('repair-logs.create') }}?vehicle_id={{ $repairLog->vehicle_id }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus"></i> Nouvelle Intervention
                    </a>
                    <a href="{{ route('vehicles.show', $repairLog->vehicle) }}"
                       class="btn btn-outline-info btn-sm">
                        <i class="fas fa-truck"></i> Voir le Véhicule
                    </a>
                    @if (Auth::user()->role=="admin")
                        <form action="{{ route('repair-logs.destroy', $repairLog) }}" method="POST" class="d-grid">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Supprimer cette intervention?')">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Historique du véhicule -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-history"></i> Historique du Véhicule</h5>
            </div>
            <div class="card-body">
                @if($historiqueVehicule->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($historiqueVehicule as $log)
                    <a href="{{ route('repair-logs.show', $log) }}"
                       class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <small>{{ $log->date_intervention->format('d/m/Y') }}</small>
                            <span class="badge bg-{{ $log->statut_color }}">{{ $log->statut_label }}</span>
                        </div>
                        <div class="d-flex w-100 justify-content-between">
                            <small>{{ $log->type_intervention_label }}</small>
                            <strong>{{ number_format($log->cout_total, 0, ',', ' ') }} FCFA</strong>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center mb-0">Aucun autre historique</p>
                @endif
            </div>
        </div>

        <!-- Informations de suivi -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Informations de Suivi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <small class="text-muted">Créé le:</small><br>
                        <strong>{{ $repairLog->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <small class="text-muted">Dernière modification:</small><br>
                        <strong>{{ $repairLog->updated_at->format('d/m/Y H:i') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
