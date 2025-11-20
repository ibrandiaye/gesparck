@extends('layouts.app')

@section('title', 'Paiements - ' . $suiviFacture->numero_facture)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-money-bill-wave"></i> Paiements de la Facture {{ $suiviFacture->numero_facture }}
                    </h4>
                    <div>
                        <a href="{{ route('paiements.create', ['facture_id' => $suiviFacture->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau Paiement
                        </a>
                        <a href="{{ route('suivi-factures.show', $suiviFacture->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la Facture
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Informations de la facture -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <strong>Client:</strong><br>
                                        {{ $suiviFacture->client->nom }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Montant total:</strong><br>
                                        {{ number_format($suiviFacture->montant, 0, ',', ' ') }} €
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Montant payé:</strong><br>
                                        <span class="text-success">{{ number_format($suiviFacture->montant_paye, 0, ',', ' ') }} €</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Montant restant:</strong><br>
                                        <span class="text-danger">{{ number_format($suiviFacture->montant_restant, 0, ',', ' ') }} €</span>
                                    </div>
                                </div>
                                <!-- Barre de progression -->
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Progression du paiement</span>
                                        <span>{{ number_format($suiviFacture->pourcentage_paiement, 1) }}%</span>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar
                                            @if($suiviFacture->statut_paiement == 'payé') bg-success
                                            @elseif($suiviFacture->statut_paiement == 'partiel') bg-warning
                                            @else bg-danger @endif"
                                             style="width: {{ $suiviFacture->pourcentage_paiement }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($paiements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Montant</th>
                                    <th>Mode</th>
                                    <th>Référence</th>
                                    <th>Statut</th>
                                    <th>Créé le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paiements as $paiement)
                                <tr>
                                    <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-success bg-pill" style="font-size: 1em;">
                                            {{ number_format($paiement->montant, 0, ',', ' ') }} €
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $paiement->mode_paiement_libelle }}</span>
                                    </td>
                                    <td>
                                        @if($paiement->reference)
                                        <small>{{ $paiement->reference }}</small>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $paiement->statut == 'complet' ? 'success' : ($paiement->statut == 'partiel' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($paiement->statut) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $paiement->created_at->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('paiements.show', $paiement->id) }}"
                                               class="btn btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('paiements.edit', $paiement->id) }}"
                                               class="btn btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('paiements.destroy', $paiement->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"
                                                        title="Supprimer"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $paiements->links() }}
                    </div>
                    @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Aucun paiement enregistré pour cette facture.
                        <br>
                        <a href="{{ route('paiements.create', ['facture_id' => $suiviFacture->id]) }}"
                           class="btn btn-primary mt-2">
                            <i class="fas fa-plus"></i> Créer le premier paiement
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
