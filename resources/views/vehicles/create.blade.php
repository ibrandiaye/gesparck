@extends('layouts.app')

@section('title', 'Ajouter un Véhicule')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-plus-circle"></i> Ajouter un Nouveau Véhicule</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('vehicles.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="immatriculation" class="form-label">Immatriculation *</label>
                                <input type="text" class="form-control @error('immatriculation') is-invalid @enderror"
                                       id="immatriculation" name="immatriculation"
                                       value="{{ old('immatriculation') }}" required>
                                @error('immatriculation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type_vehicule" class="form-label">Type de Véhicule *</label>
                                <select class="form-select @error('type_vehicule') is-invalid @enderror"
                                        id="type_vehicule" name="type_vehicule" required>
                                    <option value="">Sélectionnez un type</option>
                                    <option value="voiture" {{ old('type_vehicule') == 'voiture' ? 'selected' : '' }}>Voiture</option>
                                    <option value="camion" {{ old('type_vehicule') == 'camion' ? 'selected' : '' }}>Camion</option>
                                    <option value="utilitaire" {{ old('type_vehicule') == 'utilitaire' ? 'selected' : '' }}>Utilitaire</option>
                                    <option value="moto" {{ old('type_vehicule') == 'moto' ? 'selected' : '' }}>Moto</option>
                                </select>
                                @error('type_vehicule')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="marque" class="form-label">Marque *</label>
                                <input type="text" class="form-control @error('marque') is-invalid @enderror"
                                       id="marque" name="marque" value="{{ old('marque') }}" required>
                                @error('marque')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modele" class="form-label">Modèle *</label>
                                <input type="text" class="form-control @error('modele') is-invalid @enderror"
                                       id="modele" name="modele" value="{{ old('modele') }}" required>
                                @error('modele')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kilometrage_actuel" class="form-label">Kilométrage Actuel *</label>
                                <input type="number" class="form-control @error('kilometrage_actuel') is-invalid @enderror"
                                       id="kilometrage_actuel" name="kilometrage_actuel"
                                       value="{{ old('kilometrage_actuel', 0) }}" min="0" required>
                                @error('kilometrage_actuel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                       {{--  <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_mise_en_service" class="form-label">Date de Mise en Service *</label>
                                <input type="date" class="form-control @error('date_mise_en_service') is-invalid @enderror"
                                       id="date_mise_en_service" name="date_mise_en_service"
                                       value="{{ old('date_mise_en_service') }}" required>
                                @error('date_mise_en_service')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div> --}}
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes & Observations</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="3"
                                  placeholder="Informations supplémentaires...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('vehicles.index') }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer le Véhicule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script pour formater l'immatriculation en majuscules
    document.getElementById('immatriculation').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
</script>
@endpush
