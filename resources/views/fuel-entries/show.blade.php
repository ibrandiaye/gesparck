@extends('layouts.app')

@section('title', 'Détails du Remplissage')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-gas-pump"></i> Détails du Remplissage</h4>
                <div>
                    <a href="{{ route('fuel-entries.edit', $fuelEntry) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('fuel-entries.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Informations du Remplissage</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Date:</th>
                                <td>{{ $fuelEntry->date_remplissage->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Véhicule:</th>
                                <td>
                                    <strong>{{ $fuelEntry->vehicle->immatriculation }}</strong><br>
                                    <small>{{ $fuelEntry->vehicle->marque }} {{ $fuelEntry->vehicle->modele }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Station:</th>
                                <td>{{ $fuelEntry->station ?? 'Non spécifiée' }}</td>
                            </tr>
                            <tr>
                                <th>Type carburant:</th>
                                <td>
                                    <span class="badge bg-info">
                                        {{ strtoupper($fuelEntry->type_carburant) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h5>Détails Techniques</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Prix du litre:</th>
                                <td class="fw-bold text-primary">
                                    {{ number_format($fuelEntry->prix_litre, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                            <tr>
                                <th>Quantité:</th>
                                <td>{{ number_format($fuelEntry->litres, 1, ',', ' ') }} litres</td>
                            </tr>
                            <tr>
                                <th>Coût total:</th>
                                <td class="fw-bold text-success">
                                    {{ number_format($fuelEntry->cout_total, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                            <tr>
                                <th>Kilométrage:</th>
                                <td>{{ number_format($fuelEntry->kilometrage, 0, ',', ' ') }} km</td>
                            </tr>
                            @if($fuelEntry->km_parcourus && $fuelEntry->consommation)
                            <tr>
                                <th>Km parcourus:</th>
                                <td>{{ number_format($fuelEntry->km_parcourus, 0, ',', ' ') }} km</td>
                            </tr>
                            <tr>
                                <th>Consommation:</th>
                                <td class="fw-bold text-warning">
                                    {{ number_format($fuelEntry->consommation, 2, ',', ' ') }} L/100km
                                </td>
                            </tr>

                            @endif
                            <tr>
                                <th>Nombre de trajet:</th>
                                <td class="fw-bold text-warning">
                                    <span class="badge bg-danger">
                                    <h5>{{ $fuelEntry->nombreTotalTrajets}}</h5>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($fuelEntry->notes)
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Notes & Observations</h5>
                        <div class="alert alert-light border">
                            {{ $fuelEntry->notes }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Graphique de consommation pour ce véhicule -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Historique de Consommation - {{ $fuelEntry->vehicle->immatriculation }}</h5>
            </div>
            <div class="card-body">
                <canvas id="consumptionChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Statistiques du véhicule -->
        @if($vehicleStats)
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Statistiques du Véhicule</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Consommation moyenne:</span>
                        <strong class="text-primary">
                            {{ number_format($vehicleStats['consommation_moyenne'], 2, ',', ' ') }} L/100km
                        </strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Coût total carburant:</span>
                        <strong class="text-success">
                            {{ number_format($vehicleStats['cout_total'], 0, ',', ' ') }} FCFA
                        </strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Distance totale:</span>
                        <strong>{{ number_format($vehicleStats['distance_totale'], 0, ',', ' ') }} km</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Nombre de remplissages:</span>
                        <span class="badge bg-info rounded-pill">{{ $vehicleStats['nombre_remplissages'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Derniers remplissages du véhicule -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-history"></i> Derniers Remplissages</h5>
            </div>
            <div class="card-body">
                @php
                    $lastEntries = \App\Models\FuelEntry::where('vehicle_id', $fuelEntry->vehicle_id)
                        ->where('id', '!=', $fuelEntry->id)
                        ->orderBy('date_remplissage', 'desc')
                        ->take(5)
                        ->get();
                @endphp

                @if($lastEntries->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($lastEntries as $entry)
                    <a href="{{ route('fuel-entries.show', $entry) }}"
                       class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <small>{{ $entry->date_remplissage->format('d/m/Y') }}</small>
                            <strong>{{ number_format($entry->cout_total, 0, ',', ' ') }} FCFA</strong>
                        </div>
                        <div class="d-flex w-100 justify-content-between">
                            <small>{{ number_format($entry->litres, 1, ',', ' ') }} L</small>
                            <small>{{ number_format($entry->kilometrage, 0, ',', ' ') }} km</small>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center mb-0">Aucun autre remplissage</p>
                @endif
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-bolt"></i> Actions Rapides</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('fuel-entries.create') }}?vehicle_id={{ $fuelEntry->vehicle_id }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus"></i> Nouveau Remplissage
                    </a>
                    <a href="{{ route('vehicles.show', $fuelEntry->vehicle) }}"
                       class="btn btn-outline-info btn-sm">
                        <i class="fas fa-truck"></i> Voir le Véhicule
                    </a>
                    @if (Auth::user()->role=="admin")
                        <form action="{{ route('fuel-entries.destroy', $fuelEntry) }}" method="POST" class="d-grid">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Supprimer ce remplissage?')">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour le graphique
    const consumptionData = json($fuelEntry->vehicle->fuelEntries()
        ->orderBy('date_remplissage')
        ->get()
        ->map(function($entry) {
            return [
                'date' => $entry->date_remplissage->format('d/m/Y'),
                'consommation' => $entry->consommation,
                'prix_litre' => $entry->prix_litre,
                'kilometrage' => $entry->kilometrage
            ];
        }));

    // Filtrer les données avec consommation valide
    const validData = consumptionData.filter(item => item.consommation !== null);

    if (validData.length > 0) {
        const ctx = document.getElementById('consumptionChart').getContext('2d');
        const consumptionChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: validData.map(item => item.date),
                datasets: [
                    {
                        label: 'Consommation (L/100km)',
                        data: validData.map(item => item.consommation),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        yAxisID: 'y',
                        tension: 0.3
                    },
                    {
                        label: 'Prix du litre (FCFA)',
                        data: validData.map(item => item.prix_litre),
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        yAxisID: 'y1',
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Consommation (L/100km)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Prix (FCFA/L)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
    } else {
        document.getElementById('consumptionChart').innerHTML =
            '<p class="text-muted text-center py-4">Données insuffisantes pour afficher le graphique</p>';
    }
});
</script>
@endpush
