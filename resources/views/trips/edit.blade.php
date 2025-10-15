@extends('layouts.app')

@section('title', 'Modifier le Trajet')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Modifier le Trajet
                        <small class="text-muted">- {{ $trip->destination }} ({{ $trip->date_trajet->format('d/m/Y') }})</small>
                    </h4>
                </div>

                <div class="card-body">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('trips.update', $trip->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehicle_id">Véhicule *</label>
                                    <select name="vehicle_id" id="vehicle_id" class="form-control @error('vehicle_id') is-invalid @enderror" required>
                                        <option value="">Sélectionner un véhicule</option>
                                        @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}"
                                            {{ old('vehicle_id', $trip->vehicle_id) == $vehicle->id ? 'selected' : '' }}
                                            data-conducteur="{{ $vehicle->conducteur ?? 'Non assigné' }}"
                                            data-km="{{ $vehicle->kilometrage_actuel }}">
                                            {{ $vehicle->immatriculation }} - {{ $vehicle->modele }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fuel_entry_id">Plein associé *</label>
                                    <select name="fuel_entry_id" id="fuel_entry_id" class="form-control @error('fuel_entry_id') is-invalid @enderror" required>
                                        <option value="">Sélectionner un plein</option>
                                        @foreach($fuelEntries as $entry)
                                        <option value="{{ $entry->id }}"
                                            {{ old('fuel_entry_id', $trip->fuel_entry_id) == $entry->id ? 'selected' : '' }}>
                                            {{ $entry->vehicle->immatriculation }} -
                                            {{ $entry->date->format('d/m/Y') }} -
                                            {{ $entry->litres }}L
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('fuel_entry_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="destination">Destination *</label>
                                    <input type="text" name="destination" id="destination"
                                           class="form-control @error('destination') is-invalid @enderror"
                                           value="{{ old('destination', $trip->destination) }}" required>
                                    @error('destination')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="motif">Motif *</label>
                                    <select name="motif" id="motif" class="form-control @error('motif') is-invalid @enderror" required>
                                        @foreach($motifs as $key => $value)
                                        <option value="{{ $key }}" {{ old('motif', $trip->motif) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('motif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_trajet">Date du trajet *</label>
                                    <input type="date" name="date_trajet" id="date_trajet"
                                           class="form-control @error('date_trajet') is-invalid @enderror"
                                           value="{{ old('date_trajet', $trip->date_trajet->format('Y-m-d')) }}" required>
                                    @error('date_trajet')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                           {{--   <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nombre_trajets">Nombre de trajets *</label>
                                    <input type="number" name="nombre_trajets" id="nombre_trajets"
                                           class="form-control @error('nombre_trajets') is-invalid @enderror"
                                           value="{{ old('nombre_trajets', $trip->nombre_trajets) }}"
                                           min="1" max="50" required>
                                    @error('nombre_trajets')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="km_depart">KM Départ *</label>
                                    <input type="number" name="km_depart" id="km_depart"
                                           class="form-control @error('km_depart') is-invalid @enderror"
                                           value="{{ old('km_depart', $trip->km_depart) }}" required>
                                    @error('km_depart')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                           <div class="col-md-3">
                                <div class="form-group">
                                    <label for="km_arrivee">KM Arrivée *</label>
                                    <input type="number" name="km_arrivee" id="km_arrivee"
                                           class="form-control @error('km_arrivee') is-invalid @enderror"
                                           value="{{ old('km_arrivee', $trip->km_arrivee) }}" required>
                                    @error('km_arrivee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Distance calculée</label>
                                    <input type="text" id="distance_calculee" class="form-control" readonly
                                           style="background-color: #f8f9fa;"
                                           value="{{ $trip->distance_moyenne }} km">
                                </div>
                            </div>
                        </div> --}}

                        <div class="form-group">
                            <label for="notes">Notes (optionnel)</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $trip->notes) }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                            <a href="{{ route('trips.show', $trip->id) }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kmDepartInput = document.getElementById('km_depart');
    const kmArriveeInput = document.getElementById('km_arrivee');
    const distanceCalculee = document.getElementById('distance_calculee');

    function calculerDistance() {
        const kmDepart = parseInt(kmDepartInput.value) || 0;
        const kmArrivee = parseInt(kmArriveeInput.value) || 0;

        if (kmArrivee > kmDepart) {
            const distance = kmArrivee - kmDepart;
            distanceCalculee.value = distance + ' km';
            distanceCalculee.style.color = '#28a745';
        } else {
            distanceCalculee.value = 'Invalide';
            distanceCalculee.style.color = '#dc3545';
        }
    }

    kmDepartInput.addEventListener('input', calculerDistance);
    kmArriveeInput.addEventListener('input', calculerDistance);
});
</script>
@endpush
