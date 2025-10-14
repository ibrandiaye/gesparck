@extends('layouts.app')

@section('title', 'Historique Carburant')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-gas-pump"></i> Gestion du Carburant</h1>
    <a href="{{ route('carburants.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouveau Carburant
    </a>
</div>



<!-- Tableau des remplissages -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-history"></i> Liste des carburants</h5>
    </div>
    <div class="card-body">
        @if($carburants->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Libelle</th>
                        <th>Prix/L</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($carburants as $entry)
                    <tr>
                        <td>{{ $entry->libelle }}</td>
                        <td>{{ $entry->montant }}</td>

                        <td>
                            <div class="btn-group btn-group-sm">

                                <a href="{{ route('carburants.edit', $entry) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('carburants.destroy', $entry) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger"
                                            onclick="return confirm('Supprimer ce remplissage?')">
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
        <div class="d-flex justify-content-center mt-3">
            {{ $carburants->links() }}
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-gas-pump fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Aucun remplissage enregistré</h5>
            <p class="text-muted">Commencez par ajouter votre premier remplissage de carburants.</p>
            <a href="{{ route('carburants.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un Remplissage
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
