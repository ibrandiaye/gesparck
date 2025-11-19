@extends('layouts.app')

@section('title', 'Gestion des Clients')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-users"></i> Gestion des Clients
                    </h4>
                    <div>
                        <a href="{{ route('clients.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau Client
                        </a>
                        <a href="{{ route('clients.statistics') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> Statistiques
                        </a>
                    </div>
                </div>

                <!-- Filtres et Recherche -->
                <div class="card-body bg-light">
                    <form method="GET" action="{{ route('clients.index') }}">
                        <div class="row">
                          {{--   <div class="col-md-4">
                                <label>Statut</label>
                                <select name="actif" class="form-control" onchange="this.form.submit()">
                                    <option value="1" {{ request('actif', '1') == '1' ? 'selected' : '' }}>Clients actifs</option>
                                    <option value="0" {{ request('actif') == '0' ? 'selected' : '' }}>Clients inactifs</option>
                                    <option value="" {{ request('actif') === '' ? 'selected' : '' }}>Tous les clients</option>
                                </select>
                            </div> --}}
                            <div class="col-md-8">
                                <label>Recherche</label>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control"
                                           placeholder="Nom, ..." value="{{ request('search') }}">
                                    {{-- <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Filtrer
                                        </button>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Filtrer
                                </button>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary w-100">
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

                    @if($clients->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Nom</th>
                                    <th>Adresse</th>
                                     <th>Trajets</th>
                                    <th>Voyages</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $client)
                                <tr>
                                    <td>
                                        <strong>{{ $client->nom }}</strong>

                                    </td>
                                    <td>
                                        <small>{{ Str::limit($client->adresse, 200) }}</small>

                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-primary badge-pill">{{ $client->total_trajets }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info badge-pill">{{ $client->total_voyages }}</span>
                                    </td>

                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('clients.show', $client->id) }}"
                                               class="btn btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('clients.edit', $client->id) }}"
                                               class="btn btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            @if($client->total_trajets == 0)
                                            <form action="{{ route('clients.destroy', $client->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"
                                                        title="Supprimer"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
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
                            Affichage de {{ $clients->firstItem() }} à {{ $clients->lastItem() }}
                            sur {{ $clients->total() }} clients
                        </div>
                        <div>
                            {{ $clients->links() }}
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Aucun client trouvé.
                        @if(request()->hasAny(['search', 'actif']))
                        <br><a href="{{ route('clients.index') }}" class="btn btn-primary mt-2">Afficher tous les clients</a>
                        @else
                        <br><a href="{{ route('clients.create') }}" class="btn btn-primary mt-2">Créer le premier client</a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
