@extends('layouts.app')

@section('title', 'Historique Carburant')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-gas-pump"></i> Gestion du Carburant</h1>
    <a href="{{ route('fuel-entries.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouveau Remplissage
    </a>
</div>

<!-- Filtres -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Filtres</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('fuel-entries.index') }}">
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="vehicle" class="form-label">Véhicule</label>
                    <select name="vehicle" id="vehicle" class="form-select select2">
                        <option value="">Tous les véhicules</option>
                        @foreach($filters['vehicles'] as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ $filters['selectedVehicle'] == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->immatriculation }} - {{ $vehicle->marque }} {{ $vehicle->modele }}
                            </option>
                        @endforeach
                    </select>
                </div>

               <div class="col-md-2">
                    <label for="type_carburant" class="form-label">Type Carburant</label>
                    <select name="type_carburant" id="type_carburant" class="form-select">
                        <option value="">Tous les types</option>
                        @foreach($filters['typesCarburant'] as $type)
                            <option value="{{ $type }}" {{ $filters['selectedTypeCarburant'] == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                 {{-- <div class="col-md-2">
                    <label for="month" class="form-label">Mois</label>
                    <select name="month" id="month" class="form-select">
                        <option value="">Tous les mois</option>
                        @foreach($filters['months'] as $key => $month)
                            <option value="{{ $key }}" {{ $filters['selectedMonth'] == $key ? 'selected' : '' }}>
                                {{ $month }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}
                <div class="col-md-2">
                    <label for="date_debut" class="form-label">Date de début</label>
                    <input type="date" class="form-control" id="date_debut" name="date_debut"
                        value="{{ request('date_debut') }}">
                </div>

                <div class="col-md-2">
                    <label for="date_fin" class="form-label">Date de fin</label>
                    <input type="date" class="form-control" id="date_fin" name="date_fin"
                        value="{{ request('date_fin') }}">
                </div>


              {{--   <div class="col-md-2">
                    <label for="year" class="form-label">Année</label>
                    <select name="year" id="year" class="form-select">
                        <option value="">Toutes les années</option>
                        @foreach($filters['years'] as $year)
                            <option value="{{ $year }}" {{ $filters['selectedYear'] == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}

                <div class="col-md-2">
                    <label for="station" class="form-label">Station</label>
                   {{--  <input type="text" name="station" id="station" class="form-control"
                           value="{{ $filters['selectedStation'] }}" placeholder="Nom de station"> --}}
                    <select class="form-control"  id="station" name="station" >
                            <option value="">Selectionner</option>
                            <option value="Mobile castors" {{ $filters['selectedStation']  == "Mobile castors" ? "selected" : " " }}>Mobile castors</option>
                            <option value="Total Yarakh"  {{$filters['selectedStation'] == "Total Yarakh" ? "selected" : " " }}>Total Yarakh</option>
                                <option value="Autre Station"  {{ $filters['selectedStation'] == "Autre Station" ? "selected" : " " }}>Autre Station</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <a href="{{ route('fuel-entries.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Cartes de statistiques -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ number_format($stats['totalCoutMois'], 0, ',', ' ') }} FCFA</h4>
                        <p>Coût total</p>
                    </div>
                    <i class="fas fa-money-bill-wave fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ number_format($stats['totalLitresMois'], 1, ',', ' ') }} L</h4>
                        <p>Litres total</p>
                    </div>
                    <i class="fas fa-oil-can fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ number_format($stats['moyennePrixLitre'] ?? 0, 0, ',', ' ') }} FCFA/L</h4>
                        <p>Prix moyen du litre</p>
                    </div>
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tableau des remplissages -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-history"></i> Historique des Remplissages</h5>
        <span class="badge bg-primary">
            {{-- {{ $fuelEntries->total() }}  --}}

           {{ count($fuelEntries) }}  résultat(s)
        </span>
    </div>
    <div class="card-body">
        @if($fuelEntries->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="tab_fuel_entrie">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Véhicule</th>
                        <th>Type Carburant</th>
                        <th>Litres</th>
                        <th>Prix/L</th>
                        <th>Coût Total</th>
                        <th>Km</th>
                        <th>Nombre de trajets</th>
                        <th>Station</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fuelEntries as $entry)
                    <tr>
                        <td>{{ $entry->date_remplissage->format('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $entry->vehicle->immatriculation }}</strong><br>
                            <small class="text-muted">{{ $entry->vehicle->marque }} {{ $entry->vehicle->modele }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $entry->type_carburant }}</span>
                        </td>
                        <td>{{ number_format($entry->litres, 1, ',', ' ') }} L</td>
                        <td>{{ number_format($entry->prix_litre, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <span class="badge bg-primary">
                                {{ number_format($entry->cout_total, 0, ',', ' ') }} FCFA
                            </span>
                        </td>
                        <td>{{ number_format($entry->kilometrage, 0, ',', ' ') }} km</td>
                        <td><span class="badge bg-primary">{{ $entry->nombreTotalTrajets }}</span></td>
                        <td>{{ $entry->station  }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                 <a href="{{ route('trips.create') }}?vehicle_id={{ $entry->vehicle_id }}&entry_id={{ $entry->id }}" class="btn btn-outline-primary" title="Ajouter Trajet">
                                    <i class="fas fa-route"></i>
                                </a>
                                <a href="{{ route('fuel-entries.show', $entry) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('fuel-entries.edit', $entry) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if (Auth::user()->role=="admin")
                                            <form action="{{ route('fuel-entries.destroy', $entry) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger"
                                                        onclick="return confirm('Supprimer ce remplissage?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

      {{--   <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Affichage de {{ $fuelEntries->firstItem() }} à {{ $fuelEntries->lastItem() }} sur {{ $fuelEntries->total() }} résultats
            </div>
            <div>
                {{ $fuelEntries->links() }}
            </div>
        </div> --}}
        @else
        <div class="text-center py-4">
            <i class="fas fa-gas-pump fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Aucun remplissage trouvé</h5>
            <p class="text-muted">Aucun résultat ne correspond à vos critères de recherche.</p>
            <a href="{{ route('fuel-entries.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-redo"></i> Réinitialiser les filtres
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Réinitialisation des filtres
    document.getElementById('resetFilters').addEventListener('click', function() {
        document.getElementById('vehicle').value = '';
        document.getElementById('type_carburant').value = '';
        document.getElementById('month').value = '';
        document.getElementById('year').value = '';
        document.getElementById('station').value = '';
    });
</script>

<script>
    // Définir la date de fin par défaut sur aujourd'hui
    document.addEventListener('DOMContentLoaded', function() {
        const dateFin = document.getElementById('date_fin');
        if (!dateFin.value) {
            const today = new Date().toISOString().split('T')[0];
            dateFin.value = today;
        }

        // Validation : date de début ne peut pas être après date de fin
        const dateDebut = document.getElementById('date_debut');
        const form = document.querySelector('form');

        form.addEventListener('submit', function(e) {
            if (dateDebut.value && dateFin.value && dateDebut.value > dateFin.value) {
                e.preventDefault();
                alert('La date de début ne peut pas être après la date de fin.');
                dateDebut.focus();
            }
        });
    });
     $('#tab_fuel_entrie').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/fr-FR.json',
        },
        ordering:false,
      //  order: [[0, 'asc']],
         lengthMenu: [10, 25, 50, 100, 200,500,1000]
    });
</script>
@endpush

