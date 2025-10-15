@extends('layouts.app')

@section('title', 'Trajets du Plein')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-gas-pump"></i> Trajets pour le plein du {{ $fuelEntry->date->format('d/m/Y') }}
                    </h4>
                    <div>
                        <a href="{{ route('trips.create', ['fuel_entry_id' => $fuelEntry->id]) }}"
                           class="btn btn-primary">
                            <i class="fas fa-plus"></i> Ajouter un Trajet
                        </a>
                        <a href="{{ route('fuel-entries.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour aux pleins
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Informations du plein -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <strong>Véhicule:</strong><br>
                                        {{ $fuelEntry->vehicle->immatriculation }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Quantité:</strong><br>
                                        {{ $fuelEntry->litres }} litres
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Coût:</strong><br>
                                        {{ number_format($fuelEntry->cout, 2, ',', ' ') }} €
                                    </div>
                                    <div class="col-md-3">
                                        <strong>KM au plein:</strong><br>
                                        {{ number_format($fuelEntry->kilometrage_plein, 0, ',', ' ') }} km
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <h3 class="text-primary">{{ $stats['total_trajets'] }}</h3>
                                            <small class="text-muted">Total des trajets</small>
                                        </div>
                                        <div class="col-md-4">
                                            <h3 class="text-success">{{ $stats['distance_totale'] }} km</h3>
                                            <small class="text-muted">Distance totale</small>
                                        </div>
                                        <div class="col-md-4">
                                            <h3 class="text-info">{{ number_format($stats['consommation_moyenne'], 1) }} L/100km</h3>
                                            <small class="text-muted">Consommation moyenne</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des trajets -->
                    @if($trips->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Destination</th>
                                    <th>Motif</th>
                                    <th>Nb Trajets</th>
                                   {{--  <th>KM Départ</th>
                                    <th>KM Arrivée</th>
                                    <th>Distance/Trajet</th>
                                    <th>Distance Total</th> --}}
                                    <th>Conducteur</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trips as $trip)
                                <tr>
                                    <td>{{ $trip->date_trajet->format('d/m/Y') }}</td>
                                    <td>{{ $trip->destination }}</td>
                                    <td>
                                        <span class="badge badge-{{ $trip->motif == 'livraison' ? 'primary' :
                                            ($trip->motif == 'client' ? 'success' :
                                            ($trip->motif == 'maintenance' ? 'warning' : 'secondary')) }}">
                                            {{ ucfirst($trip->motif) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $trip->nombre_trajets }}</span>
                                    </td>
                                  {{--   <td>{{ number_format($trip->km_depart, 0, ',', ' ') }}</td>
                                    <td>{{ number_format($trip->km_arrivee, 0, ',', ' ') }}</td>
                                    <td>{{ $trip->distance_moyenne }} km</td>
                                    <td><strong>{{ $trip->distance_totale }} km</strong></td> --}}
                                    <td>{{ $trip->vehicle->conducteur ?? 'Non assigné' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('trips.show', $trip->id) }}"
                                               class="btn btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('trips.edit', $trip->id) }}"
                                               class="btn btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        Aucun trajet enregistré pour ce plein.
                        <br>
                        <a href="{{ route('trips.create', ['fuel_entry_id' => $fuelEntry->id]) }}"
                           class="btn btn-primary mt-2">
                            <i class="fas fa-plus"></i> Ajouter le premier trajet
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
