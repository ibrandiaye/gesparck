@extends('layouts.app')

@section('title', 'Détails du Véhicule - ' . $vehicle->immatriculation)

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Informations générales -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-info-circle"></i> Détails du Véhicule</h4>
                <div>
                    <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('vehicles.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Informations Générales</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Immatriculation:</th>
                                <td><strong class="text-primary">{{ $vehicle->immatriculation }}</strong></td>
                            </tr>
                            <tr>
                                <th>Marque/Modèle:</th>
                                <td>{{ $vehicle->marque }} {{ $vehicle->modele }}</td>
                            </tr>
                            <tr>
                                <th>Type:</th>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ ucfirst($vehicle->type_vehicule) }}
                                    </span>
                                </td>
                            </tr>
                             <tr>
                                <th>Categorie:</th>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ ucfirst($vehicle->categorie) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>État:</th>
                                <td>
                                    <span class="badge bg-{{ $vehicle->etat == 'disponible' ? 'success' : ($vehicle->etat == 'en_entretien' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($vehicle->etat) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h5>Caractéristiques Techniques</h5>
                        <table class="table table-borderless">
                            <tr>
                                <tr>
                                    <th>Carburant:</th>
                                    <td>{{ $vehicle->carburant->libelle ?? null }}</td>
                                </tr>
                                <th width="40%">Kilométrage:</th>
                                <td>
                                    <span class="badge bg-dark">
                                        {{ number_format($vehicle->kilometrage_actuel, 0, ',', ' ') }} km
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Nombre de trajet:</th>
                                <td>
                                    <span class="badge bg-danger">
                                        {{ $vehicle->nombreTotalTrajets}}
                                    </span>
                                </td>
                            </tr>
                             {{--<tr>
                                <th>Âge du véhicule:</th>
                                <td>
                                    {{ $vehicle->date_mise_en_service->diffInYears(now()) }} ans
                                </td>
                            </tr> --}}
                            <tr>
                                <th>Date d'ajout:</th>
                                <td>{{ $vehicle->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($vehicle->notes)
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Notes & Observations</h5>
                        <div class="alert alert-light border">
                            {{ $vehicle->notes }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Statistiques -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Statistiques du Véhicule</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <i class="fas fa-gas-pump fa-2x text-primary mb-2"></i>
                            <h4>{{ $vehicle->fuelEntries->count() }}</h4>
                            <small class="text-muted">Remplissages</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <i class="fas fa-tools fa-2x text-warning mb-2"></i>
                            <h4>{{ $vehicle->repairLogs->count() }}</h4>
                            <small class="text-muted">Interventions</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                            <h4>{{ number_format($vehicle->fuelEntries->sum('cout_total') + $vehicle->repairLogs->sum('cout_total'), 0, ',', ' ') }} FCFA</h4>
                            <small class="text-muted">Coût Total</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <i class="fas fa-tachometer-alt fa-2x text-info mb-2"></i>
                            <h4>{{ $vehicle->consommation_moyenne ? $vehicle->consommation_moyenne . ' L/100km' : 'N/A' }}</h4>
                            <small class="text-muted">Consommation Moy.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Historique de Consommation</h5>
            </div>
            <div class="card-body">
                <canvas id="consumptionChart" height="100"></canvas>
            </div>
        </div>

        <!-- Derniers remplissages -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-gas-pump"></i> Derniers Remplissages</h5>
                <a href="{{ route('fuel-entries.create') }}?vehicle_id={{ $vehicle->id }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nouveau
                </a>
            </div>
            <div class="card-body">
                @if($vehicle->fuelEntries->count() > 0)
                <div class="table-responsive " >
                    <table class="table table-sm" id="fuelEntries">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Litres</th>
                                <th>Prix/L</th>
                                <th>Coût</th>
                                <th>Km</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicle->fuelEntries as $entry)
                            <tr>
                                <td>{{ $entry->date_remplissage->format('d/m/Y') }}</td>
                                <td>{{ number_format($entry->litres, 1, ',', ' ') }} L</td>
                                <td>{{ number_format($entry->prix_litre, 0, ',', ' ') }} FCFA</td>
                                <td>{{ number_format($entry->cout_total, 0, ',', ' ') }} FCFA</td>
                                <td>{{ number_format($entry->kilometrage, 0, ',', ' ') }} km</td>
                                <td>
                                    <a href="{{ route('fuel-entries.show', $entry) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>

                            @endforeach
                        </tbody>
                    </table>
                     @php
                        $coutCarburant = $vehicle->fuelEntries->sum('cout_total');
                        $nbLitre = $vehicle->fuelEntries->sum('litres');

                @endphp
                <div class="row">
                    <div class="col-md-6">
                        <h5>Nombre de Litre : {{ number_format($nbLitre,1,',','') }} Litres </h5>
                    </div>
                    <div class="col-md-6">
                        <h5>Cout Total : {{ number_format($coutCarburant,1,',','') }} CFA </h5>
                    </div>
                </div>


                </div>
                @if($vehicle->fuelEntries->count() > 5)
                <div class="text-center mt-2">
                    <a href="{{ route('fuel-entries.index') }}?vehicle={{ $vehicle->id }}" class="btn btn-sm btn-outline-primary">
                        Voir tout l'historique
                    </a>
                </div>
                @endif
                @else
                <p class="text-muted text-center mb-0">Aucun remplissage enregistré</p>
                @endif
            </div>
        </div>

        <!-- Dernières interventions -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-tools"></i> Dernières Interventions</h5>
                <a href="{{ route('repair-logs.create') }}?vehicle_id={{ $vehicle->id }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nouvelle
                </a>
            </div>
            <div class="card-body">
                @if($vehicle->repairLogs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm " id="repairLogs">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Coût</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicle->repairLogs->sortByDesc('date_intervention') as $log)
                            <tr>
                                <td>{{ $log->date_intervention->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $log->type_intervention_label }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($log->description, 30) }}</td>
                                <td>{{ number_format($log->cout_total, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    <span class="badge bg-{{ $log->statut_color }}">
                                        {{ $log->statut_label }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('repair-logs.show', $log) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                      @php

                            $coutEntretien = $vehicle->repairLogs->sum('cout_total');
                        @endphp
                        <h5> Cout Total : {{ number_format($coutEntretien,1,',','') }} CFA</h5>
                </div>
                @if($vehicle->repairLogs->count() > 5)
                <div class="text-center mt-2">
                    <a href="{{ route('repair-logs.index') }}?vehicle={{ $vehicle->id }}" class="btn btn-sm btn-outline-primary">
                        Voir tout l'historique
                    </a>
                </div>
                @endif
                @else
                <p class="text-muted text-center mb-0">Aucune intervention enregistrée</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Actions Rapides -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bolt"></i> Actions Rapides</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('fuel-entries.create') }}?vehicle_id={{ $vehicle->id }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-gas-pump"></i> Ajouter Carburant
                    </a>
                    <a href="{{ route('repair-logs.create') }}?vehicle_id={{ $vehicle->id }}"
                       class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-tools"></i> Enregistrer Dépannage
                    </a>
                    <a href="{{ route('fuel-entries.index') }}?vehicle={{ $vehicle->id }}"
                       class="btn btn-outline-info btn-sm">
                        <i class="fas fa-history"></i> Historique Carburant
                    </a>
                    <a href="{{ route('repair-logs.index') }}?vehicle={{ $vehicle->id }}"
                       class="btn btn-outline-info btn-sm">
                        <i class="fas fa-history"></i> Historique Dépannages
                    </a>
                </div>
            </div>
        </div>

        <!-- État du Véhicule -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-sliders-h"></i> Changer l'État</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('vehicles.update', $vehicle) }}" method="POST" class="d-grid gap-2">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="marque" value="{{ $vehicle->marque }}">
                    <input type="hidden" name="modele" value="{{ $vehicle->modele }}">
                    <input type="hidden" name="kilometrage_actuel" value="{{ $vehicle->kilometrage_actuel }}">

                    <select name="etat" class="form-select" onchange="this.form.submit()">
                        <option value="disponible" {{ $vehicle->etat == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="en_entretien" {{ $vehicle->etat == 'en_entretien' ? 'selected' : '' }}>En Entretien</option>
                        <option value="hors_service" {{ $vehicle->etat == 'hors_service' ? 'selected' : '' }}>Hors Service</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Prochain entretien -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-calendar-check"></i> Prochain Entretien</h5>
            </div>
            <div class="card-body">
                @php
                    $prochainEntretien = $vehicle->repairLogs
                        ->where('date_prochaine_revision', '>=', now())
                        ->sortBy('date_prochaine_revision')
                        ->first();
                @endphp

                @if($prochainEntretien)
                <div class="alert alert-info">
                    <strong>{{ $prochainEntretien->date_prochaine_revision->format('d/m/Y') }}</strong>
                    <br>
                    <small>{{ $prochainEntretien->description }}</small>
                    @if($prochainEntretien->prochain_kilometrage)
                    <br>
                    <small>À {{ number_format($prochainEntretien->prochain_kilometrage, 0, ',', ' ') }} km</small>
                    @endif
                </div>
                @else
                <p class="text-muted text-center mb-0">Aucun entretien planifié</p>
                @endif
            </div>
        </div>

        <!-- Statistiques détaillées -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie"></i> Répartition des Coûts</h5>
            </div>
            <div class="card-body">
                @php
                    $coutCarburant = $vehicle->fuelEntries->sum('cout_total');
                    $coutEntretien = $vehicle->repairLogs->sum('cout_total');
                    $total = $coutCarburant + $coutEntretien;
                @endphp

                @if($total > 0)
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Carburant:</span>
                        <strong>{{ number_format($coutCarburant, 0, ',', ' ') }} FCFA</strong>
                    </div>
                    <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar bg-primary" style="width: {{ $total > 0 ? ($coutCarburant/$total)*100 : 0 }}%"></div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Entretien:</span>
                        <strong>{{ number_format($coutEntretien, 0, ',', ' ') }} FCFA</strong>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-warning" style="width: {{ $total > 0 ? ($coutEntretien/$total)*100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="text-center">
                    <h4>{{ number_format($total, 0, ',', ' ') }} FCFA</h4>
                    <small class="text-muted">Coût total</small>
                </div>
                @else
                <p class="text-muted text-center mb-0">Aucune donnée de coût</p>
                @endif
            </div>
        </div>

        <!-- Alertes -->
        <div class="card mt-4 border-warning">
            <div class="card-header bg-warning text-dark">
                <h5><i class="fas fa-exclamation-triangle"></i> Alertes</h5>
            </div>
            <div class="card-body">
                @php
                    $alertes = [];

                    // Alerte kilométrage prochain entretien
                    if ($prochainEntretien && $prochainEntretien->prochain_kilometrage) {
                        $differenceKm = $prochainEntretien->prochain_kilometrage - $vehicle->kilometrage_actuel;
                        if ($differenceKm < 1000 && $differenceKm > 0) {
                            $alertes[] = "Entretien prévu à {$prochainEntretien->prochain_kilometrage} km ({$differenceKm} km restants)";
                        }
                    }

                    // Alerte date prochain entretien
                    if ($prochainEntretien && $prochainEntretien->date_prochaine_revision) {
                        $joursRestants = now()->diffInDays($prochainEntretien->date_prochaine_revision, false);
                        if ($joursRestants < 30 && $joursRestants > 0) {
                            $alertes[] = "Entretien dans {$joursRestants} jours";
                        }
                    }

                    // Alerte consommation anormale
                    if ($vehicle->consommation_moyenne && $vehicle->consommation_moyenne > 15) {
                        $alertes[] = "Consommation élevée: {$vehicle->consommation_moyenne} L/100km";
                    }
                @endphp

                @if(count($alertes) > 0)
                <div class="alert alert-warning">
                    <ul class="mb-0">
                        @foreach($alertes as $alerte)
                        <li>{{ $alerte }}</li>
                        @endforeach
                    </ul>
                </div>
                @else
                <p class="text-success text-center mb-0">
                    <i class="fas fa-check-circle"></i> Aucune alerte
                </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour le graphique de consommation
    const fuelData = json($vehicle->fuelEntries->sortBy('date_remplissage')->map(function($entry) {
        return [
            'date' => $entry->date_remplissage->format('M Y'),
            'consommation' => $entry->consommation,
            'prix_litre' => $entry->prix_litre,
            'kilometrage' => $entry->kilometrage
        ];
    }));

    // Filtrer les données avec consommation valide
    const validData = fuelData.filter(item => item.consommation !== null);

    if (validData.length > 1) {
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
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Consommation: ${context.parsed.y} L/100km`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'L/100km'
                        }
                    }
                }
            }
        });
    } else {
        document.getElementById('consumptionChart').innerHTML =
            '<p class="text-muted text-center py-4">Données insuffisantes pour afficher le graphique de consommation</p>';
    }

    // Confirmation avant changement d'état
    const etatSelect = document.querySelector('select[name="etat"]');
    if (etatSelect) {
        etatSelect.addEventListener('change', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir changer l\'état du véhicule ?')) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.progress {
    background-color: #e9ecef;
    border-radius: 0.375rem;
}
.progress-bar {
    border-radius: 0.375rem;
}
</style>
@endpush
