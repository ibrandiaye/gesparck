@extends('layouts.app')

@section('title', 'Facture ' . $suiviFacture->numero_facture)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice-dollar"></i> Facture {{ $suiviFacture->numero_facture }}
                    </h4>
                    <div>
                        <a href="{{ route('suivi-factures.edit', $suiviFacture->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('suivi-factures.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Informations de la facture -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Informations de la Facture</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Numéro de facture:</strong></td>
                                            <td>
                                                <span class="badge badge-dark" style="font-size: 1.1em;">
                                                    {{ $suiviFacture->numero_facture }}
                                                </span>
                                            </td>
                                        </tr>
                                          <tr>
                                            <td><strong>Date Facture:</strong></td>
                                            <td>{{ $suiviFacture->date_facture->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date de livraison:</strong></td>
                                            <td>{{  $suiviFacture->date_livraison ? $suiviFacture->date_livraison->format('d/m/Y') : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Montant:</strong></td>
                                            <td>
                                                <h4 class="text-success mb-0">
                                                    {{ number_format($suiviFacture->montant, 0, ',', ' ') }} CFA
                                                </h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Mois de livraison:</strong></td>
                                            <td>{{ $suiviFacture->mois_livraison }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Informations du client -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Informations du Client</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Client:</strong></td>
                                            <td>
                                                <strong>
                                                    <a href="{{ route('clients.show', $suiviFacture->client->id) }}" class="text-dark">
                                                        {{ $suiviFacture->client->nom }}
                                                    </a>
                                                </strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Adresse:</strong></td>
                                            <td>{{ $suiviFacture->client->adresse }}</td>
                                        </tr>
                                        @if($suiviFacture->client->ville || $suiviFacture->client->code_postal)
                                        <tr>
                                            <td><strong>Localisation:</strong></td>
                                            <td>
                                                @if($suiviFacture->client->code_postal && $suiviFacture->client->ville)
                                                {{ $suiviFacture->client->code_postal }}, {{ $suiviFacture->client->ville }}
                                                @elseif($suiviFacture->client->ville)
                                                {{ $suiviFacture->client->ville }}
                                                @elseif($suiviFacture->client->code_postal)
                                                {{ $suiviFacture->client->code_postal }}
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @if($suiviFacture->client->telephone)
                                        <tr>
                                            <td><strong>Téléphone:</strong></td>
                                            <td>
                                                <i class="fas fa-phone text-muted"></i>
                                                {{ $suiviFacture->client->telephone }}
                                            </td>
                                        </tr>
                                        @endif
                                        @if($suiviFacture->client->email)
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>
                                                <i class="fas fa-envelope text-muted"></i>
                                                <a href="mailto:{{ $suiviFacture->client->email }}">{{ $suiviFacture->client->email }}</a>
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques du client -->
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Statistiques du Client</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $statsClient = $suiviFacture->client->statistiques;
                            @endphp
                            <div class="row text-center">
                                {{-- <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-primary mb-0">{{ $statsClient['total_trajets'] }}</h4>
                                        <small class="text-muted">Trajets effectués</small>
                                    </div>
                                </div> --}}
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-success mb-0">{{ $statsClient['total_factures'] }}</h4>
                                        <small class="text-muted">Total factures</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-warning mb-0">
                                            {{ number_format($statsClient['montant_total_factures'], 0, ',', ' ') }} CFA
                                        </h4>
                                        <small class="text-muted">Montant total</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-info mb-0">
                                            @if($statsClient['total_factures'] > 0)
                                            {{ number_format($statsClient['montant_total_factures'] / $statsClient['total_factures'], 0, ',', ' ') }} CFA
                                            @else
                                            0,00 CFA
                                            @endif
                                        </h4>
                                        <small class="text-muted">Moyenne/facture</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Paiements -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Suivi des Paiements</h5>
                        <div>
                            <a href="{{ route('paiements.create', ['facture_id' => $suiviFacture->id]) }}"
                            class="btn btn-light btn-sm">
                                <i class="fas fa-plus"></i> Nouveau Paiement
                            </a>
                            <a href="{{ route('paiements.by-facture', $suiviFacture->id) }}"
                            class="btn btn-outline-light btn-sm">
                                <i class="fas fa-list"></i> Voir Tous
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Barre de progression -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Progression du paiement</span>
                                <span>{{ number_format($suiviFacture->pourcentage_paiement, 1) }}%</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar
                                    @if($suiviFacture->statut_paiement == 'payé') bg-success
                                    @elseif($suiviFacture->statut_paiement == 'partiel') bg-warning
                                    @else bg-danger @endif"
                                    role="progressbar"
                                    style="width: {{ $suiviFacture->pourcentage_paiement }}%"
                                    aria-valuenow="{{ $suiviFacture->pourcentage_paiement }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small>Payé: {{ number_format($suiviFacture->montant_paye, 2, ',', ' ') }} €</small>
                                <small>Restant: {{ number_format($suiviFacture->montant_restant, 2, ',', ' ') }} €</small>
                                <small>Total: {{ number_format($suiviFacture->montant, 2, ',', ' ') }} €</small>
                            </div>
                        </div>

                        <!-- Derniers paiements -->
                        @php
                            $derniersPaiements = $suiviFacture->paiements()
                                                            ->orderBy('date_paiement', 'desc')
                                                            ->limit(5)
                                                            ->get();
                        @endphp

                        @if($derniersPaiements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Montant</th>
                                        <th>Mode</th>
                                        <th>Référence</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($derniersPaiements as $paiement)
                                    <tr>
                                        <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge badge-success">
                                                {{ number_format($paiement->montant, 2, ',', ' ') }} €
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $paiement->mode_paiement_libelle }}</span>
                                        </td>
                                        <td>
                                            @if($paiement->reference)
                                            <small>{{ $paiement->reference }}</small>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $paiement->statut == 'complet' ? 'success' : 'warning' }}">
                                                {{ $paiement->statut }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('paiements.show', $paiement->id) }}"
                                            class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle"></i> Aucun paiement enregistré pour cette facture.
                        </div>
                        @endif

                        <!-- Résumé des paiements -->
                        <div class="mt-3 p-3 bg-light rounded">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <h5 class="text-primary">{{ $suiviFacture->paiements->count() }}</h5>
                                    <small class="text-muted">Nombre de paiements</small>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="text-success">{{ number_format($suiviFacture->montant_paye, 2, ',', ' ') }} €</h5>
                                    <small class="text-muted">Total payé</small>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="text-{{ $suiviFacture->statut_paiement == 'payé' ? 'success' : 'danger' }}">
                                        {{ ucfirst($suiviFacture->statut_paiement) }}
                                    </h5>
                                    <small class="text-muted">Statut</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Retour -->
                @if($suiviFacture->a_retour)
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-undo"></i> Retour sur Facture
                        </h5>
                        <form action="{{ route('suivi-factures.annuler-retour', $suiviFacture->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-dark btn-sm"
                                    onclick="return confirm('Êtes-vous sûr de vouloir annuler ce retour ?')">
                                <i class="fas fa-times"></i> Annuler le retour
                            </button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Montant du retour:</strong><br>
                                <h4 class="text-danger">{{ number_format($suiviFacture->montant_retour, 2, ',', ' ') }} €</h4>
                            </div>
                            <div class="col-md-4">
                                <strong>Date du retour:</strong><br>
                                {{ $suiviFacture->date_retour->format('d/m/Y') }}
                            </div>
                            <div class="col-md-4">
                                <strong>Nouveau montant net:</strong><br>
                                <h4 class="text-success">{{ number_format($suiviFacture->montant_net, 2, ',', ' ') }} €</h4>
                            </div>
                        </div>
                        @if($suiviFacture->raison_retour)
                        <div class="mt-3">
                            <strong>Raison:</strong><br>
                            <p class="mb-0">{{ $suiviFacture->raison_retour }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <div class="card mb-4">
                    <div class="card-header bg-light text-dark d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-undo"></i> Gestion des Retours
                        </h5>
                        <a href="{{ route('suivi-factures.retour', $suiviFacture->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-plus"></i> Ajouter un retour
                        </a>
                    </div>
                    <div class="card-body text-center">
                        <p class="text-muted mb-0">Aucun retour enregistré sur cette facture.</p>
                    </div>
                </div>
                @endif

                <div class="card-footer text-muted">
                    <small>
                        Facture créée le {{ $suiviFacture->created_at->format('d/m/Y à H:i') }}
                        @if($suiviFacture->created_at != $suiviFacture->updated_at)
                        - Modifiée le {{ $suiviFacture->updated_at->format('d/m/Y à H:i') }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
