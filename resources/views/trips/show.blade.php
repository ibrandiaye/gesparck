@extends('layouts.app')

@section('title', 'Détail du Trajet')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-route"></i> Détail du Trajet
                    </h4>
                    <div>
                        <a href="{{ route('trips.edit', $trip->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('trips.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Informations principales -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Informations du Trajet</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Date:</strong></td>
                                            <td>{{ $trip->date_trajet->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Destination:</strong></td>
                                            <td>{{ $trip->destination }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Motif:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $trip->motif == 'livraison' ? 'primary' :
                                                    ($trip->motif == 'client' ? 'success' :
                                                    ($trip->motif == 'maintenance' ? 'warning' : 'secondary')) }}">
                                                    {{ ucfirst($trip->motif) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nombre de trajets:</strong></td>
                                            <td>
                                                <span class="badge badge-info badge-pill">
                                                    {{ $trip->nombre_trajets }}
                                                </span>
                                            </td>
                                        </tr>
                                      {{--   <tr>
                                            <td><strong>Distance par trajet:</strong></td>
                                            <td>{{ $trip->distance_moyenne }} km</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Distance totale:</strong></td>
                                            <td><strong class="text-primary">{{ $trip->distance_totale }} km</strong></td>
                                        </tr> --}}
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Informations véhicule et kilométrage -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Véhicule & Kilométrage</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Véhicule:</strong></td>
                                            <td>
                                                {{ $trip->vehicle->immatriculation }}<br>
                                                <small class="text-muted">{{ $trip->vehicle->modele }}</small>
                                            </td>
                                        </tr>
                                       {{--   <tr>
                                            <td><strong>Conducteur:</strong></td>
                                            <td>{{ $trip->vehicle->conducteur ?? 'Non assigné' }}</td>
                                        </tr>
                                       <tr>
                                            <td><strong>KM Départ:</strong></td>
                                            <td>{{ number_format($trip->km_depart, 0, ',', ' ') }} km</td>
                                        </tr>
                                        <tr>
                                            <td><strong>KM Arrivée:</strong></td>
                                            <td>{{ number_format($trip->km_arrivee, 0, ',', ' ') }} km</td>
                                        </tr> --}}
                                        <tr>
                                            <td><strong>Plein associé:</strong></td>
                                            <td>
                                                {{ $trip->fuelEntry->date_remplissage->format('d/m/Y') }} -
                                                {{ $trip->fuelEntry->litres }}L
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($trip->notes)
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Notes</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $trip->notes }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Statistiques rapides -->
                    <div class="card mt-4">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Statistiques</h5>
                        </div>
                        <div class="card-body">
                            {{-- <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="border rounded p-3">
                                        <h3 class="text-primary">{{ $trip->distance_moyenne }} km</h3>
                                        <small class="text-muted">Distance moyenne/trajet</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3">
                                        <h3 class="text-success">{{ $trip->distance_totale }} km</h3>
                                        <small class="text-muted">Distance totale</small>
                                    </div>
                                </div> --}}
                                <div class="col-md-4">
                                    <div class="border rounded p-3">
                                        <h3 class="text-info">{{ $trip->nombre_trajets }}</h3>
                                        <small class="text-muted">Nombre de trajets</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-muted">
                    <small>
                        Créé le {{ $trip->created_at->format('d/m/Y à H:i') }}
                        @if($trip->created_at != $trip->updated_at)
                        - Modifié le {{ $trip->updated_at->format('d/m/Y à H:i') }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
