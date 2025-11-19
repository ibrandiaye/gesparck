@extends('layouts.app')

@section('title', 'Statistiques Factures')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-line"></i> Statistiques des Factures
                    </h4>
                    <div>
                        <a href="{{ route('suivi-factures.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour aux factures
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Cartes de statistiques globales -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card bg-primary text-white text-center">
                                <div class="card-body py-3">
                                    <h3 class="mb-0">{{ $stats['total_factures'] }}</h3>
                                    <small>Total Factures</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card bg-success text-white text-center">
                                <div class="card-body py-3">
                                    <h3 class="mb-0">{{ number_format($stats['montant_total'], 0, ',', ' ') }} CFA</h3>
                                    <small>Montant Total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card bg-info text-white text-center">
                                <div class="card-body py-3">
                                    <h3 class="mb-0">{{ number_format($stats['moyenne_montant'], 0, ',', ' ') }} CFA</h3>
                                    <small>Moyenne/Facture</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card bg-warning text-dark text-center">
                                <div class="card-body py-3">
                                    <h3 class="mb-0">{{ $stats['top_clients']->count() }}</h3>
                                    <small>Clients Facturés</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top clients par chiffre d'affaires -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Top 10 des Clients par Chiffre d'Affaires</h5>
                                </div>
                                <div class="card-body">
                                    @if($stats['top_clients']->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Client</th>
                                                    <th>Nombre de factures</th>
                                                    <th>Chiffre d'affaires</th>
                                                    <th>Moyenne/facture</th>
                                                    <th>Pourcentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $totalCA = $stats['top_clients']->sum('montant_total');
                                                @endphp
                                                @foreach($stats['top_clients'] as $index => $client)
                                                <tr>
                                                    <td>
                                                        <span class="badge badge-{{ $index < 3 ? 'warning' : 'secondary' }}">
                                                            {{ $index + 1 }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong>
                                                            <a href="{{ route('clients.show', $client->client_id) }}" class="text-dark">
                                                                {{ $client->client->nom }}
                                                            </a>
                                                        </strong>
                                                        @if($client->client->ville)
                                                        <br><small class="text-muted">{{ $client->client->ville }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-primary badge-pill">{{ $client->nombre_factures }}</span>
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">
                                                            {{ number_format($client->montant_total, 0, ',', ' ') }} CFA
                                                        </strong>
                                                    </td>
                                                    <td>
                                                        <span class="text-info">
                                                            {{ number_format($client->montant_total / $client->nombre_factures, 0, ',', ' ') }} CFA
                                                        </span>
                                                    </td>
                                                    <td width="25%">
                                                        @if($totalCA > 0)
                                                        @php
                                                            $percentage = ($client->montant_total / $totalCA) * 100;
                                                        @endphp
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-success"
                                                                 role="progressbar"
                                                                 style="width: {{ $percentage }}%"
                                                                 aria-valuenow="{{ $percentage }}"
                                                                 aria-valuemin="0"
                                                                 aria-valuemax="100">
                                                                {{ number_format($percentage, 1) }}%
                                                            </div>
                                                        </div>
                                                        @else
                                                        <div class="text-muted">-</div>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-info-circle"></i> Aucune donnée disponible.
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Évolution mensuelle -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Évolution Mensuelle (12 derniers mois)</h5>
                                </div>
                                <div class="card-body">
                                    @if($stats['factures_par_mois']->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Mois</th>
                                                    <th>Nombre de factures</th>
                                                    <th>Chiffre d'affaires</th>
                                                    <th>Moyenne/facture</th>
                                                    <th>Évolution</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($stats['factures_par_mois'] as $stat)
                                                @php
                                                    $date = \Carbon\Carbon::create($stat->annee, $stat->mois, 1);
                                                    $moyenne = $stat->nombre_factures > 0 ? $stat->montant_total / $stat->nombre_factures : 0;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $date->locale('fr')->monthName }} {{ $stat->annee }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge badge-primary badge-pill">{{ $stat->nombre_factures }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-success">
                                                            {{ number_format($stat->montant_total, 0, ',', ' ') }} CFA
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="text-info">
                                                            {{ number_format($moyenne, 0, ',', ' ') }} CFA
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($stat->nombre_factures > 0)
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-chart-line"></i> Actif
                                                        </span>
                                                        @else
                                                        <span class="badge badge-secondary">Aucune</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-info-circle"></i> Aucune donnée disponible pour les 12 derniers mois.
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
