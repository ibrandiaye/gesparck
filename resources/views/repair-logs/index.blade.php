@extends('layouts.app')

@section('title', 'Gestion des Dépannages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-tools"></i> Gestion des Dépannages</h1>
    <a href="{{ route('repair-logs.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouvelle Intervention
    </a>
</div>

<!-- Cartes de statistiques -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['totalInterventions'] }}</h4>
                        <p>Total Interventions</p>
                    </div>
                    <i class="fas fa-tools fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['interventionsMois'] }}</h4>
                        <p>Ce mois</p>
                    </div>
                    <i class="fas fa-calendar fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ number_format($stats['coutTotalMois'], 0, ',', ' ') }} FCFA</h4>
                        <p>Coût ce mois</p>
                    </div>
                    <i class="fas fa-money-bill-wave fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['enCours'] }}</h4>
                        <p>En cours</p>
                    </div>
                    <i class="fas fa-clock fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Filtres</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('repair-logs.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="statut" class="form-label">Statut</label>
                <select class="form-select" id="statut" name="statut">
                    <option value="">Tous les statuts</option>
                    <option value="planifie" {{ request('statut') == 'planifie' ? 'selected' : '' }}>Planifié</option>
                    <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En Cours</option>
                    <option value="termine" {{ request('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                    <option value="annule" {{ request('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="type" class="form-label">Type d'intervention</label>
                <select class="form-select" id="type" name="type">
                    <option value="">Tous les types</option>
                    @foreach(['entretien_routine', 'reparation', 'vidange', 'freinage', 'pneumatique', 'electrique', 'mecanique', 'carrosserie', 'autre'] as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="vehicle" class="form-label">Véhicule</label>
                <select class="form-select" id="vehicle" name="vehicle">
                    <option value="">Tous les véhicules</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ request('vehicle') == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->immatriculation }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label d-block">&nbsp;</label>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrer
                </button>
                <a href="{{ route('repair-logs.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-undo"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tableau des interventions -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list"></i> Historique des Interventions</h5>
    </div>
    <div class="card-body">
        @if($repairLogs->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Véhicule</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Coût</th>
                        <th>Statut</th>
                        <th>Actions</th>
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
                        <td>
                            <span class="badge bg-{{ $log->statut_color }}">
                                {{ $log->statut_label }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('repair-logs.show', $log) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('repair-logs.edit', $log) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('repair-logs.destroy', $log) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger"
                                            onclick="return confirm('Supprimer cette intervention?')">
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
        <div class="d-flex justify-content-center mt-3">
            {{ $repairLogs->links() }}
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
@endsection
