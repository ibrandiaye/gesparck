@extends('layouts.app')

@section('title', 'Nouveau Trajet')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-route"></i> Nouveau Trajet
                        @if($entry)
                        <small class="text-muted">- Pour le plein du {{ $entry->date_remplissage->format('d/m/Y') }}</small>
                        @endif
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

                    <form action="{{ route('trips.store') }}" method="POST">
                        @csrf



                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehicle_id">Véhicule *</label>
                                    <select name="vehicle_id" id="vehicle_id" class="form-control @error('vehicle_id') is-invalid @enderror" required>

                                        <option value="{{ $vehicle->id }}"
                                            {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}
                                            data-conducteur="{{ $vehicle->conducteur ?? 'Non assigné' }}"
                                            data-km="{{ $vehicle->kilometrage_actuel }}">
                                            {{ $vehicle->immatriculation }} - {{ $vehicle->modele }}
                                        </option>
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

                                        <option value="{{ $entry->id }}"
                                            {{ old('fuel_entry_id') == $entry->id ? 'selected' : '' }}>
                                            {{ $entry->vehicle->immatriculation }} -
                                            {{ $entry->date_remplissage->format('d/m/Y') }} -
                                            {{ $entry->litres }}L
                                        </option>

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
                                           value="{{ old('destination') }}"
                                           placeholder="Ex: Paris, Lyon, Marseille..." required>
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
                                        <option value="{{ $key }}" {{ old('motif') == $key ? 'selected' : '' }}>
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
                                           value="{{ old('date_trajet', now()->format('Y-m-d')) }}" required>
                                    @error('date_trajet')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nombre_trajets">Nombre de trajets *</label>
                                    <input type="number" name="nombre_trajets" id="nombre_trajets"
                                           class="form-control @error('nombre_trajets') is-invalid @enderror"
                                           value="{{ old('nombre_trajets', 1) }}"
                                           min="1" max="50" required>
                                    <small class="form-text text-muted">
                                        Nombre d'allers-retours ou trajets identiques
                                    </small>
                                    @error('nombre_trajets')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>




                        </div>

                        <div class="form-group">
                            <label for="notes">Notes (optionnel)</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      placeholder="Observations particulières...">{{ old('notes') }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Informations véhicule -->
                        <div class="alert alert-info" id="vehicle-info" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Conducteur:</strong> <span id="info-conducteur"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>KM Actuel:</strong> <span id="info-km"></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Enregistrer le Trajet
                            </button>
                            <a href="{{ route('trips.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


