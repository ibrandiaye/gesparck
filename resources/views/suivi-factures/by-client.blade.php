@extends('layouts.app')

@section('title', 'Factures - ' . $client->nom)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice-dollar"></i> Factures de {{ $client->nom }}
                    </h4>
                    <div>
                        <a href="{{ route('suivi-factures.create', ['client_id' => $client->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle Facture
                        </a>
                        <a href="{{ route('clients.show', $client->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour au client
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Informations du client -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <strong>Client:</strong><br>
                                        {{ $client->nom }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Ville:</strong><br>
                                        {{ $client->ville ?? '-' }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Total factures:</strong><br>
                                        {{ $factures->total() }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Montant total:</strong><br>
                                        <span class="text-success">
                                            {{ number_format($factures->sum('montant'), 2, ',', ' ') }} CFA
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($factures->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>N° Facture</th>
                                    <th>Date Livraison</th>
                                    <th>Montant</th>
                                    <th>Créée le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($factures as $facture)
                                <tr>
                                    <td>
                                        <strong>{{ $facture->numero_facture }}</strong>
                                    </td>
                                    <td>{{ $facture->date_livraison->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-success badge-pill" style="font-size: 1em;">
                                            {{ number_format($facture->montant) }} CFA
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $facture->created_at->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('suivi-factures.show', $facture->id) }}"
                                               class="btn btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('suivi-factures.edit', $facture->id) }}"
                                               class="btn btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $factures->links() }}
                    </div>
                    @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Aucune facture enregistrée pour ce client.
                        <br>
                        <a href="{{ route('suivi-factures.create', ['client_id' => $client->id]) }}"
                           class="btn btn-primary mt-2">
                            <i class="fas fa-plus"></i> Créer la première facture
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
