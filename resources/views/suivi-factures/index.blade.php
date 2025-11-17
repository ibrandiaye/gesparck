@extends('layouts.app')

@section('title', 'Suivi des Factures')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice-dollar"></i> Suivi des Factures
                    </h4>
                    <div>
                        <a href="{{ route('suivi-factures.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle Facture
                        </a>
                        <a href="{{ route('suivi-factures.statistics') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> Statistiques
                        </a>
                    </div>
                </div>

                <!-- Filtres et Recherche -->
                <div class="card-body bg-light">
                    <form method="GET" action="{{ route('suivi-factures.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Client</label>
                                <select name="client_id" class="form-control">
                                    <option value="">Tous les clients</option>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}"
                                        {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->nom }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Date début</label>
                                <input type="date" name="date_debut" class="form-control"
                                    value="{{ request('date_debut') }}">
                            </div>
                            <div class="col-md-2">
                                <label>Date fin</label>
                                <input type="date" name="date_fin" class="form-control"
                                    value="{{ request('date_fin') }}">
                            </div>
                            <div class="col-md-3">
                                <label>Recherche</label>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control"
                                           placeholder="N° facture, client..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="fas fa-search"></i> Filtrer
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <a href="{{ route('suivi-factures.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-redo"></i> Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    @endif

                    @if($factures->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>N° Facture</th>
                                    <th>Client</th>
                                    <th>Date Facture</th>
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
                                    <td>
                                        <a href="{{ route('clients.show', $facture->client->id) }}" class="text-dark">
                                            {{ $facture->client->nom }}
                                        </a>
                                        @if($facture->client->ville)
                                        <br><small class="text-muted">{{ $facture->client->ville }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $facture->date_facture->format('d/m/Y') }}</td>
                                    <td>{{ $facture->date_livraison->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-success badge-pill" style="font-size: 1em;">
                                            {{ number_format($facture->montant, 2, ',', ' ') }} CFA
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
                                            <form action="{{ route('suivi-factures.destroy', $facture->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"
                                                        title="Supprimer"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')">
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
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Affichage de {{ $factures->firstItem() }} à {{ $factures->lastItem() }}
                            sur {{ $factures->total() }} factures
                        </div>
                        <div>
                            {{ $factures->links() }}
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Aucune facture trouvée.
                        @if(request()->hasAny(['client_id', 'date_debut', 'date_fin', 'search']))
                        <br><a href="{{ route('suivi-factures.index') }}" class="btn btn-primary mt-2">Afficher toutes les factures</a>
                        @else
                        <br><a href="{{ route('suivi-factures.create') }}" class="btn btn-primary mt-2">Créer la première facture</a>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Totaux -->
                @if($factures->count() > 0)
                <div class="card-footer bg-dark text-white">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h5 class="mb-0">{{ $factures->total() }}</h5>
                            <small>Total Factures</small>
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-0 text-success">
                                {{ number_format($factures->sum('montant'), 2, ',', ' ') }} CFA
                            </h5>
                            <small>Montant Total</small>
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-0 text-info">
                                {{ number_format($factures->avg('montant'), 2, ',', ' ') }} CFA
                            </h5>
                            <small>Moyenne par Facture</small>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
