@extends('layouts.app')

@section('title', 'Paiement ' . $paiement->id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-money-bill-wave"></i> Détail du Paiement
                    </h4>
                    <div>
                        <a href="{{ route('paiements.edit', $paiement->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('paiements.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Informations du paiement -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Informations du Paiement</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>ID:</strong></td>
                                            <td>#{{ $paiement->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date du paiement:</strong></td>
                                            <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Montant:</strong></td>
                                            <td>
                                                <h4 class="text-success mb-0">
                                                    {{ number_format($paiement->montant, 0, ',', ' ') }} €
                                                </h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Mode de paiement:</strong></td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $paiement->mode_paiement_libelle }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Référence:</strong></td>
                                            <td>
                                                @if($paiement->reference)
                                                {{ $paiement->reference }}
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Statut:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $paiement->statut == 'complet' ? 'success' : ($paiement->statut == 'partiel' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($paiement->statut) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Informations de la facture -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Facture Associée</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>N° Facture:</strong></td>
                                            <td>
                                                <strong>
                                                    <a href="{{ route('suivi-factures.show', $paiement->facture->id) }}" class="text-dark">
                                                        {{ $paiement->facture->numero_facture }}
                                                    </a>
                                                </strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Client:</strong></td>
                                            <td>
                                                <a href="{{ route('clients.show', $paiement->facture->client->id) }}" class="text-dark">
                                                    {{ $paiement->facture->client->nom }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Montant total:</strong></td>
                                            <td>{{ number_format($paiement->facture->montant, 0, ',', ' ') }} €</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Montant payé:</strong></td>
                                            <td>
                                                <span class="text-success">
                                                    {{ number_format($paiement->facture->montant_paye, 0, ',', ' ') }} €
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Montant restant:</strong></td>
                                            <td>
                                                <span class="text-danger">
                                                    {{ number_format($paiement->facture->montant_restant, 0, ',', ' ') }} €
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Statut paiement:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $paiement->facture->statut_paiement == 'payé' ? 'success' : ($paiement->facture->statut_paiement == 'partiel' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($paiement->facture->statut_paiement) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($paiement->notes)
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Notes</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $paiement->notes }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Historique des paiements de la facture -->
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Historique des Paiements pour cette Facture</h5>
                        </div>
                        <div class="card-body">
                            @if($paiement->facture->paiements->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Montant</th>
                                            <th>Mode</th>
                                            <th>Référence</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paiement->facture->paiements->sortByDesc('date_paiement') as $p)
                                        <tr class="{{ $p->id == $paiement->id ? 'table-active' : '' }}">
                                            <td>{{ $p->date_paiement->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $p->id == $paiement->id ? 'success' : 'secondary' }}">
                                                    {{ number_format($p->montant, 0, ',', ' ') }} €
                                                </span>
                                            </td>
                                            <td>{{ $p->mode_paiement_libelle }}</td>
                                            <td>
                                                @if($p->reference)
                                                <small>{{ $p->reference }}</small>
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $p->statut == 'complet' ? 'success' : ($p->statut == 'partiel' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($p->statut) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> Aucun autre paiement pour cette facture.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-footer text-muted">
                    <small>
                        Paiement créé le {{ $paiement->created_at->format('d/m/Y à H:i') }}
                        @if($paiement->created_at != $paiement->updated_at)
                        - Modifié le {{ $paiement->updated_at->format('d/m/Y à H:i') }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
