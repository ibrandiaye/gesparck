@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['totalVehicles'] }}</h4>
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
                        <h4>{{ $stats['vehiclesDisponibles'] }}</h4>
                        <p>Véhicules Disponibles</p>
                    </div>
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ number_format($stats['coutCarburantMois'] ?? 0, 0, ',', ' ') }} FCFA</h4>
                        <p>Carburant ce mois</p>
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
                        <h4>{{ $stats['remplissagesMois'] ?? 0 }}</h4>
                        <p>Remplissages</p>
                    </div>
                    <i class="fas fa-chart-bar fa-2x"></i>
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
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="timeRange" class="form-label">Période</label>
                <select class="form-select" id="timeRange">
                    <option value="6">6 derniers mois</option>
                    <option value="3">3 derniers mois</option>
                    <option value="12">12 derniers mois</option>
                    <option value="all">Toute la période</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="vehicleFilter" class="form-label">Véhicule</label>
                <select class="form-select" id="vehicleFilter">
                    <option value="all">Tous les véhicules</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->immatriculation }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="chartType" class="form-label">Type de Graphique</label>
                <select class="form-select" id="chartType">
                    <option value="line">Ligne</option>
                    <option value="bar">Barres</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label d-block">&nbsp;</label>
                <button type="button" id="applyFilters" class="btn btn-primary">
                    <i class="fas fa-sync-alt"></i> Appliquer
                </button>
                <button type="button" id="resetFilters" class="btn btn-outline-secondary">
                    <i class="fas fa-undo"></i> Réinitialiser
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Graphique de consommation moyenne -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Consommation Moyenne par Véhicule</h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="exportConsumption">
                        <i class="fas fa-download"></i> Exporter
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info" id="toggleConsumptionView">
                        <i class="fas fa-exchange-alt"></i> Alterner
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="avgConsumptionChart" height="250"></canvas>
                </div>
                <div class="mt-3" id="consumptionStats">
                    <!-- Les statistiques seront chargées ici -->
                </div>
            </div>
        </div>
    </div>

    <!-- Coût mensuel carburant -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Coût Carburant Mensuel</h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="exportCost">
                        <i class="fas fa-download"></i> Exporter
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" id="toggleTrendLine">
                        <i class="fas fa-chart-line"></i> Tendance
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="monthlyCostChart" height="250"></canvas>
                <div class="mt-3" id="costStats">
                    <!-- Les statistiques seront chargées ici -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphique interactif avancé -->
@if($vehicles->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-area"></i> Analyse Détaillée de Consommation</h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="detailedView">
                        <i class="fas fa-expand"></i> Vue détaillée
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="list-group" id="vehicleSelector">
                            @foreach($vehicles as $vehicle)
                            <button type="button" class="list-group-item list-group-item-action vehicle-selector"
                                    data-vehicle-id="{{ $vehicle->id }}">
                                {{ $vehicle->immatriculation }}
                                <br>
                                <small class="text-muted">{{ $vehicle->marque }} {{ $vehicle->modele }}</small>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-9">
                        <canvas id="detailedChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal pour la vue détaillée -->
<div class="modal fade" id="detailedModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Analyse Détaillée</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <canvas id="detailedModalChart" height="400"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Véhicules récents -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-clock"></i> Véhicules Récents</h5>
            </div>
            <div class="card-body">
                @if($recentVehicles->count() > 0)
                <div class="list-group">
                    @foreach($recentVehicles as $vehicle)
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $vehicle->marque }} {{ $vehicle->modele }}</h6>
                            <small class="text-muted">{{ $vehicle->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1">Immat: {{ $vehicle->immatriculation }} | {{ number_format($vehicle->kilometrage_actuel, 0, ',', ' ') }} km</p>
                        <span class="badge bg-{{ $vehicle->etat == 'disponible' ? 'success' : 'warning' }}">
                            {{ ucfirst($vehicle->etat) }}
                        </span>
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-muted">Aucun véhicule enregistré.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Remplissages récents -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-history"></i> Derniers Remplissages</h5>
            </div>
            <div class="card-body">
                @if($recentFuelEntries->count() > 0)
                <div class="list-group">
                    @foreach($recentFuelEntries as $entry)
                    <a href="{{ route('fuel-entries.show', $entry) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $entry->vehicle->immatriculation }}</h6>
                            <small class="text-muted">{{ $entry->date_remplissage->format('d/m/Y') }}</small>
                        </div>
                        <p class="mb-1">
                            {{ number_format($entry->litres, 1, ',', ' ') }} L •
                            {{ number_format($entry->cout_total, 0, ',', ' ') }} FCFA
                        </p>
                        <small class="text-muted">{{ number_format($entry->kilometrage, 0, ',', ' ') }} km</small>
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-muted">Aucun remplissage enregistré.</p>
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
<script>
// Variables globales pour les graphiques
let consumptionChart, monthlyCostChart, detailedChart;
let currentFilters = {
    timeRange: '6',
    vehicleFilter: 'all',
    chartType: 'bar'
};

// Élément de chargement
const loadingOverlay = document.createElement('div');
loadingOverlay.className = 'loading-overlay';
loadingOverlay.innerHTML = `
    <div class="text-center">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
        </div>
        <p class="mt-2 text-muted">Chargement des données...</p>
    </div>
`;

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    setupEventListeners();
    loadChartData(); // Chargement initial
});

function initializeCharts() {
    // Graphique de consommation moyenne
    const avgCtx = document.getElementById('avgConsumptionChart');
    if (avgCtx) {
        consumptionChart = new Chart(avgCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Consommation Moyenne (L/100km)',
                    data: [],
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
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
                            text: 'Litres/100km'
                        }
                    }
                },
                onClick: (e, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const vehicleId = consumptionChart.data.datasets[0].vehicleIds[index];
                        if (vehicleId) {
                            window.location.href = `/vehicles/${vehicleId}`;
                        }
                    }
                }
            }
        });
    }

    // Graphique des coûts mensuels
/*   const monthlyCtx = document.getElementById('monthlyCostChart');
if (monthlyCtx) {
    monthlyCostChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Coût Carburant Mensuel (FCFA)',
                data: [],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.2,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                        }
                    }
                }
            },
            scales: {
                x: { title: { display: true, text: 'Mois' } },
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Coût (FCFA)' },
                    ticks: {
                        callback: value => new Intl.NumberFormat('fr-FR').format(value)
                    }
                }
            }
        }
    });
}
*/

    // Graphique détaillé
    const detailedCtx = document.getElementById('detailedChart');
    if (detailedCtx) {
        detailedChart = new Chart(detailedCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
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
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    }
}

function setupEventListeners() {
    // Filtres
    document.getElementById('applyFilters').addEventListener('click', loadChartData);
    document.getElementById('resetFilters').addEventListener('click', resetFilters);

    // Changements en temps réel des filtres
    document.getElementById('timeRange').addEventListener('change', function() {
        currentFilters.timeRange = this.value;
        loadChartData();
    });

    document.getElementById('chartType').addEventListener('change', function() {
        currentFilters.chartType = this.value;
        if (consumptionChart) {
            consumptionChart.config.type = this.value;
            consumptionChart.update();
        }
    });

    // Boutons d'export
    document.getElementById('exportConsumption').addEventListener('click', exportConsumptionChart);
    document.getElementById('exportCost').addEventListener('click', exportCostChart);

    // Boutons d'interaction
    document.getElementById('toggleConsumptionView').addEventListener('click', toggleConsumptionView);
    document.getElementById('toggleTrendLine').addEventListener('click', toggleTrendLine);

    // Sélection de véhicule pour le graphique détaillé
    document.querySelectorAll('.vehicle-selector').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.vehicle-selector').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            loadDetailedChart(this.dataset.vehicleId);
        });
    });

    // Sélection automatique du premier véhicule
    const firstVehicle = document.querySelector('.vehicle-selector');
    if (firstVehicle) {
        firstVehicle.click();
    }
}

async function loadChartData() {
    showLoading();

    try {
        console.log('Envoi des filtres:', currentFilters);

        const response = await fetch('{{ route("dashboard.charts") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(currentFilters)
        });

        console.log('Réponse reçue, status:', response.status);

        const data = await response.json();
        console.log('Données reçues:', data);

        if (data.success) {
            updateConsumptionChart(data.consumption);
            updateMonthlyCostChart(data.monthlyCost);
            updateStatistics(data.statistics);
            showSuccess('Données mises à jour avec succès');
        } else {
            throw new Error(data.message || 'Erreur serveur');
        }

    } catch (error) {
        console.error('Erreur détaillée:', error);
        showError('Erreur de chargement: ' + error.message);
        loadDemoData();
    } finally {
        hideLoading();
    }
}

function updateConsumptionChart(data) {
    if (consumptionChart && data) {
        consumptionChart.data.labels = data.labels;
        consumptionChart.data.datasets[0].data = data.values;
        consumptionChart.data.datasets[0].vehicleIds = data.vehicleIds || [];
        consumptionChart.update('resize');
    }
}
function updateMonthlyCostChart(data) {
    if (monthlyCostChart && data?.labels && data?.values) {
        monthlyCostChart.data.labels = data.labels;
        monthlyCostChart.data.datasets[0].data = data.values;
        monthlyCostChart.update();

        console.log('Graphique coûts mensuels mis à jour:', {
            labels: data.labels,
            values: data.values
        });
    }
}
async function loadDetailedChart(vehicleId) {
    showLoading('Chargement des données du véhicule...');

    try {
        const response = await fetch(`/dashboard/vehicle-chart/${vehicleId}`);
        const data = await response.json();

        if (data.success && detailedChart) {
            detailedChart.data.labels = data.data.dates;
            detailedChart.data.datasets = [
                {
                    label: 'Consommation (L/100km)',
                    data: data.data.consumption,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    yAxisID: 'y',
                    tension: 0.3
                },
                {
                    label: 'Prix du Litre (FCFA)',
                    data: data.data.prices,
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    yAxisID: 'y1',
                    tension: 0.3
                }
            ];
            detailedChart.update('resize');
            showSuccess(`Données de ${data.vehicle} chargées`);
        } else {
            throw new Error(data.message || 'Erreur lors du chargement');
        }

    } catch (error) {
        console.error('Erreur lors du chargement du graphique détaillé:', error);
        showError('Erreur: ' + error.message);
    } finally {
        hideLoading();
    }
}

function updateStatistics(stats) {
    const consumptionStats = document.getElementById('consumptionStats');
    const costStats = document.getElementById('costStats');

    if (consumptionStats) {
        consumptionStats.innerHTML = `
            <div class="row text-center">
                <div class="col-4">
                    <small class="text-muted">Moyenne</small>
                    <div class="fw-bold text-primary">${stats.avgConsumption ? stats.avgConsumption + ' L/100km' : 'N/A'}</div>
                </div>
                <div class="col-4">
                    <small class="text-muted">Minimum</small>
                    <div class="fw-bold text-success">${stats.minConsumption ? stats.minConsumption + ' L/100km' : 'N/A'}</div>
                </div>
                <div class="col-4">
                    <small class="text-muted">Maximum</small>
                    <div class="fw-bold text-danger">${stats.maxConsumption ? stats.maxConsumption + ' L/100km' : 'N/A'}</div>
                </div>
            </div>
        `;
    }

    if (costStats) {
        costStats.innerHTML = `
            <div class="row text-center">
                <div class="col-4">
                    <small class="text-muted">Total</small>
                    <div class="fw-bold text-primary">${stats.totalCost ? stats.totalCost.toLocaleString('fr-FR') + ' FCFA' : 'N/A'}</div>
                </div>
                <div class="col-4">
                    <small class="text-muted">Moyenne/mois</small>
                    <div class="fw-bold text-info">${stats.avgMonthlyCost ? stats.avgMonthlyCost.toLocaleString('fr-FR') + ' FCFA' : 'N/A'}</div>
                </div>
                <div class="col-4">
                    <small class="text-muted">Économie potentielle</small>
                    <div class="fw-bold text-warning">${stats.potentialSavings ? stats.potentialSavings.toLocaleString('fr-FR') + ' FCFA' : 'N/A'}</div>
                </div>
            </div>
        `;
    }
}

function resetFilters() {
    document.getElementById('timeRange').value = '6';
    document.getElementById('vehicleFilter').value = 'all';
    document.getElementById('chartType').value = 'bar';

    currentFilters = {
        timeRange: '6',
        vehicleFilter: 'all',
        chartType: 'bar'
    };

    loadChartData();
}

function toggleConsumptionView() {
    if (consumptionChart) {
        const isBar = consumptionChart.config.type === 'bar';
        consumptionChart.config.type = isBar ? 'line' : 'bar';
        consumptionChart.update();
    }
}

function toggleTrendLine() {
    showInfo('Fonctionnalité de ligne de tendance bientôt disponible');
}

function exportConsumptionChart() {
    if (consumptionChart) {
        const link = document.createElement('a');
        link.download = `consommation-vehicules-${new Date().toISOString().split('T')[0]}.png`;
        link.href = document.getElementById('avgConsumptionChart').toDataURL();
        link.click();
    }
}

function exportCostChart() {
    if (monthlyCostChart) {
        const link = document.createElement('a');
        link.download = `cout-carburant-${new Date().toISOString().split('T')[0]}.png`;
        link.href = document.getElementById('monthlyCostChart').toDataURL();
        link.click();
    }
}

// Fonctions de gestion du chargement
function showLoading(message = 'Chargement des données...') {
    loadingOverlay.querySelector('p').textContent = message;
   // document.querySelector('.container-fluid').appendChild(loadingOverlay);
}

function hideLoading() {
    if (loadingOverlay.parentNode) {
        loadingOverlay.parentNode.removeChild(loadingOverlay);
    }
}

// Fonctions de notification
function showSuccess(message) {
    showNotification(message, 'success');
}

function showError(message) {
    showNotification(message, 'danger');
}

function showInfo(message) {
    showNotification(message, 'info');
}

function showNotification(message, type) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alert);

    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Données de démonstration en cas d'erreur
function loadDemoData() {
    const demoData = {
        consumption: {
            labels: ['Véhicule A', 'Véhicule B', 'Véhicule C'],
            values: [8.5, 7.2, 9.1],
            vehicleIds: [1, 2, 3]
        },
        monthlyCost: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            values: [450000, 520000, 480000, 610000, 580000, 530000]
        },
        statistics: {
            avgConsumption: 8.3,
            minConsumption: 7.2,
            maxConsumption: 9.1,
            totalCost: 3170000,
            avgMonthlyCost: 528333,
            potentialSavings: 475500
        }
    };

    updateConsumptionChart(demoData.consumption);
    updateMonthlyCostChart(demoData.monthlyCost);
    updateStatistics(demoData.statistics);
    showInfo('Données de démonstration affichées (erreur API)');
}
</script>
@endpush
