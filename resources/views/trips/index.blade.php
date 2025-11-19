@extends('layouts.app')

@section('title', 'Liste des Trajets')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-route"></i> Liste des Trajets
                    </h4>
                    <div>
                        <a href="{{ route('trips.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau Trajet
                        </a>
                        <a href="{{ route('trips.statistics') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> Statistiques
                        </a>
                        <a href="{{ route('trips.export') }}" class="btn btn-success">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="card-body bg-light">
                    <form method="GET" action="{{ route('trips.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Véhicule</label>
                                <select name="vehicle_id" class="form-control select2">
                                    <option value="">Tous les véhicules</option>
                                    @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}"
                                        {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->immatriculation }} - {{ $vehicle->modele }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Motif</label>
                                <select name="motif" class="form-control">
                                    <option value="">Tous les motifs</option>
                                    @foreach($motifs as $motif)
                                    <option value="{{ $motif }}"
                                        {{ request('motif') == $motif ? 'selected' : '' }}>
                                        {{ ucfirst($motif) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                          {{--   <div class="col-md-2">
                                <label>Avec clients</label>
                                <select name="avec_clients" class="form-control">
                                    <option value="">Tous</option>
                                    <option value="1" {{ request('avec_clients') == '1' ? 'selected' : '' }}>Avec clients</option>
                                    <option value="0" {{ request('avec_clients') == '0' ? 'selected' : '' }}>Sans clients</option>
                                </select>
                            </div> --}}
                            <div class="col-md-2">
                                <label>Date début</label>
                                <input type="date" name="date_debut" class="form-control"
                                    value="{{ request('date_debut') }}">
                            </div>
                            <div class="col-md-2">
                                <label>Date fin</label>
                                <input type="date" name="date_fin" class="form-control"
                                    value="{{ request('date_fin') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-filter"></i> Filtrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    @endif

                    @if($trips->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tripTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Nom du trajet</th>
                                    <th>Véhicule</th>
                                    <th>Clients</th>
                                    <th>Destination</th>
                                    <th>Motif</th>
                                    <th>Nb Trajets</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trips as $trip)
                                <tr>
                                    <td>{{ $trip->date_trajet->format('d/m/Y') }}</td>
                                    <td>
                                        @if($trip->nom_trajet)
                                        <strong>{{ $trip->nom_trajet }}</strong>
                                        @if($trip->description)
                                        <br><small class="text-muted">{{ Str::limit($trip->description, 30) }}</small>
                                        @endif
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $trip->vehicle->immatriculation }}</strong>
                                        <br><small class="text-muted">{{ $trip->vehicle->modele }}</small>
                                    </td>
                                    <td>
                                        @if($trip->clients->count() > 0)
                                        <div class="client-list">
                                            @foreach($trip->clients->take(2) as $client)
                                            <span class="badge bg-primary mb-1">
                                                {{ $client->nom }}
                                                @if($client->pivot->ordre_visite > 1)
                                                <small>(#{{ $client->pivot->ordre_visite }})</small>
                                                @endif
                                            </span>
                                            @endforeach
                                            @if($trip->clients->count() > 2)
                                            <span class="badge bg-secondary">
                                                +{{ $trip->clients->count() - 2 }} autres
                                            </span>
                                            @endif
                                        </div>
                                        @else
                                        <span class="text-muted">Aucun client</span>
                                        @endif
                                    </td>
                                    <td>{{ $trip->destination }}</td>
                                    <td>
                                        <span class="badge bg-{{ $trip->motif == 'livraison' ? 'primary' :
                                            ($trip->motif == 'client' ? 'success' :
                                            ($trip->motif == 'maintenance' ? 'warning' : 'secondary')) }}">
                                            {{ ucfirst($trip->motif) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $trip->nombre_trajets }}</span>
                                    </td>
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
                                            <form action="{{ route('trips.destroy', $trip->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"
                                                        title="Supprimer"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce trajet ?')">
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
                    {{-- <div class="d-flex justify-content-center mt-4">
                        {{ $trips->links() }}
                    </div> --}}
                    @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Aucun trajet trouvé.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
$('#tripTable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/fr-FR.json',
        },
            ordering:true,
            order: [[0, 'desc']],
             lengthMenu: [10, 25, 50, 100, 200,500,1000,5000,10000]

    });
    </script>
@endpush
