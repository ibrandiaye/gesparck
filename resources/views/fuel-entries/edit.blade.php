@extends('layouts.app')

@section('title', 'Modifier le Remplissage')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-edit"></i> Modifier le Remplissage</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('fuel-entries.update', $fuelEntry) }}" method="POST" id="fuelForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vehicle_id" class="form-label">Véhicule *</label>
                                <select class="form-select @error('vehicle_id') is-invalid @enderror"
                                        id="vehicle_id" name="vehicle_id" required>
                                    <option value="">Sélectionnez un véhicule</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}"
                                            {{ old('vehicle_id', $fuelEntry->vehicle_id) == $vehicle->id ? 'selected' : '' }}
                                            data-kilometrage="{{ $vehicle->kilometrage_actuel }}">
                                            {{ $vehicle->immatriculation }} - {{ $vehicle->marque }} {{ $vehicle->modele }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_remplissage" class="form-label">Date de Remplissage *</label>
                                <input type="date" class="form-control @error('date_remplissage') is-invalid @enderror"
                                       id="date_remplissage" name="date_remplissage"
                                       value="{{ old('date_remplissage', $fuelEntry->date_remplissage->format('Y-m-d')) }}" required>
                                @error('date_remplissage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="prix_litre" class="form-label">Prix du Litre (FCFA) *</label>
                                <input type="number" step="0.001" class="form-control @error('prix_litre') is-invalid @enderror"
                                       id="prix_litre" name="prix_litre"
                                       value="{{ old('prix_litre', $fuelEntry->prix_litre) }}" min="0" max="1000" required>
                                @error('prix_litre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="litres" class="form-label">Quantité (Litres) *</label>
                                <input type="number" step="0.01" class="form-control @error('litres') is-invalid @enderror"
                                       id="litres" name="litres"
                                       value="{{ old('litres', $fuelEntry->litres) }}" min="1" max="1000" required>
                                @error('litres')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="kilometrage" class="form-label">Kilométrage *</label>
                                <input type="number" class="form-control @error('kilometrage') is-invalid @enderror"
                                       id="kilometrage" name="kilometrage"
                                       value="{{ old('kilometrage', $fuelEntry->kilometrage) }}" min="0" required>
                                @error('kilometrage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted" id="kmHelp">
                                    Kilométrage au moment du remplissage
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="station" class="form-label">Station Service</label>
                                <select class="form-control select2"  id="station" name="station" required>
                                    <option value="">Selectionner</option>
                                    <option value="Mobile castors" {{ $fuelEntry->station == "Mobile castors" ? "selected" : " " }}>Mobile castors</option>
                                    <option value="Total Yarakh"  {{ $fuelEntry->station == "Total Yarakh" ? "selected" : " " }}>Total Yarakh</option>
                                     <option value="Autre Station"  {{ $fuelEntry->station == "Autre Station" ? "selected" : " " }}>Autre Station</option>
                                </select>
                               {{--  <input type="text" class="form-control @error('station') is-invalid @enderror"
                                       id="station" name="station" --}}
                                       value="{{ old('station', $fuelEntry->station) }}" placeholder="Ex: Total, Shell, etc.">
                                @error('station')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type_carburant" class="form-label">Type de Carburant *</label>
                                <select class="form-select @error('type_carburant') is-invalid @enderror"
                                        id="type_carburant" name="type_carburant" required>
                                    <option value="">Sélectionnez un type</option>
                                    <option value="diesel" {{ old('type_carburant', $fuelEntry->type_carburant) == 'diesel' ? 'selected' : '' }}>Diesel</option>
                                    <option value="essence" {{ old('type_carburant', $fuelEntry->type_carburant) == 'essence' ? 'selected' : '' }}>Essence</option>
                                    <option value="sp95" {{ old('type_carburant', $fuelEntry->type_carburant) == 'sp95' ? 'selected' : '' }}>SP95</option>
                                    <option value="sp98" {{ old('type_carburant', $fuelEntry->type_carburant) == 'sp98' ? 'selected' : '' }}>SP98</option>
                                    <option value="gpl" {{ old('type_carburant', $fuelEntry->type_carburant) == 'gpl' ? 'selected' : '' }}>GPL</option>
                                </select>
                                @error('type_carburant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes & Observations</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="3"
                                  placeholder="Remarques sur le remplissage...">{{ old('notes', $fuelEntry->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Calcul automatique -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Coût Total Calculé:</strong>
                                <span id="coutTotal" class="fw-bold">
                                    {{ number_format($fuelEntry->cout_total, 0, ',', ' ') }} FCFA
                                </span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Le calcul se met à jour automatiquement</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('fuel-entries.show', $fuelEntry) }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à Jour
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Informations de suivi -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Informations de Suivi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">Créé le:</small><br>
                        <strong>{{ $fuelEntry->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Dernière modification:</small><br>
                        <strong>{{ $fuelEntry->updated_at->format('d/m/Y H:i') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const vehicleSelect = document.getElementById('vehicle_id');
    const prixLitreInput = document.getElementById('prix_litre');
    const litresInput = document.getElementById('litres');
    const kilometrageInput = document.getElementById('kilometrage');
    const coutTotalSpan = document.getElementById('coutTotal');
    const kmHelp = document.getElementById('kmHelp');

    // Calcul automatique du coût total
    function calculateTotalCost() {
        const prixLitre = parseFloat(prixLitreInput.value) || 0;
        const litres = parseFloat(litresInput.value) || 0;
        const totalCost = prixLitre * litres;

        coutTotalSpan.textContent = totalCost.toLocaleString('fr-FR', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }) + ' FCFA';
    }

    // Mettre à jour le kilométrage suggéré
    vehicleSelect.addEventListener('change', function() {
        const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
        const kilometrageActuel = selectedOption.getAttribute('data-kilometrage');

        if (kilometrageActuel) {
            kilometrageInput.value = kilometrageActuel;
            kmHelp.textContent = `Kilométrage actuel du véhicule: ${parseInt(kilometrageActuel).toLocaleString('fr-FR')} km`;
        }
    });

    // Écouter les changements
    prixLitreInput.addEventListener('input', calculateTotalCost);
    litresInput.addEventListener('input', calculateTotalCost);

    // Calcul initial
    calculateTotalCost();
});
</script>
@endpush
