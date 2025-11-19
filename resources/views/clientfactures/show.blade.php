@extends('layouts.app')

@section('title', $client->nom)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user"></i> {{ $client->nom }}
                        @if(!$client->actif)
                        <span class="badge badge-secondary ml-2">Inactif</span>
                        @endif
                    </h4>
                    <div>
                        <a href="{{ route('clientfactures.edit', $client->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('clientfactures.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Informations du client -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Informations du Client</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%"><strong>Nom:</strong></td>
                                            <td>{{ $client->nom }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Adresse:</strong></td>
                                            <td>{{ $client->adresse }}</td>
                                        </tr>

                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Statistiques -->

                    </div>



                    <!-- Historique des trajets -->
                   {{--  <div class="card">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Historique des Trajets</h5>
                            <a href="{{ route('trips.create', ['client_id' => $client->id]) }}"
                               class="btn btn-light btn-sm">
                                <i class="fas fa-plus"></i> Nouveau Trajet
                            </a>
                        </div>
                        <div class="card-body">
                            @if($trips->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Véhicule</th>
                                            <th>Destination</th>
                                            <th>Motif</th>
                                            <th>Nb Trajets</th>
                                            <th>Distance</th>
                                            <th>Conducteur</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($trips as $trip)
                                        <tr>
                                            <td>{{ $trip->date_trajet->format('d/m/Y') }}</td>
                                            <td>
                                                <strong>{{ $trip->vehicle->immatriculation }}</strong>
                                                <br><small class="text-muted">{{ $trip->vehicle->modele }}</small>
                                            </td>
                                            <td>{{ $trip->destination }}</td>
                                            <td>
                                                <span class="badge bg-{{ $trip->motif == 'livraison' ? 'primary' :
                                                    ($trip->motif == 'client' ? 'success' :
                                                    ($trip->motif == 'maintenance' ? 'warning' : 'secondary')) }}">
                                                    {{ ucfirst($trip->motif) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $trip->nombre_trajets }}</span>
                                            </td>
                                            <td>{{ $trip->distance_totale }} km</td>
                                            <td>{{ $trip->vehicle->conducteur ?? 'Non assigné' }}</td>
                                            <td>
                                                <a href="{{ route('trips.show', $trip->id) }}"
                                                   class="btn btn-info btn-sm" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $trips->links() }}
                            </div>
                            @else
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> Aucun trajet enregistré pour ce client.
                                <br>
                                <a href="{{ route('trips.create', ['client_id' => $client->id]) }}"
                                   class="btn btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Créer le premier trajet
                                </a>
                            </div>
                            @endif
                        </div>
                    </div> --}}
                </div>

                <div class="card-footer text-muted">
                    <small>
                        Client créé le {{ $client->created_at->format('d/m/Y à H:i') }}
                        @if($client->created_at != $client->updated_at)
                        - Modifié le {{ $client->updated_at->format('d/m/Y à H:i') }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
        <!-- Factures du client -->
<div class="card mb-4">
    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Factures du Client</h5>
        <div>
            <a href="{{ route('suivi-factures.create', ['client_id' => $client->id]) }}"
               class="btn btn-dark btn-sm">
                <i class="fas fa-plus"></i> Nouvelle Facture
            </a>
            <a href="{{ route('suivi-factures.by-client', $client->id) }}"
               class="btn btn-outline-dark btn-sm">
                <i class="fas fa-list"></i> Voir Toutes
            </a>
        </div>
    </div>
    <div class="card-body">
        @php
            $dernieresFactures = $client->factures()
                                       ->orderBy('date_livraison', 'desc')
                                       ->limit(5)
                                       ->get();
        @endphp

        @if($dernieresFactures->count() > 0)
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>N° Facture</th>
                        <th>Date Livraison</th>
                        <th>Montant</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dernieresFactures as $facture)
                    <tr>
                        <td>
                            <strong>{{ $facture->numero_facture }}</strong>
                        </td>
                        <td>{{ $facture->date_livraison->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge badge-success">
                                {{ number_format($facture->montant, 2, ',', ' ') }} CFA
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('suivi-factures.show', $facture->id) }}"
                               class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3 p-3 bg-light rounded">
            <div class="row text-center">
                <div class="col-md-6">
                    <h5 class="text-primary">{{ $statistiques['total_factures'] }}</h5>
                    <small class="text-muted">Total Factures</small>
                </div>
                <div class="col-md-6">
                    <h5 class="bg-success">{{ number_format($statistiques['montant_total_factures'], 2, ',', ' ') }} CFA</h5>
                    <small class="text-muted">Montant Total</small>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i> Aucune facture enregistrée pour ce client.
            <br>
            <a href="{{ route('suivi-factures.create', ['client_id' => $client->id]) }}"
               class="btn btn-primary mt-2">
                <i class="fas fa-plus"></i> Créer la première facture
            </a>
        </div>
        @endif
    </div>
</div>
    </div>
</div>
@endsection
