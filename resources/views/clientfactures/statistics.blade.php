@extends('layouts.app')

@section('title', 'Statistiques Clients')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-pie"></i> Statistiques des Clients
                    </h4>
                    <div>
                        <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour aux clients
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Cartes de statistiques globales -->
                    <div class="row mb-4">
                        @php
                            $totalClients = App\Models\Client::count();
                            //$clientsActifs = App\Models\Client::where('actif', true)->count();
                            $totalTrajetsClients = App\Models\Trip::whereNotNull('client_id')->count();
                            $totalVoyagesClients = App\Models\Trip::whereNotNull('client_id')->sum('nombre_trajets');
                            $clientsAvecTrajets = App\Models\Client::has('trips')->count();
                        @endphp

                        <div class="col-md-2 col-6 mb-3">
                            <div class="card bg-primary text-white text-center">
                                <div class="card-body py-3">
                                    <h3 class="mb-0">{{ $totalClients }}</h3>
                                    <small>Total Clients</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 col-6 mb-3">
                            <div class="card bg-info text-white text-center">
                                <div class="card-body py-3">
                                    <h3 class="mb-0">{{ $clientsAvecTrajets }}</h3>
                                    <small>Avec Trajets</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="card bg-warning text-dark text-center">
                                <div class="card-body py-3">
                                    <h3 class="mb-0">{{ $totalTrajetsClients }}</h3>
                                    <small>Total Trajets</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="card bg-danger text-white text-center">
                                <div class="card-body py-3">
                                    <h3 class="mb-0">{{ $totalVoyagesClients }}</h3>
                                    <small>Total Voyages</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="card bg-dark text-white text-center">
                                <div class="card-body py-3">
                                    <h3 class="mb-0">
                                        @if($totalClients > 0)
                                        {{ number_format($totalTrajetsClients / $totalClients, 1) }}
                                        @else
                                        0
                                        @endif
                                    </h3>
                                    <small>Moyenne/Client</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top clients -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Top 10 des Clients les Plus Visités</h5>
                                    <span class="badge badge-light">{{ $topClients->sum('total_voyages') }} voyages total</span>
                                </div>
                                <div class="card-body">
                                    @if($topClients->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Client</th>
                                                    <th>Ville</th>
                                                    <th class="text-center">Trajets</th>
                                                    <th class="text-center">Voyages</th>
                                                    <th width="25%">Répartition</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $totalVoyages = $topClients->sum('total_voyages');
                                                @endphp
                                                @foreach($topClients as $index => $client)
                                                <tr>
                                                    <td>
                                                        <span class="badge badge-{{ $index < 3 ? 'warning' : 'secondary' }}">
                                                            {{ $index + 1 }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong>
                                                            <a href="{{ route('clients.show', $client->id) }}" class="text-dark">
                                                                {{ $client->nom }}
                                                            </a>
                                                        </strong>
                                                        @if(!$client->actif)
                                                        <span class="badge badge-secondary ml-1">Inactif</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $client->ville ?? '-' }}</td>
                                                    <td class="text-center">
                                                        <span class="badge badge-primary badge-pill">{{ $client->total_trajets }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <strong class="text-primary">{{ $client->total_voyages }}</strong>
                                                    </td>
                                                    <td>
                                                        @if($totalVoyages > 0)
                                                        @php
                                                            $percentage = ($client->total_voyages / $totalVoyages) * 100;
                                                            $color = $index < 3 ? 'bg-success' : 'bg-info';
                                                        @endphp
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar {{ $color }}"
                                                                 role="progressbar"
                                                                 style="width: {{ $percentage }}%"
                                                                 aria-valuenow="{{ $percentage }}"
                                                                 aria-valuemin="0"
                                                                 aria-valuemax="100">
                                                                {{ number_format($percentage, 1) }}%
                                                            </div>
                                                        </div>
                                                        @else
                                                        <div class="text-muted">-</div>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-info-circle"></i> Aucun client avec des trajets enregistrés.
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>


                    </div>



                    <!-- Clients sans trajets -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">Clients Actifs Sans Trajets</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $clientsSansTrajets = App\Models\Client::doesntHave('trips')
                                                                            ->orderBy('nom')
                                                                            ->get();
                                    @endphp

                                    @if($clientsSansTrajets->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Client</th>
                                                    <th>Adresse</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($clientsSansTrajets as $client)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('clients.show', $client->id) }}" class="text-dark">
                                                            {{ $client->nom }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $client->ville ?? '-' }}</td>

                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="alert alert-success text-center">
                                        <i class="fas fa-check-circle"></i> Tous les clients actifs ont au moins un trajet !
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Derniers trajets clients -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0">Derniers Trajets Clients</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $derniersTrajets = App\Models\Trip::with(['client', 'vehicle'])
                                                                        ->whereNotNull('client_id')
                                                                        ->orderBy('date_trajet', 'desc')
                                                                        ->limit(10)
                                                                        ->get();
                                    @endphp

                                    @if($derniersTrajets->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Client</th>
                                                    <th>Véhicule</th>
                                                    <th>Destination</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($derniersTrajets as $trajet)
                                                <tr>
                                                    <td>
                                                        <small>{{ $trajet->date_trajet->format('d/m/Y') }}</small>
                                                    </td>
                                                    <td>
                                                        <strong>
                                                            <a href="{{ route('clients.show', $trajet->client->id) }}" class="text-dark">
                                                                {{ $trajet->client->nom }}
                                                            </a>
                                                        </strong>
                                                    </td>
                                                    <td>
                                                        <small>{{ $trajet->vehicle->immatriculation }}</small>
                                                    </td>
                                                    <td>
                                                        <small>{{ $trajet->destination }}</small>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-info-circle"></i> Aucun trajet client enregistré.
                                    </div>
                                    @endif
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

@push('scripts')

@endpush

@push('styles')
<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.badge-pill {
    border-radius: 10rem;
}

.progress {
    border-radius: 10px;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.alert {
    border: none;
    border-radius: 10px;
}
</style>
@endpush
