@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $nbVehicules }}</h4>
                        <p>Total Véhicules</p>
                    </div>
                    <i class="fas fa-truck fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ number_format($montantEntretien?? 0, 0, ',', ' ') }} FCFA</h4>
                        <p> {{ $nbEntretiens }} Entretiens Ce mois</p>
                    </div>
                    <i class="fas fa-tools fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ number_format($montantCarburant?? 0, 0, ',', ' ') }} FCFA</h4>
                        <p> {{ $nbLitre }}L Carburant ce mois</p>
                    </div>
                    <i class="fas fa-gas-pump fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $nbTrajets }}</h4>
                        <p>Trajet ce mois</p>
                    </div>
                    <i class="fas fa-route fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtres interactifs -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-sliders-h"></i> Filtres des Graphiques</h5>
    </div>
    <form method="POST" action="{{ route('dashboard') }}" >
        @csrf
        <div class="card-body">
        <div class="row g-3">
              <div class="col-md-3">
                <label for="vehicleFilter" class="form-label">Véhicule</label>
                <select class="select2 form-select" id="vehicle_id"  name="vehicle_id">
                    <option value="">Tous les véhicules</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ $vehicle_id==$vehicle->id ? 'selected' : 'null' }}>{{ $vehicle->immatriculation }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="timeRange" class="form-label">Date Debut</label>
                <input class="form-control" type="date"  name="date_debut" value="{{ $date_debut }}" >
            </div>

            <div class="col-md-3">
                <label for="chartType" class="form-label"></label>
                <label for="timeRange" class="form-label">Date Fin</label>
                <input class="form-control" type="date"  value="{{ $date_fin }}" name="date_fin" >
            </div>
            <div class="col-md-3">
                <label class="form-label d-block">&nbsp;</label>
                <button type="submit" id="applyFilters" class="btn btn-primary">
                    <i class="fas fa-sync-alt"></i> Appliquer
                </button>
                <button type="button" id="resetFilters" class="btn btn-outline-secondary">
                    <i class="fas fa-undo"></i> Réinitialiser
                </button>
            </div>
        </div>
    </div>
    </form>

</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="vehicles">
                <thead class="table-dark">
                    <tr>
                        <th>Véhicule</th>
                        <th>Montant Carburant</th>
                        <th>Montant Depannage</th>
                        <th>Nombre de trajets</th>
                        <th>Coût Total</th>


                    </tr>
                </thead>
                <tbody>
                    @foreach($vehicles as $vehicle)
                    <tr>
                        <td>
                            <strong>{{ $vehicle->immatriculation }}</strong><br>
                            {{--  <small class="text-muted">{{ $entry->vehicle->marque }} {{ $entry->vehicle->modele }}</small>--}}
                        </td>
                        <td>
                            <span class="badge bg-primary">
                                {{ number_format($vehicle->montantFuelEntry, 0, ',', ' ') }} FCFA
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-primary">
                                {{ number_format($vehicle->montantRepairLog, 0, ',', ' ') }} FCFA
                            </span>
                        </td>
                        <td><span class="badge bg-primary">{{ $vehicle->nbTrajet }}</span></td>
                        <td>
                            <span class="badge bg-primary">
                                {{ number_format($vehicle->montantFuelEntry + $vehicle->montantRepairLog , 0, ',', ' ') }} FCFA
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history"></i> Historique des Remplissages</h5>
            </div>
            <div class="card-body">
                @if($fuelEntries->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="fuelEntries">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Véhicule</th>
                                <th>Type Carburant</th>
                                <th>Litres</th>
                                <th>Coût Total</th>
                                <th>Nombre de trajets</th>

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
                                <td>{{ $entry->type_carburant }}</td>
                                <td>{{ number_format($entry->litres, 1, ',', ' ') }} L</td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ number_format($entry->cout_total, 0, ',', ' ') }} FCFA
                                    </span>
                                </td>
                                <td><span class="badge bg-primary">{{ $entry->nombreTotalTrajets }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @else
                <div class="text-center py-4">
                    <i class="fas fa-gas-pump fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun remplissage enregistré</h5>
                    <p class="text-muted">Commencez par ajouter votre premier remplissage de carburant.</p>
                    <a href="{{ route('fuel-entries.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajouter un Remplissage
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list"></i> Historique des Interventions</h5>
            </div>
            <div class="card-body">
                @if($repairLogs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="repairLogs">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Véhicule</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Coût</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($repairLogs as $log)
                            <tr>
                                <td>{{ $log->date_intervention->format('d/m/Y') }}</td>
                                <td>
                                    <strong>{{ $log->vehicle->immatriculation }}</strong><br>
                                    <small class="text-muted">{{ $log->vehicle->marque }} {{ $log->vehicle->modele }}</small>
                                </td>
                                <td>
                                    <i class="{{ $log->type_icon }}"></i>
                                    {{ $log->type_intervention_label }}
                                </td>
                                <td>{{ Str::limit($log->description, 50) }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ number_format($log->cout_total, 0, ',', ' ') }} FCFA
                                    </span>
                                </td>


                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @else
                <div class="text-center py-4">
                    <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune intervention enregistrée</h5>
                    <p class="text-muted">Commencez par ajouter votre première intervention.</p>
                    <a href="{{ route('repair-logs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajouter une Intervention
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>





@endsection

@push('styles')
<style>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.chart-container {
    position: relative;
    height: 250px;
    width: 100%;
}

.vehicle-selector.active {
    background-color: #0d6efd !important;
    color: white !important;
    border-color: #0d6efd !important;
}

.alert.position-fixed {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
@endpush

{{-- Le HTML reste identique jusqu'à la section scripts --}}

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</script>

<script>

    $('#repairLogs').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/fr-FR.json',
        },
            ordering:false,

    });
    $('#fuelEntries').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/fr-FR.json',
        },
            ordering:false,

    });
     $('#vehicles').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/fr-FR.json',
        },
        order: [[4, 'desc']]
    });
</script>
@endpush
