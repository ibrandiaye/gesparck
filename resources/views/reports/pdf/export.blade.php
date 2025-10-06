<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Flotte - {{ $generated_at }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
        }
        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .card {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            flex: 1;
            margin: 0 5px;
        }
        .card h3 {
            margin: 0;
            font-size: 14px;
            color: #666;
        }
        .card .value {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            background-color: #2c3e50;
            color: white;
            padding: 8px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
        .chart-placeholder {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport de Gestion de Flotte</h1>
        <p>Généré le {{ $generated_at }}</p>
        @if($filters['periode'] === 'custom')
        <p>Période : {{ \Carbon\Carbon::parse($filters['date_debut'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($filters['date_fin'])->format('d/m/Y') }}</p>
        @else
        <p>Période : {{ $filters['periode'] }} derniers jours</p>
        @endif
    </div>

    <!-- Cartes de synthèse -->
    <div class="summary-cards">
        <div class="card">
            <h3>Coût Total</h3>
            <div class="value">{{ number_format($statistics['totalCost'], 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="card">
            <h3>Consommation Moyenne</h3>
            <div class="value">{{ $statistics['avgConsumption'] }} L/100km</div>
        </div>
        <div class="card">
            <h3>Interventions</h3>
            <div class="value">{{ $statistics['totalInterventions'] }}</div>
        </div>
        <div class="card">
            <h3>Remplissages</h3>
            <div class="value">{{ $statistics['totalRefuels'] }}</div>
        </div>
    </div>

    <!-- Répartition des coûts -->
    <div class="section">
        <div class="section-title">Répartition des Coûts</div>
        <div class="chart-placeholder">
            <strong>Répartition des Coûts</strong><br>
            Carburant: {{ number_format($charts['costDistribution']['fuel'], 0, ',', ' ') }} FCFA<br>
            Entretien: {{ number_format($charts['costDistribution']['repairs'], 0, ',', ' ') }} FCFA<br>
            Autres: {{ number_format($charts['costDistribution']['other'], 0, ',', ' ') }} FCFA
        </div>
    </div>

    <!-- Top 5 véhicules -->
    <div class="section">
        <div class="section-title">Top 5 Véhicules - Coûts Totaux</div>
        <table>
            <thead>
                <tr>
                    <th>Véhicule</th>
                    <th>Coût Carburant</th>
                    <th>Coût Entretien</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables['topVehicles'] as $vehicle)
                <tr>
                    <td>{{ $vehicle['immatriculation'] }}</td>
                    <td>{{ number_format($vehicle['fuel_cost'], 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($vehicle['repair_cost'], 0, ',', ' ') }} FCFA</td>
                    <td><strong>{{ number_format($vehicle['total_cost'], 0, ',', ' ') }} FCFA</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Types d'intervention -->
    <div class="section">
        <div class="section-title">Types d'Intervention</div>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Nombre</th>
                    <th>Coût Total</th>
                    <th>Coût Moyen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables['interventionTypes'] as $type)
                <tr>
                    <td>{{ $type['type_label'] }}</td>
                    <td>{{ $type['count'] }}</td>
                    <td>{{ number_format($type['total_cost'], 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($type['avg_cost'], 0, ',', ' ') }} FCFA</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Rapport détaillé -->
    <div class="section">
        <div class="section-title">Rapport Détaillé </div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Véhicule</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Coût</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables['detailedReport'] as $entry)
                <tr>
                    <td>{{ $entry['date'] }}</td>
                    <td>{{ $entry['vehicle'] }}</td>
                    <td>{{ $entry['type_label'] }}</td>
                    <td>{{ $entry['description'] }}</td>
                    <td>{{ number_format($entry['cost'], 0, ',', ' ') }} FCFA</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Document généré automatiquement par le système de Gestion de Flotte
    </div>
</body>
</html>
