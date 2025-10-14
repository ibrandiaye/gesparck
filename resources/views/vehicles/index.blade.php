@extends('layouts.app')

@section('title', 'Liste des Véhicules')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-truck"></i> Liste des Véhicules</h1>
    <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
        {{-- <i class="fas fa-plus"></i>  --}} Nouveau Véhicule
    </a>
</div>
<div class="row">
    <div class="mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Rechercher un véhicule...">
</div>

</div>
<div class="row">
    <h5>SEDIPAL  - NESTLE</h5>
    @foreach($vehicles as $vehicle)
        @if ($vehicle->categorie=="sedipal-nestle")
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge bg-{{ $vehicle->etat == 'disponible' ? 'success' : ($vehicle->etat == 'en_entretien' ? 'warning' : 'danger') }}">
                            {{ ucfirst($vehicle->etat) }}
                        </span>
                        <div>
                            <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Supprimer ce véhicule?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $vehicle->marque }} {{ $vehicle->modele }}</h5>
                        <p class="card-text">
                            <strong>Immatriculation:</strong> {{ $vehicle->immatriculation }}<br>
                            <strong>Type:</strong> {{ ucfirst($vehicle->type_vehicule) }}<br>
                            <strong>Kilométrage:</strong> {{ number_format($vehicle->kilometrage_actuel, 0, ',', ' ') }} km<br>
                            <strong>Catégorie:</strong> {{ $vehicle->categorie }}
                        </p>
                        @if($vehicle->notes)
                        <p class="card-text"><small class="text-muted">{{ Str::limit($vehicle->notes, 100) }}</small></p>
                        @endif
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-sm btn-outline-dark">
                            <i class="fas fa-eye"></i> Détails
                        </a>
                         <a href="{{ route('fuel-entries.create') }}?vehicle_id={{ $vehicle->id }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-gas-pump"></i> {{-- <i class="fas fa-plus"></i>  --}} Ajouter Carburant
                    </a>
                    <a href="{{ route('repair-logs.create') }}?vehicle_id={{ $vehicle->id }}"
                       class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-tools"></i> {{-- <i class="fas fa-plus"></i>  --}} Enregistrer Dépannage
                    </a>
                    </div>
                </div>
            </div>
        @endif

    @endforeach
    <hr>
    <h5>CDN  - NESTLE</h5>
    @foreach($vehicles as $vehicle)
        @if ($vehicle->categorie=="cdn-nestle" )
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge bg-{{ $vehicle->etat == 'disponible' ? 'success' : ($vehicle->etat == 'en_entretien' ? 'warning' : 'danger') }}">
                            {{ ucfirst($vehicle->etat) }}
                        </span>
                        <div>
                            <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Supprimer ce véhicule?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $vehicle->marque }} {{ $vehicle->modele }}</h5>
                        <p class="card-text">
                            <strong>Immatriculation:</strong> {{ $vehicle->immatriculation }}<br>
                            <strong>Type:</strong> {{ ucfirst($vehicle->type_vehicule) }}<br>
                            <strong>Kilométrage:</strong> {{ number_format($vehicle->kilometrage_actuel, 0, ',', ' ') }} km<br>
                            <strong>Catégorie:</strong> {{ $vehicle->categorie }}
                        </p>
                        @if($vehicle->notes)
                        <p class="card-text"><small class="text-muted">{{ Str::limit($vehicle->notes, 100) }}</small></p>
                        @endif
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-sm btn-outline-dark">
                            <i class="fas fa-eye"></i> Détails
                        </a>
                         <a href="{{ route('fuel-entries.create') }}?vehicle_id={{ $vehicle->id }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-gas-pump"></i> {{-- <i class="fas fa-plus"></i>  --}} Ajouter Carburant
                    </a>
                    <a href="{{ route('repair-logs.create') }}?vehicle_id={{ $vehicle->id }}"
                       class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-tools"></i> {{-- <i class="fas fa-plus"></i>  --}} Enregistrer Dépannage
                    </a>
                    </div>
                </div>
            </div>
        @endif

    @endforeach
    <hr>
    <h5>VEHICULE DE LIVRAISON SEDIPAL</h5>
    @foreach($vehicles as $vehicle)
        @if ($vehicle->categorie=="vehicule-livraison-sedipal")
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge bg-{{ $vehicle->etat == 'disponible' ? 'success' : ($vehicle->etat == 'en_entretien' ? 'warning' : 'danger') }}">
                            {{ ucfirst($vehicle->etat) }}
                        </span>
                        <div>
                            <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Supprimer ce véhicule?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $vehicle->marque }} {{ $vehicle->modele }}</h5>
                        <p class="card-text">
                            <strong>Immatriculation:</strong> {{ $vehicle->immatriculation }}<br>
                            <strong>Type:</strong> {{ ucfirst($vehicle->type_vehicule) }}<br>
                            <strong>Kilométrage:</strong> {{ number_format($vehicle->kilometrage_actuel, 0, ',', ' ') }} km<br>
                            <strong>Catégorie:</strong> {{ $vehicle->categorie }}
                        </p>
                        @if($vehicle->notes)
                        <p class="card-text"><small class="text-muted">{{ Str::limit($vehicle->notes, 100) }}</small></p>
                        @endif
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-sm btn-outline-dark">
                            <i class="fas fa-eye"></i> Détails
                        </a>
                         <a href="{{ route('fuel-entries.create') }}?vehicle_id={{ $vehicle->id }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-gas-pump"></i> {{-- <i class="fas fa-plus"></i>  --}} Ajouter Carburant
                    </a>
                    <a href="{{ route('repair-logs.create') }}?vehicle_id={{ $vehicle->id }}"
                       class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-tools"></i> {{-- <i class="fas fa-plus"></i>  --}} Enregistrer Dépannage
                    </a>
                    </div>
                </div>
            </div>
        @endif

    @endforeach
    <hr>
    <h5>Autre</h5>
    @foreach($vehicles as $vehicle)
        @if ($vehicle->categorie!="sedipal-nestle" && $vehicle->categorie!="cdn-nestle" && $vehicle->categorie!="vehicule-livraison-sedipal")
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge bg-{{ $vehicle->etat == 'disponible' ? 'success' : ($vehicle->etat == 'en_entretien' ? 'warning' : 'danger') }}">
                            {{ ucfirst($vehicle->etat) }}
                        </span>
                        <div>
                            <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Supprimer ce véhicule?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $vehicle->marque }} {{ $vehicle->modele }}</h5>
                        <p class="card-text">
                            <strong>Immatriculation:</strong> {{ $vehicle->immatriculation }}<br>
                            <strong>Type:</strong> {{ ucfirst($vehicle->type_vehicule) }}<br>
                            <strong>Kilométrage:</strong> {{ number_format($vehicle->kilometrage_actuel, 0, ',', ' ') }} km<br>
                            <strong>Catégorie:</strong> {{ $vehicle->categorie }}
                        </p>
                        @if($vehicle->notes)
                        <p class="card-text"><small class="text-muted">{{ Str::limit($vehicle->notes, 100) }}</small></p>
                        @endif
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-sm btn-outline-dark">
                            <i class="fas fa-eye"></i> Détails
                        </a>
                         <a href="{{ route('fuel-entries.create') }}?vehicle_id={{ $vehicle->id }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-gas-pump"></i>  {{-- <i class="fas fa-plus"></i>  --}}  Carburant
                    </a>
                    <a href="{{ route('repair-logs.create') }}?vehicle_id={{ $vehicle->id }}"
                       class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-tools"></i> {{-- <i class="fas fa-plus"></i>  --}}  Dépannage
                    </a>
                    </div>
                </div>
            </div>
        @endif

    @endforeach
</div>

@if($vehicles->isEmpty())
<div class="alert alert-info text-center">
    <i class="fas fa-info-circle"></i> Aucun véhicule enregistré pour le moment.
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('searchInput');
    const cards = document.querySelectorAll('.card');

    input.addEventListener('keyup', function() {
        const searchText = input.value.toLowerCase();

        cards.forEach(card => {
            const textContent = card.textContent.toLowerCase();
            if (textContent.includes(searchText)) {
                card.parentElement.style.display = ''; // Afficher
            } else {
                card.parentElement.style.display = 'none'; // Masquer
            }
        });
    });
});
</script>


@endpush
