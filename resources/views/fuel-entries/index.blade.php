@extends('layouts.app')

@section('title', 'Historique Carburant')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-gas-pump"></i> Gestion du Carburant</h1>
    <a href="{{ route('fuel-entries.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouveau Remplissage
    </a>
</div>

<!-- Cartes de statistiques -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ number_format($stats['totalCoutMois'], 0, ',', ' ') }} FCFA</h4>
                        <p>Coût ce mois</p>
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
                        <p>Litres ce mois</p>
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
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-history"></i> Historique des Remplissages</h5>
    </div>
    <div class="card-body">
        @if($fuelEntries->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Véhicule</th>
                        <th>Station</th>
                        <th>Litres</th>
                        <th>Prix/L</th>
                        <th>Coût Total</th>
                        <th>Km</th>
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
                        <td>{{ $entry->station ?? 'N/A' }}</td>
                        <td>{{ number_format($entry->litres, 1, ',', ' ') }} L</td>
                        <td>{{ number_format($entry->prix_litre, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <span class="badge bg-primary">
                                {{ number_format($entry->cout_total, 0, ',', ' ') }} FCFA
                            </span>
                        </td>
                        <td>{{ number_format($entry->kilometrage, 0, ',', ' ') }} km</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('fuel-entries.show', $entry) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('fuel-entries.edit', $entry) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('fuel-entries.destroy', $entry) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger"
                                            onclick="return confirm('Supprimer ce remplissage?')">
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
            {{ $fuelEntries->links() }}
        </div>
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
@endsection
