@extends('layouts.app')

@section('title', 'Rapports & Statistiques')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-chart-bar"></i> Rapports & Statistiques</h1>
    <div class="btn-group">
        <button class="btn btn-outline-primary" id="exportPdf">
            <i class="fas fa-file-pdf"></i> Export PDF
        </button>
        <button class="btn btn-outline-success" id="exportExcel">
            <i class="fas fa-file-excel"></i> Export Excel
        </button>
    </div>
</div>

<!-- Filtres des rapports -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Filtres des Rapports</h5>
    </div>
    <div class="card-body">
        <form id="reportFilters" class="row g-3">
            @csrf
            <div class="col-md-3">
                <label for="periode" class="form-label">Période</label>
                <select class="form-select" id="periode" name="periode">
                    <option value="7">7 derniers jours</option>
                    <option value="30" selected>30 derniers jours</option>
                    <option value="90">3 derniers mois</option>
                    <option value="180">6 derniers mois</option>
                    <option value="365">1 an</option>
                    <option value="custom">Période personnalisée</option>
                </select>
            </div>
            <div class="col-md-3 d-none" id="customDateRange">
                <label for="date_debut" class="form-label">Date de début</label>
                <input type="date" class="form-control" id="date_debut" name="date_debut">
            </div>
            <div class="col-md-3 d-none" id="customDateRangeEnd">
                <label for="date_fin" class="form-label">Date de fin</label>
                <input type="date" class="form-control" id="date_fin" name="date_fin">
            </div>
            <div class="col-md-3">
                <label for="vehicle_id" class="form-label ">Véhicule</label>
                <select class="form-select select2" id="vehicle_id" name="vehicle_id">
                    <option value="all">Tous les véhicules</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->immatriculation }}</option>
                    @endforeach
                </select>
            </div>
            {{-- <div class="col-md-3">
                <label for="type_rapport" class="form-label">Type de Rapport</label>
                <select class="form-select" id="type_rapport" name="type_rapport">
                    <option value="global">Vue globale</option>
                    <option value="carburant">Carburant uniquement</option>
                    <option value="entretien">Entretien uniquement</option>
                    <option value="comparatif">Analyse comparative</option>
                </select>
            </div> --}}
            <input type="hidden" id="type_rapport" name="type_rapport" value="global">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sync-alt"></i> Générer le Rapport
                </button>
                <button type="button" id="resetFilters" class="btn btn-outline-secondary">
                    <i class="fas fa-undo"></i> Réinitialiser
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Cartes de synthèse -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 id="stat-cout-total">0 FCFA</h4>
                        <p>Coût Total</p>
                    </div>
                    <i class="fas fa-money-bill-wave fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 id="stat-consommation-moy">0 L/100km</h4>
                        <p>Consommation Moy.</p>
                    </div>
                    <i class="fas fa-gas-pump fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 id="stat-interventions">0</h4>
                        <p>Interventions</p>
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
                        <h4 id="stat-remplissages">0</h4>
                        <p>Remplissages</p>
                    </div>
                    <i class="fas fa-oil-can fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques principaux -->
<div class="row">
    <!-- Répartition des coûts -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Répartition des Coûts</h5>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" onclick="toggleChartType('costDistribution')">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="costDistributionChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Évolution des coûts mensuels -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Évolution des Coûts Mensuels</h5>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" onclick="toggleChartView('monthlyCosts')">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="monthlyCostsChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tableaux détaillés -->
<div class="row mt-4">
    <!-- Top 5 véhicules les plus chers -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-trophy"></i> Top 5 Véhicules - Coûts Totaux</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="topVehiclesTable">
                        <thead>
                            <tr>
                                <th>Véhicule</th>
                                <th>Coût Carburant</th>
                                 <th>Nombre Trajets</th>
                                <th>Coût Entretien</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Les données seront chargées via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Types d'intervention les plus fréquents -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-wrench"></i> Types d'Intervention</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="interventionTypesTable">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Nombre</th>
                                <th>Coût Total</th>
                                <th>Coût Moyen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Les données seront chargées via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rapport détaillé -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list-alt"></i> Rapport Détaillé</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="detailedReportTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Véhicule</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Coût</th>
                        <th>Kilométrage</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Les données seront chargées via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Section d'export -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-download"></i> Export des Données</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-file-pdf fa-3x text-primary mb-3"></i>
                        <h5>Rapport PDF</h5>
                        <p class="text-muted">Rapport complet avec graphiques</p>
                        <button class="btn btn-outline-primary w-100" id="generatePdf">
                            <i class="fas fa-download me-2"></i>Générer PDF
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                        <h5>Export Excel</h5>
                        <p class="text-muted">Données brutes format Excel</p>
                        <button class="btn btn-outline-success w-100" id="generateExcel">
                            <i class="fas fa-download me-2"></i>Exporter Excel
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <i class="fas fa-file-csv fa-3x text-info mb-3"></i>
                        <h5>Export CSV</h5>
                        <p class="text-muted">Données au format CSV</p>
                        <button class="btn btn-outline-info w-100" id="generateCsv">
                            <i class="fas fa-download me-2"></i>Exporter CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>


<script>
// Variables globales

let costDistributionChart, monthlyCostsChart;
let currentFilters = {
    periode: '30',
    vehicle_id: 'all',
    type_rapport: 'global'
};

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    setupEventListeners();
    loadReportData();
});

function initializeCharts() {
    // Graphique de répartition des coûts
    const costCtx = document.getElementById('costDistributionChart').getContext('2d');
    costDistributionChart = new Chart(costCtx, {
        type: 'doughnut',
        data: {
            labels: ['Carburant', 'Entretien', 'Autres'],
            datasets: [{
                data: [0, 0, 0],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value.toLocaleString('fr-FR')} FCFA (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Graphique d'évolution des coûts mensuels
    const monthlyCtx = document.getElementById('monthlyCostsChart').getContext('2d');
    monthlyCostsChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Carburant',
                    data: [],
                    borderColor: '#FF6384',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Entretien',
                    data: [],
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fr-FR') + ' FCFA';
                        }
                    }
                }
            }
        }
    });
}

function setupEventListeners() {
    // Gestion des filtres
    document.getElementById('reportFilters').addEventListener('submit', function(e) {
        e.preventDefault();
        loadReportData();
    });

    document.getElementById('resetFilters').addEventListener('click', resetFilters);

    // Gestion de la période personnalisée
    document.getElementById('periode').addEventListener('change', function() {
        const isCustom = this.value === 'custom';
        document.getElementById('customDateRange').classList.toggle('d-none', !isCustom);
        document.getElementById('customDateRangeEnd').classList.toggle('d-none', !isCustom);

        if (!isCustom) {
            loadReportData();
        }
    });
      document.getElementById('vehicle_id').addEventListener('change', function() {
        const isCustom = this.value === 'custom';
        document.getElementById('customDateRange').classList.toggle('d-none', !isCustom);
        document.getElementById('customDateRangeEnd').classList.toggle('d-none', !isCustom);

        if (!isCustom) {
            loadReportData();
        }
    });
    document.getElementById('type_rapport').addEventListener('change', function() {
        const isCustom = this.value === 'custom';
        document.getElementById('customDateRange').classList.toggle('d-none', !isCustom);
        document.getElementById('customDateRangeEnd').classList.toggle('d-none', !isCustom);

        if (!isCustom) {
            loadReportData();
        }
    });


    // Exports
    document.getElementById('generatePdf').addEventListener('click', generatePdf);
    document.getElementById('generateExcel').addEventListener('click', generateExcel);
    document.getElementById('generateCsv').addEventListener('click', generateCsv);
}

async function loadReportData() {
    showLoading();

    try {
        const formData = new FormData(document.getElementById('reportFilters'));

        // Convertir FormData en objet simple
        const filters = {};
        for (let [key, value] of formData.entries()) {
            filters[key] = value;
        }

        // Débogage: voir ce qui est envoyé
        console.log('Filtres envoyés:', filters);

        const response = await fetch('{{ route("reports.data") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(filters)
        });

        const data = await response.json();
        console.log('Réponse reçue:', data.data.tables);

        if (data.success) {
            updateCharts(data.data.charts);
            updateStatistics(data.data.statistics);
            updateTables(data.data.tables);
           /*   */
            showSuccess('Rapport généré avec succès');
        } else {
            throw new Error(data.message || 'Erreur inconnue');
        }

    } catch (error) {
        console.error('Erreur détaillée:', error);
        showError('Erreur lors du chargement des données: ' + error.message);

        // Charger des données de démonstration en cas d'erreur
        loadDemoData();
    } finally {
        hideLoading();
    }
}

function updateCharts(chartsData) {
    // Répartition des coûts
    costDistributionChart.data.datasets[0].data = [
        chartsData.costDistribution.fuel,
        chartsData.costDistribution.repairs,
        chartsData.costDistribution.other
    ];
    costDistributionChart.update();

    // Évolution mensuelle
    monthlyCostsChart.data.labels = chartsData.monthlyTrends.months;
    monthlyCostsChart.data.datasets[0].data = chartsData.monthlyTrends.fuel;
    monthlyCostsChart.data.datasets[1].data = chartsData.monthlyTrends.repairs;
    monthlyCostsChart.update();
}

function updateStatistics(stats) {
    document.getElementById('stat-cout-total').textContent =
        stats.totalCost.toLocaleString('fr-FR') + ' FCFA';
    document.getElementById('stat-consommation-moy').textContent =
        stats.avgConsumption + ' L/100km';
    document.getElementById('stat-interventions').textContent =
        stats.totalInterventions;
    document.getElementById('stat-remplissages').textContent =
        stats.totalRefuels;
}

function updateTables(tables) {
    if ($.fn.DataTable.isDataTable('#topVehiclesTable')) {
                $('#topVehiclesTable').DataTable().clear().destroy();
            }
            $('#topVehiclesTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/fr-FR.json',
            },
             ordering:false,

            });
            if ($.fn.DataTable.isDataTable('#detailedReportTable')) {
                $('#detailedReportTable').DataTable().clear().destroy();
            }
            $('#detailedReportTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/fr-FR.json',
            },
            ordering:false,

            });
          if ($.fn.DataTable.isDataTable('#interventionTypesTable')) {
                $('#interventionTypesTable').DataTable().clear().destroy();
            }
            $('#interventionTypesTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/fr-FR.json',
            },

            });
        // Top véhicules
        console.log(tables.topVehicles[0].repair_cost + "ibra");
        const topVehiclesBody = document.querySelector('#topVehiclesTable tbody');
        topVehiclesBody.innerHTML = tables.topVehicles.map(vehicle => `
        <tr>
            <td>${vehicle.immatriculation}</td>
            <td>${vehicle.fuel_cost.toLocaleString('fr-FR')} FCFA</td>
            <td>${vehicle.trajets} </td>
            <td>${vehicle.repair_cost.toLocaleString('fr-FR')} FCFA</td>
            <td><strong>${vehicle.total_cost.toLocaleString('fr-FR')} FCFA</strong></td>
        </tr>
    `).join('');

    // Types d'intervention
    const interventionTypesBody = document.querySelector('#interventionTypesTable tbody');
    interventionTypesBody.innerHTML = tables.interventionTypes.map(type => `
        <tr>
            <td>${type.type_label}</td>
            <td>${type.count}</td>
            <td>${type.total_cost.toLocaleString('fr-FR')} FCFA</td>
            <td>${type.avg_cost.toLocaleString('fr-FR')} FCFA</td>
        </tr>
    `).join('');

    // Rapport détaillé
    const detailedBody = document.querySelector('#detailedReportTable tbody');
    detailedBody.innerHTML = tables.detailedReport.map(entry => `
        <tr>
            <td>${entry.date}</td>
            <td>${entry.vehicle}</td>
            <td>
                <span class="badge bg-${entry.type === 'carburant' ? 'primary' : 'warning'}">
                    ${entry.type_label}
                </span>
            </td>
            <td>${entry.description}</td>
            <td>${entry.cost.toLocaleString('fr-FR')} FCFA</td>
            <td>${entry.kilometrage ? entry.kilometrage.toLocaleString('fr-FR') + ' km' : 'N/A'}</td>
        </tr>
    `).join('');

}

function resetFilters() {
    document.getElementById('reportFilters').reset();
    document.getElementById('customDateRange').classList.add('d-none');
    document.getElementById('customDateRangeEnd').classList.add('d-none');
    currentFilters = {
        periode: '30',
        vehicle_id: 'all',
        type_rapport: 'global'
    };
    loadReportData();
}

function toggleChartType(chartId) {
    if (chartId === 'costDistribution') {
        const isDoughnut = costDistributionChart.config.type === 'doughnut';
        costDistributionChart.config.type = isDoughnut ? 'pie' : 'doughnut';
        costDistributionChart.update();
    }
}

function toggleChartView(chartId) {
    if (chartId === 'monthlyCosts') {
        const isLine = monthlyCostsChart.config.type === 'line';
        monthlyCostsChart.config.type = isLine ? 'bar' : 'line';
        monthlyCostsChart.update();
    }
}

function generatePdf() {
    showLoading('Génération du PDF...');
    // Implémentation PDF à venir
    setTimeout(() => {
        hideLoading();
        showSuccess('PDF généré avec succès');
    }, 2000);
}

function generateExcel() {
    showLoading('Export Excel en cours...');
    // Implémentation Excel à venir
    setTimeout(() => {
        hideLoading();
        showSuccess('Export Excel terminé');
    }, 2000);
}

function generateCsv() {
    showLoading('Export CSV en cours...');
    // Implémentation CSV à venir
    setTimeout(() => {
        hideLoading();
        showSuccess('Export CSV terminé');
    }, 2000);
}

// Fonctions utilitaires
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
    // Créer une notification toast Bootstrap
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    const container = document.getElementById('toastContainer') || createToastContainer();
    container.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    // Nettoyer après fermeture
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// Fonctions de loading
function showLoading(message = 'Chargement...') {
    let loadingOverlay = document.getElementById('loadingOverlay');

    if (!loadingOverlay) {
        loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'loadingOverlay';
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-2 text-muted">${message}</p>
            </div>
        `;
        document.body.appendChild(loadingOverlay);
    }

    loadingOverlay.style.display = 'flex';
}

function hideLoading() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.style.display = 'none';
    }
}

function loadDemoData() {
    console.log('Chargement des données de démonstration...');

    const demoData = {
        charts: {
            costDistribution: {
                fuel: 4500000,
                repairs: 3200000,
                other: 500000
            },
            monthlyTrends: {
                months: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                fuel: [650000, 720000, 580000, 810000, 690000, 750000],
                repairs: [450000, 520000, 380000, 610000, 490000, 550000]
            }
        },
        statistics: {
            totalCost: 8200000,
            avgConsumption: 8.3,
            totalInterventions: 24,
            totalRefuels: 48
        },
        tables: {
            topVehicles: [
                {
                    immatriculation: 'AB-123-CD',
                    fuel_cost: 1200000,
                    repair_cost: 850000,
                    total_cost: 2050000
                },
                {
                    immatriculation: 'EF-456-GH',
                    fuel_cost: 980000,
                    repair_cost: 720000,
                    total_cost: 1700000
                },
                {
                    immatriculation: 'IJ-789-KL',
                    fuel_cost: 1150000,
                    repair_cost: 540000,
                    total_cost: 1690000
                },
                {
                    immatriculation: 'MN-012-OP',
                    fuel_cost: 760000,
                    repair_cost: 680000,
                    total_cost: 1440000
                },
                {
                    immatriculation: 'QR-345-ST',
                    fuel_cost: 810000,
                    repair_cost: 410000,
                    total_cost: 1220000
                }
            ],
            interventionTypes: [
                {
                    type_label: 'Entretien Routine',
                    count: 8,
                    total_cost: 1200000,
                    avg_cost: 150000
                },
                {
                    type_label: 'Vidange',
                    count: 6,
                    total_cost: 800000,
                    avg_cost: 133333
                },
                {
                    type_label: 'Réparation',
                    count: 5,
                    total_cost: 2500000,
                    avg_cost: 500000
                },
                {
                    type_label: 'Freinage',
                    count: 3,
                    total_cost: 600000,
                    avg_cost: 200000
                },
                {
                    type_label: 'Pneumatique',
                    count: 2,
                    total_cost: 400000,
                    avg_cost: 200000
                }
            ],
            detailedReport: [
                {
                    date: '15/06/2024',
                    vehicle: 'AB-123-CD',
                    type: 'carburant',
                    type_label: 'Carburant',
                    description: '45.5L @ 650 FCFA/L',
                    cost: 29575,
                    kilometrage: 125430
                },
                {
                    date: '10/06/2024',
                    vehicle: 'AB-123-CD',
                    type: 'entretien',
                    type_label: 'Vidange',
                    description: 'Vidange moteur + filtre',
                    cost: 85000,
                    kilometrage: 125000
                },
                {
                    date: '05/06/2024',
                    vehicle: 'EF-456-GH',
                    type: 'carburant',
                    type_label: 'Carburant',
                    description: '38.2L @ 655 FCFA/L',
                    cost: 25021,
                    kilometrage: 89210
                }
            ]
        }
    };

    updateCharts(demoData.charts);
    updateStatistics(demoData.statistics);
    updateTables(demoData.tables);

    showInfo('Données de démonstration affichées (erreur API)');
}
async function generatePdf() {
    try {
        showLoading('Génération du PDF...');

        const formData = new FormData(document.getElementById('reportFilters'));
        const filters = Object.fromEntries(formData.entries());

        const response = await fetch('{{ route("reports.export.pdf") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(filters)
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = `rapport-flotte-${new Date().toISOString().split('T')[0]}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            showSuccess('PDF généré avec succès');
        } else {
            const error = await response.json();
            throw new Error(error.message || 'Erreur lors de la génération du PDF');
        }

    } catch (error) {
        console.error('Erreur PDF:', error);
        showError('Erreur: ' + error.message);
    } finally {
        hideLoading();
    }
}

async function generateExcel() {
    try {
        showLoading('Export Excel en cours...');

        const formData = new FormData(document.getElementById('reportFilters'));
        const filters = Object.fromEntries(formData.entries());
        const typeRapport = document.getElementById('type_rapport').value;

        // Ajouter le type de rapport pour l'export
        filters.export_type = typeRapport;

        const response = await fetch('{{ route("reports.export.excel") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(filters)
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = `rapport-flotte-${new Date().toISOString().split('T')[0]}.xlsx`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            showSuccess('Export Excel terminé');
        } else {
            const error = await response.json();
            throw new Error(error.message || 'Erreur lors de l\'export Excel');
        }

    } catch (error) {
        console.error('Erreur Excel:', error);
        showError('Erreur: ' + error.message);
    } finally {
        hideLoading();
    }
}

async function generateCsv() {
    try {
        showLoading('Export CSV en cours...');

        const formData = new FormData(document.getElementById('reportFilters'));
        const filters = Object.fromEntries(formData.entries());
        const typeRapport = document.getElementById('type_rapport').value;

        // Ajouter le type de rapport pour l'export
        filters.export_type = typeRapport;

        const response = await fetch('{{ route("reports.export.csv") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(filters)
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = `rapport-flotte-${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            showSuccess('Export CSV terminé');
        } else {
            const error = await response.json();
            throw new Error(error || 'Erreur lors de l\'export CSV');
        }

    } catch (error) {
        console.error('Erreur CSV:', error);
        showError('Erreur: ' + error.message);
    } finally {
        hideLoading();
    }
}
</script>
@endpush

@push('styles')
<style>
.card {
    margin-bottom: 1rem;
    transition: transform 0.2s ease-in-out;
}
.card:hover {
    transform: translateY(-2px);
}
.table th {
    border-top: none;
    font-weight: 600;
}
.badge {
    font-size: 0.75em;
}
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
</style>
@endpush
