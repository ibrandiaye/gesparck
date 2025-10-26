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
                        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary">
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
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Statistiques</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-6 mb-3">
                                            <div class="border rounded p-3 bg-light">
                                                <h2 class="text-primary mb-0">{{ $statistiques['total_trajets'] }}</h2>
                                                <small class="text-muted">Trajets effectués</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="border rounded p-3 bg-light">
                                                <h2 class="text-info mb-0">{{ $statistiques['total_voyages'] }}</h2>
                                                <small class="text-muted">Total voyages</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 bg-light">
                                                <h2 class="text-success mb-0">{{ $statistiques['distance_totale'] }} km</h2>
                                                <small class="text-muted">Distance totale</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 bg-light">
                                                <h2 class="text-warning mb-0">
                                                    @if($statistiques['total_trajets'] > 0)
                                                    {{ number_format($statistiques['distance_totale'] / $statistiques['total_trajets'], 1) }}
                                                    @else
                                                    0
                                                    @endif km
                                                </h2>
                                                <small class="text-muted">Moyenne/trajet</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($client->notes)
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Notes</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $client->notes }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Historique des trajets -->
                    <div class="card">
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
                                                <span class="badge badge-{{ $trip->motif == 'livraison' ? 'primary' :
                                                    ($trip->motif == 'client' ? 'success' :
                                                    ($trip->motif == 'maintenance' ? 'warning' : 'secondary')) }}">
                                                    {{ ucfirst($trip->motif) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info">{{ $trip->nombre_trajets }}</span>
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
                    </div>
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
    </div>
</div>
@endsection
