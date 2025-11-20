@extends('layouts.app')

@section('title', 'Gestion des Paiements')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-money-bill-wave"></i> Gestion des Paiements
                    </h4>
                    <div>
                        <a href="{{ route('paiements.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau Paiement
                        </a>
                        <a href="{{ route('paiements.statistics') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> Statistiques
                        </a>
                        <a href="{{ route('suivi-factures.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour aux Factures
                        </a>
                    </div>
                </div>

                <!-- Filtres et Recherche -->
                <div class="card-body bg-light">
                    <form method="GET" action="{{ route('paiements.index') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Facture</label>
                                <select name="facture_id" class="form-control">
                                    <option value="">Toutes les factures</option>
                                    @foreach($factures as $facture)
                                    <option value="{{ $facture->id }}"
                                        {{ request('facture_id') == $facture->id ? 'selected' : '' }}>
                                        {{ $facture->numero_facture }} - {{ $facture->client->nom }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Mode de paiement</label>
                                <select name="mode_paiement" class="form-control">
                                    <option value="">Tous les modes</option>
                                    @foreach($modesPaiement as $mode)
                                    <option value="{{ $mode }}"
                                        {{ request('mode_paiement') == $mode ? 'selected' : '' }}>
                                        {{ ucfirst($mode) }}
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
                                           placeholder="Référence, client..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <a href="{{ route('paiements.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-redo"></i>
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

                    @if($paiements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Facture</th>
                                    <th>Client</th>
                                    <th>Montant</th>
                                    <th>Mode</th>
                                    <th>Référence</th>
                                    <th>Statut</th>
                                    <th>Créé le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paiements as $paiement)
                                <tr>
                                    <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                    <td>
                                        <strong>
                                            <a href="{{ route('suivi-factures.show', $paiement->suivi_facture_id) }}" class="text-dark">
                                                {{ $paiement->facture->numero_facture }}
                                            </a>
                                        </strong>
                                    </td>
                                    <td>
                                        <a href="{{ route('clients.show', $paiement->facture->client->id) }}" class="text-dark">
                                            {{ $paiement->facture->client->nom }}
                                        </a>
                                        @if($paiement->facture->client->ville)
                                        <br><small class="text-muted">{{ $paiement->facture->client->ville }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-pill" style="font-size: 1em;">
                                            {{ number_format($paiement->montant, 0, ',', ' ') }} €
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $paiement->mode_paiement_libelle }}</span>
                                    </td>
                                    <td>
                                        @if($paiement->reference)
                                        <small>{{ $paiement->reference }}</small>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $paiement->statut == 'complet' ? 'success' : ($paiement->statut == 'partiel' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($paiement->statut) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $paiement->created_at->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('paiements.show', $paiement->id) }}"
                                               class="btn btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('paiements.edit', $paiement->id) }}"
                                               class="btn btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('paiements.destroy', $paiement->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"
                                                        title="Supprimer"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?')">
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
                            Affichage de {{ $paiements->firstItem() }} à {{ $paiements->lastItem() }}
                            sur {{ $paiements->total() }} paiements
                        </div>
                        <div>
                            {{ $paiements->links() }}
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Aucun paiement trouvé.
                        @if(request()->hasAny(['facture_id', 'date_debut', 'date_fin', 'mode_paiement', 'search']))
                        <br><a href="{{ route('paiements.index') }}" class="btn btn-primary mt-2">Afficher tous les paiements</a>
                        @else
                        <br><a href="{{ route('paiements.create') }}" class="btn btn-primary mt-2">Créer le premier paiement</a>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Totaux -->
                @if($paiements->count() > 0)
                <div class="card-footer bg-dark text-white">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h5 class="mb-0">{{ $paiements->total() }}</h5>
                            <small>Total Paiements</small>
                        </div>
                        <div class="col-md-3">
                            <h5 class="mb-0 text-success">
                                {{ number_format($paiements->sum('montant'), 0, ',', ' ') }} €
                            </h5>
                            <small>Montant Total</small>
                        </div>
                        <div class="col-md-3">
                            <h5 class="mb-0 text-info">
                                {{ $paiements->where('statut', 'complet')->count() }}
                            </h5>
                            <small>Paiements Complets</small>
                        </div>
                        <div class="col-md-3">
                            <h5 class="mb-0 text-warning">
                                {{ $paiements->where('statut', 'partiel')->count() }}
                            </h5>
                            <small>Paiements Partiels</small>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
