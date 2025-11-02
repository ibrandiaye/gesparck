@extends('layouts.app')

@section('title', 'Statistiques des Trajets')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-bar"></i> Statistiques des Trajets
                    </h4>
                    <div>
                        <a href="{{ route('trips.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour aux trajets
                        </a>
                    </div>
                </div>
                      <!-- Filtres -->
                <div class="card-body bg-light">
                    <form method="GET" action="{{ route('trips.statistics') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Date début</label>
                                <input type="date" name="date_debut" class="form-control"
                                    value="{{ request('date_debut') }}">
                            </div>
                            <div class="col-md-4">
                                <label>Date fin</label>
                                <input type="date" name="date_fin" class="form-control"
                                    value="{{ request('date_fin') }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-filter"></i> Filtrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <!-- Statistiques par motif -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Répartition par Motif</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Motif</th>
                                                    <th>Nombre de trajets</th>
                                                    <th>Total des voyages</th>
                                                     {{-- <th>Distance moyenne</th>  --}}
                                                    <th>Pourcentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $totalTrajets = $statsByMotif->sum('nombre_trajets');
                                                    $totalVoyages = $statsByMotif->sum('total_voyages');
                                                @endphp
                                                @foreach($statsByMotif as $stat)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-{{ $stat->motif == 'livraison' ? 'primary' :
                                                            ($stat->motif == 'client' ? 'success' :
                                                            ($stat->motif == 'maintenance' ? 'warning' : 'secondary')) }}">
                                                            {{ ucfirst($stat->motif) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $stat->nombre_trajets }}</td>
                                                    <td>{{ $stat->total_voyages }}</td>
                                                    {{-- <td>{{ number_format($stat->distance_moyenne, 1) }} km</td> --}}
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar"
                                                                 role="progressbar"
                                                                 style="width: {{ ($stat->nombre_trajets / $totalTrajets) * 100 }}%"
                                                                 aria-valuenow="{{ ($stat->nombre_trajets / $totalTrajets) * 100 }}"
                                                                 aria-valuemin="0"
                                                                 aria-valuemax="100">
                                                                {{ number_format(($stat->nombre_trajets / $totalTrajets) * 100, 1) }}%
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="font-weight-bold">
                                                <tr>
                                                    <td>Total</td>
                                                    <td>{{ $totalTrajets }}</td>
                                                    <td>{{ $totalVoyages }}</td>
                                                    <td>-</td>
                                                    <td>100%</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top destinations -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Top 10 des Destinations</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Destination</th>
                                                    <th>Visites</th>
                                                    <th>Total trajets</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($topDestinations as $destination)
                                                <tr>
                                                    <td>{{ $destination->destination }}</td>
                                                    <td>{{ $destination->nombre_visites }}</td>
                                                    <td>{{ $destination->total_trajets }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Véhicules les plus utilisés -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Véhicules les Plus Utilisés</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Véhicule</th>
                                                    <th>Trajets</th>
                                                    <th>Total voyages</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($topVehicles as $vehicle)
                                                <tr>
                                                    <td>
                                                        {{ $vehicle->vehicle->immatriculation }}<br>
                                                        <small class="text-muted">{{ $vehicle->vehicle->modele }}</small>
                                                    </td>
                                                    <td>{{ $vehicle->nombre_trajets }}</td>
                                                    <td>{{ $vehicle->total_voyages }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques mensuelles -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">Évolution Mensuelle (12 derniers mois)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Mois</th>
                                                    <th>Nombre de trajets</th>
                                                    <th>Total voyages</th>
                                                   {{--  <th>Distance totale</th>
                                                    <th>Moyenne trajet/jour</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($monthlyStats as $stat)
                                                @php
                                                    $date = \Carbon\Carbon::create($stat->annee, $stat->mois, 1);
                                                    $daysInMonth = $date->daysInMonth;
                                                    $trajetsParJour = $stat->nombre_trajets / $daysInMonth;
                                                @endphp
                                                <tr>
                                                    <td>{{ $date->locale('fr')->monthName }} {{ $stat->annee }}</td>
                                                    <td>{{ $stat->nombre_trajets }}</td>
                                                    <td>{{ $stat->total_voyages }}</td>
                                                   {{--  <td>{{ number_format($stat->distance_totale, 0, ',', ' ') }} km</td>
                                                    <td>{{ number_format($trajetsParJour, 1) }}</td> --}}
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
