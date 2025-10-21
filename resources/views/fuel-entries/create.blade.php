@extends('layouts.app')

@section('title', 'Nouveau Remplissage')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-gas-pump"></i> Nouveau Remplissage de Carburant</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('fuel-entries.store') }}" method="POST" id="fuelForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vehicle_id" class="form-label">Véhicule *</label>
                                <select class="form-select select2 @error('vehicle_id') is-invalid @enderror"
                                        id="vehicle_id" name="vehicle_id" required>
                                    <option value="">Sélectionnez un véhicule</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}"
                                            {{ old('vehicle_id', $thisVehicle->id ?? null) == $vehicle->id ? 'selected' : '' }}
                                            data-kilometrage="{{ $vehicle->kilometrage_actuel }}"
                                            data-carburant="{{ $vehicle->carburant->libelle ?? null }}" data-montant-carburant="{{ $vehicle->carburant->montant ?? null }}">
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
                                       value="{{ old('date_remplissage', date('Y-m-d')) }}" required>
                                @error('date_remplissage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="prix_litre" class="form-label">Prix du Litre (FCFA)  *</label>
                                <input type="number" step="0.001" class="form-control @error('prix_litre') is-invalid @enderror"
                                       id="prix_litre" name="prix_litre"
                                       value="{{ old('prix_litre',$thisVehicle->carburant->montant ?? null ) }}" min="0" max="1000" required>
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
                                       value="{{ old('litres') }}" min="1" max="1000" required>
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
                                       value="{{ old('kilometrage',$thisVehicle->kilometrage_actuel ?? null) }}" min="0" required>
                                @error('kilometrage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted" id="kmHelp">
                                    Kilométrage actuel du véhicule
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="station" class="form-label">Station Service</label>
                                <select class="form-control"  id="station" name="station" required>
                                    <option value="">Selectionner</option>
                                    <option value="Mobile castors" {{ old('station') == "Mobile castors" ? "selected" : " " }}>Mobile castors</option>
                                    <option value="Total Yarakh"  {{ old('station') == "Total Yarakh" ? "selected" : " " }}>Total Yarakh</option>
                                     <option value="Autre Station"  {{ old('station') == "Autre Station" ? "selected" : " " }}>Autre Station</option>
                                </select>
                               {{--  <input type="text" class="form-control @error('station') is-invalid @enderror"
                                       id="station" name="station"
                                       value="{{ old('station') }}" placeholder="Ex: Total, Shell, etc."> --}}
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
                                    <option value="Diesel" {{ old('type_carburant',$thisVehicle->carburant->libelle) == 'diesel' ? 'selected' : '' }}>Diesel</option>
                                    <option value="Essence" {{ old('type_carburant',$thisVehicle->carburant->libelle) == 'essence' ? 'selected' : '' }}>Essence</option>
                                    <option value="sp95" {{ old('type_carburant',$thisVehicle->carburant->libelle) == 'sp95' ? 'selected' : '' }}>SP95</option>
                                    <option value="sp98" {{ old('type_carburant',$thisVehicle->carburant->libelle) == 'sp98' ? 'selected' : '' }}>SP98</option>
                                    <option value="gpl" {{ old('type_carburant',$thisVehicle->carburant->libelle) == 'gpl' ? 'selected' : '' }}>GPL</option>
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
                                  placeholder="Remarques sur le remplissage...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Calcul automatique -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Coût Total Calculé:</strong>
                                <span id="coutTotal" class="fw-bold">0 FCFA</span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Le calcul se fait automatiquement</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('fuel-entries.index') }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer le Remplissage
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
document.addEventListener('DOMContentLoaded', function() {
    const vehicleSelect = document.getElementById('vehicle_id');
    const prixLitreInput = document.getElementById('prix_litre');
    const litresInput = document.getElementById('litres');
    const kilometrageInput = document.getElementById('kilometrage');
    const coutTotalSpan = document.getElementById('coutTotal');
    const kmHelp = document.getElementById('kmHelp');
    const type_carburant = document.getElementById('type_carburant');


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

    // Mettre à jour le kilométrage suggéré quand on change de véhicule
    vehicleSelect.addEventListener('change', function() {
        const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
        const kilometrageActuel = selectedOption.getAttribute('data-kilometrage');
        const prix_carburant = selectedOption.getAttribute('data-montant-carburant');
        const carburant = selectedOption.getAttribute('data-carburant');

        if (kilometrageActuel) {
            kilometrageInput.value = kilometrageActuel;
            kmHelp.textContent = `Kilométrage actuel du véhicule: ${parseInt(kilometrageActuel).toLocaleString('fr-FR')} km`;
        }
         if (prix_carburant) {
            prixLitreInput.value = prix_carburant;
        }
         if (carburant) {
            for (let i = 0; i < type_carburant.options.length; i++) {
            if (type_carburant.options[i].value === carburant) {
                type_carburant.selectedIndex = i;
                break;
            }
            }
        }
    });

    // Écouter les changements de prix et quantité
    prixLitreInput.addEventListener('input', calculateTotalCost);
    litresInput.addEventListener('input', calculateTotalCost);

    // Calcul initial
    calculateTotalCost();

    // Validation personnalisée
    document.getElementById('fuelForm').addEventListener('submit', function(e) {
        const kilometrage = parseInt(kilometrageInput.value);
        const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
        const kilometrageActuel = parseInt(selectedOption.getAttribute('data-kilometrage'));

        if (kilometrage < kilometrageActuel) {
            e.preventDefault();
            alert('Le kilométrage ne peut pas être inférieur au kilométrage actuel du véhicule!');
            kilometrageInput.focus();
        }
    });
});
</script>
@endpush
