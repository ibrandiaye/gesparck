@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['totalVehicles'] }}</h4>
                        <p>Total Véhicules</p>
                    </div>
                    <i class="fas fa-truck fa-3x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['vehiclesDisponibles'] }}</h4>
                        <p>Véhicules Disponibles</p>
                    </div>
                    <i class="fas fa-check-circle fa-3x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $stats['vehiclesEnEntretien'] }}</h4>
                        <p>En Entretien</p>
                    </div>
                    <i class="fas fa-tools fa-3x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-clock"></i> Véhicules Récents</h5>
            </div>
            <div class="card-body">
                @if($recentVehicles->count() > 0)
                <div class="list-group">
                    @foreach($recentVehicles as $vehicle)
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $vehicle->marque }} {{ $vehicle->modele }}</h6>
                            <small class="text-muted">{{ $vehicle->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1">Immat: {{ $vehicle->immatriculation }} | {{ number_format($vehicle->kilometrage_actuel, 0, ',', ' ') }} km</p>
                        <span class="badge bg-{{ $vehicle->etat == 'disponible' ? 'success' : 'warning' }}">
                            {{ ucfirst($vehicle->etat) }}
                        </span>
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-muted">Aucun véhicule enregistré.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
