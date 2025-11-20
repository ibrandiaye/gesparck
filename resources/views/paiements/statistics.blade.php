@extends('layouts.app')

@section('title', 'Statistiques Paiements')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-line"></i> Statistiques des Paiements
                    </h4>
                    <div>
                        <a href="{{ route('paiements.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour aux Paiements
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Cartes de statistiques globales -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card bg-primary text-white text-center">
                                <div class="card-body py-3">
                                    <h3 class="mb-0">{{ $stats['total_paiements'] }}</h3>
                                    <small>Total Paiements</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card bg-success text-white text-center">
                                <div class="card-body py-3">
                                    <h3 class="mb-0">{{ number_format($stats['montant_total'], 0, ',', ' ') }} €</h3>
                                    <small>Montant Total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card bg-info text-white text-center">
                                <div class="card-body py-3">
                                    <h3 class="mb-0">{{ number_format($stats['montant_total'] / max($stats['total_paiements'], 1), 0, ',', ' ') }} €</h3>
                                    <small>Moyenne/Paiement</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="card bg-warning text-dark text-center">
                                <div class="card-body py-3">
                                    <h3 class="mb-0">{{ $stats['factures_impayees'] }}</h3>
                                    <small>Factures Impayées</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paiements par mode -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Répartition par Mode de Paiement</h5>
                                </div>
                                <div class="card-body">
                                    @if($stats['paiements_par_mode']->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Mode de paiement</th>
                                                    <th>Nombre</th>
                                                    <th>Montant total</th>
                                                    <th>Pourcentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($stats['paiements_par_mode'] as $mode)
                                                <tr>
                                                    <td>
                                                        @php
                                                            $modesLibelle = [
                                                                'virement' => 'Virement',
                                                                'cheque' => 'Chèque',
                                                                'espece' => 'Espèces',
                                                                'carte' => 'Carte bancaire'
                                                            ];
                                                        @endphp
                                                        <span class="badge bg-primary">{{ $modesLibelle[$mode->mode_paiement] ?? $mode->mode_paiement }}</span>
                                                    </td>
                                                    <td>{{ $mode->nombre_paiements }}</td>
                                                    <td>{{ number_format($mode->montant_total, 0, ',', ' ') }} €</td>
                                                    <td width="30%">
                                                        @if($stats['montant_total'] > 0)
                                                        @php
                                                            $percentage = ($mode->montant_total / $stats['montant_total']) * 100;
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

                        <!-- Évolution mensuelle -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Évolution Mensuelle (12 derniers mois)</h5>
                                </div>
                                <div class="card-body">
                                    @if($stats['paiements_par_mois']->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Mois</th>
                                                    <th>Nombre</th>
                                                    <th>Montant</th>
                                                    <th>Tendance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($stats['paiements_par_mois'] as $stat)
                                                @php
                                                    $date = \Carbon\Carbon::create($stat->annee, $stat->mois, 1);
                                                    $moyenne = $stat->nombre_paiements > 0 ? $stat->montant_total / $stat->nombre_paiements : 0;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $date->locale('fr')->monthName }} {{ $stat->annee }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary bg-pill">{{ $stat->nombre_paiements }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-success">
                                                            {{ number_format($stat->montant_total, 0, ',', ' ') }} €
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($stat->nombre_paiements > 0)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-chart-line"></i> Actif
                                                        </span>
                                                        @else
                                                        <span class="badge bg-secondary">Aucun</span>
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

                    <!-- Résumé financier -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Résumé Financier</h5>
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <h4 class="text-primary">{{ $stats['total_paiements'] }}</h4>
                                            <small class="text-muted">Transactions totales</small>
                                        </div>
                                        <div class="col-md-4">
                                            <h4 class="text-success">{{ number_format($stats['montant_total'], 0, ',', ' ') }} €</h4>
                                            <small class="text-muted">Chiffre d'affaires</small>
                                        </div>
                                        <div class="col-md-4">
                                            <h4 class="text-info">{{ number_format($stats['montant_total'] / max($stats['total_paiements'], 1), 0, ',', ' ') }} €</h4>
                                            <small class="text-muted">Ticket moyen</small>
                                        </div>
                                    </div>
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
