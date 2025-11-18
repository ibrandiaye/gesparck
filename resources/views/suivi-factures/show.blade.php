@extends('layouts.app')

@section('title', 'Facture ' . $suiviFacture->numero_facture)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice-dollar"></i> Facture {{ $suiviFacture->numero_facture }}
                    </h4>
                    <div>
                        <a href="{{ route('suivi-factures.edit', $suiviFacture->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('suivi-factures.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Informations de la facture -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Informations de la Facture</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Numéro de facture:</strong></td>
                                            <td>
                                                <span class="badge badge-dark" style="font-size: 1.1em;">
                                                    {{ $suiviFacture->numero_facture }}
                                                </span>
                                            </td>
                                        </tr>
                                          <tr>
                                            <td><strong>Date Facture:</strong></td>
                                            <td>{{ $suiviFacture->date_facture->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date de livraison:</strong></td>
                                            <td>{{  $suiviFacture->date_livraison ? $suiviFacture->date_livraison->format('d/m/Y') : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Montant:</strong></td>
                                            <td>
                                                <h4 class="text-success mb-0">
                                                    {{ number_format($suiviFacture->montant, 2, ',', ' ') }} CFA
                                                </h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Mois de livraison:</strong></td>
                                            <td>{{ $suiviFacture->mois_livraison }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Informations du client -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Informations du Client</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Client:</strong></td>
                                            <td>
                                                <strong>
                                                    <a href="{{ route('clients.show', $suiviFacture->client->id) }}" class="text-dark">
                                                        {{ $suiviFacture->client->nom }}
                                                    </a>
                                                </strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Adresse:</strong></td>
                                            <td>{{ $suiviFacture->client->adresse }}</td>
                                        </tr>
                                        @if($suiviFacture->client->ville || $suiviFacture->client->code_postal)
                                        <tr>
                                            <td><strong>Localisation:</strong></td>
                                            <td>
                                                @if($suiviFacture->client->code_postal && $suiviFacture->client->ville)
                                                {{ $suiviFacture->client->code_postal }}, {{ $suiviFacture->client->ville }}
                                                @elseif($suiviFacture->client->ville)
                                                {{ $suiviFacture->client->ville }}
                                                @elseif($suiviFacture->client->code_postal)
                                                {{ $suiviFacture->client->code_postal }}
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @if($suiviFacture->client->telephone)
                                        <tr>
                                            <td><strong>Téléphone:</strong></td>
                                            <td>
                                                <i class="fas fa-phone text-muted"></i>
                                                {{ $suiviFacture->client->telephone }}
                                            </td>
                                        </tr>
                                        @endif
                                        @if($suiviFacture->client->email)
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>
                                                <i class="fas fa-envelope text-muted"></i>
                                                <a href="mailto:{{ $suiviFacture->client->email }}">{{ $suiviFacture->client->email }}</a>
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques du client -->
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Statistiques du Client</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $statsClient = $suiviFacture->client->statistiques;
                            @endphp
                            <div class="row text-center">
                                {{-- <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-primary mb-0">{{ $statsClient['total_trajets'] }}</h4>
                                        <small class="text-muted">Trajets effectués</small>
                                    </div>
                                </div> --}}
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-success mb-0">{{ $statsClient['total_factures'] }}</h4>
                                        <small class="text-muted">Total factures</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-warning mb-0">
                                            {{ number_format($statsClient['montant_total_factures'], 2, ',', ' ') }} CFA
                                        </h4>
                                        <small class="text-muted">Montant total</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3">
                                        <h4 class="text-info mb-0">
                                            @if($statsClient['total_factures'] > 0)
                                            {{ number_format($statsClient['montant_total_factures'] / $statsClient['total_factures'], 2, ',', ' ') }} CFA
                                            @else
                                            0,00 CFA
                                            @endif
                                        </h4>
                                        <small class="text-muted">Moyenne/facture</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-muted">
                    <small>
                        Facture créée le {{ $suiviFacture->created_at->format('d/m/Y à H:i') }}
                        @if($suiviFacture->created_at != $suiviFacture->updated_at)
                        - Modifiée le {{ $suiviFacture->updated_at->format('d/m/Y à H:i') }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
