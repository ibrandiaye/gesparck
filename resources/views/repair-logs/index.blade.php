@extends('layouts.app')

@section('title', 'Gestion des Dépannages')

@section('content')
    <style>
        svg {
            height: 20px !important;
        }
    </style>

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
                        <p>Interventions</p>
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
                        <p>Coût total</p>
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
            <div class="col-md-2">
                <label for="statut" class="form-label">Type</label>

                     <select class="form-select @error('categorie') is-invalid @enderror"
                        id="categorie" name="categorie" >
                    <option value="">Tous les types</option>
                    <option value="sedipal-nestle" {{ request('categorie') == 'sedipal-nestle' ? 'selected' : '' }}>sedipal-nestle</option>
                    <option value="cdn-nestle" {{ request('categorie') == 'cdn-nestle' ? 'selected' : '' }}>cdn-nestle</option>
                    <option value="vehicule-livraison-sedipal" {{ request('categorie') == 'vehicule-livraison-sedipal' ? 'selected' : '' }}>vehicule-livraison-sedipal</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="type" class="form-label">Type Depannage</label>
                <select class="form-select " id="type" name="type">
                    <option value="">Tous les types</option>
                    @foreach(['divers-reparation', 'pneu', 'vidange', 'batterie', 'disque-plateau',  'autre'] as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="vehicle" class="form-label">Véhicule</label>
                <select class="form-select select2" id="vehicle" name="vehicle">
                    <option value="">Tous les véhicules</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ request('vehicle') == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->immatriculation }}
                        </option>
                    @endforeach
                </select>
            </div>

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

            <div class="col-md-2 d-flex align-items-end">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary" title="Appliquer les filtres">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="{{ route('repair-logs.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </div>
        </form>

        <!-- Indicateur de filtre actif -->
        @if(request()->anyFilled(['statut', 'type', 'vehicle', 'date_debut', 'date_fin']))
        <div class="mt-3">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                Filtres actifs :
                @if(request('statut'))
                    <span class="badge bg-primary">Statut: {{ ucfirst(request('statut')) }}</span>
                @endif
                @if(request('type'))
                    <span class="badge bg-primary">Type: {{ ucfirst(str_replace('_', ' ', request('type'))) }}</span>
                @endif
                @if(request('vehicle'))
                    @php
                        $selectedVehicle = $vehicles->firstWhere('id', request('vehicle'));
                    @endphp
                    @if($selectedVehicle)
                        <span class="badge bg-primary">Véhicule: {{ $selectedVehicle->immatriculation }}</span>
                    @endif
                @endif
                @if(request('date_debut') || request('date_fin'))
                    <span class="badge bg-primary">
                        Période:
                        {{ request('date_debut') ? \Carbon\Carbon::parse(request('date_debut'))->format('d/m/Y') : 'Début' }}
                         -
                        {{ request('date_fin') ? \Carbon\Carbon::parse(request('date_fin'))->format('d/m/Y') : 'Fin' }}
                    </span>
                @endif
            </small>
        </div>
        @endif
    </div>
</div>

<!-- Tableau des interventions -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list"></i> Historique des Interventions</h5>
        <span class="badge bg-primary">
            {{ $repairLogs->total() }} résultat(s)
        </span>
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
                            <strong>{{ $log->vehicle->immatriculation ?? null }}</strong><br>
                            <small class="text-muted">{{ $log->vehicle->marque  ?? null}} {{ $log->vehicle->modele ?? null }}</small>
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
                                @if (Auth::user()->role=="admin")
                                    <form action="{{ route('repair-logs.destroy', $log) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger"
                                                onclick="return confirm('Supprimer cette intervention?')">
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

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Affichage de {{ $repairLogs->firstItem() }} à {{ $repairLogs->lastItem() }} sur {{ $repairLogs->total() }} résultats
            </div>
            <div>
                {{ $repairLogs->links() }}
            </div>
        </div>

        @else
        <div class="text-center py-4">
            <i class="fas fa-tools fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Aucune intervention trouvée</h5>
            <p class="text-muted">Aucun résultat ne correspond à vos critères de recherche.</p>
            <a href="{{ route('repair-logs.index') }}" class="btn btn-primary me-2">
                <i class="fas fa-redo"></i> Réinitialiser les filtres
            </a>
            <a href="{{ route('repair-logs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter une Intervention
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
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
</script>
@endpush
