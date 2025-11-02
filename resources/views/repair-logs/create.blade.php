@extends('layouts.app')

@section('title', 'Nouvelle Intervention')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-plus-circle"></i> Nouvelle Intervention</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('repair-logs.store') }}" method="POST" enctype="multipart/form-data" id="repairForm">
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
                                            {{ old('vehicle_id',$thisVehicle->id ?? null) == $vehicle->id ? 'selected' : '' }}
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
                                <label for="date_intervention" class="form-label">Date d'Intervention *</label>
                                <input type="date" class="form-control @error('date_intervention') is-invalid @enderror"
                                       id="date_intervention" name="date_intervention"
                                       value="{{ old('date_intervention', date('Y-m-d')) }}" required>
                                @error('date_intervention')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type_intervention" class="form-label">Type d'Intervention *</label>
                                <select class="form-select @error('type_intervention') is-invalid @enderror"
                                        id="type_intervention" name="type_intervention" required>
                                    <option value="">Sélectionnez un type</option>
                                    {{-- <option value="entretien_routine" {{ old('type_intervention') == 'entretien_routine' ? 'selected' : '' }}>Entretien Routine</option> --}}
                                    <option value="divers-reparation" {{ old('type_intervention') == 'divers-reparation' ? 'selected' : '' }}>Divers Réparation</option>
                                    <option value="vidange" {{ old('type_intervention') == 'vidange' ? 'selected' : '' }}>Vidange</option>
                                    {{-- <option value="freinage" {{ old('type_intervention') == 'freinage' ? 'selected' : '' }}>Freinage</option> --}}
                                    <option value="pneu" {{ old('type_intervention') == 'pneu' ? 'selected' : '' }}>Pneu</option>
                                    {{-- <option value="electrique" {{ old('type_intervention') == 'electrique' ? 'selected' : '' }}>Électrique</option> --}}
{{--                                     <option value="mecanique" {{ old('type_intervention') == 'mecanique' ? 'selected' : '' }}>Mécanique</option>
                                    <option value="carrosserie" {{ old('type_intervention') == 'carrosserie' ? 'selected' : '' }}>Carrosserie</option> --}}
                                    <option value="batterie" {{ old('type_intervention') == 'batterie' ? 'selected' : '' }}>batterie</option>
                                   <option value="disque-plateau" {{ old('type_intervention') == 'disque-plateau' ? 'selected' : '' }}>Disque plateau</option>
                                    <option value="autre" {{ old('type_intervention') == 'autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('type_intervention')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kilometrage_vehicule" class="form-label">Kilométrage du Véhicule *</label>
                                <input type="number" class="form-control @error('kilometrage_vehicule') is-invalid @enderror"
                                       id="kilometrage_vehicule" name="kilometrage_vehicule"
                                       value="{{ old('kilometrage_vehicule',$thisVehicle->kilometrage_actuel ?? null) }}" min="0" required>
                                @error('kilometrage_vehicule')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted" id="kmHelp">
                                    Kilométrage actuel du véhicule
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <input type="text" class="form-control @error('description') is-invalid @enderror"
                               id="description" name="description"
                               value="{{ old('description') }}"
                               placeholder="Ex: Vidange moteur, changement plaquettes..." required>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                   {{--  <div class="mb-3">
                        <label for="details_travaux" class="form-label">Détails des Travaux</label>
                        <textarea class="form-control @error('details_travaux') is-invalid @enderror"
                                  id="details_travaux" name="details_travaux" rows="3"
                                  placeholder="Détail des travaux effectués...">{{ old('details_travaux') }}</textarea>
                        @error('details_travaux')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cout_pieces" class="form-label">Coût Pièces (FCFA) *</label>
                                <input type="number" step="0.01" class="form-control @error('cout_pieces') is-invalid @enderror"
                                       id="cout_pieces" name="cout_pieces"
                                       value="{{ old('cout_pieces', 0) }}" min="0" required>
                                @error('cout_pieces')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cout_main_oeuvre" class="form-label">Coût Main d'Œuvre (FCFA) *</label>
                                <input type="number" step="0.01" class="form-control @error('cout_main_oeuvre') is-invalid @enderror"
                                       id="cout_main_oeuvre" name="cout_main_oeuvre"
                                       value="{{ old('cout_main_oeuvre', 0) }}" min="0" required>
                                @error('cout_main_oeuvre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>



                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="statut" class="form-label">Statut *</label>
                                <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                     <option value="termine" {{ old('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                                    <option value="planifie" {{ old('statut') == 'planifie' ? 'selected' : '' }}>Planifié</option>
                                    <option value="en_cours" {{ old('statut') == 'en_cours' ? 'selected' : '' }}>En Cours</option>
                                    <option value="annule" {{ old('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="garage" class="form-label">Garage/Atelier</label>
                                <input type="text" class="form-control @error('garage') is-invalid @enderror"
                                       id="garage" name="garage"
                                       value="{{ old('garage') }}" placeholder="Nom du garage...">
                                @error('garage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="technicien" class="form-label">Technicien</label>
                                <input type="text" class="form-control @error('technicien') is-invalid @enderror"
                                       id="technicien" name="technicien"
                                       value="{{ old('technicien') }}" placeholder="Nom du technicien...">
                                @error('technicien')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_prochaine_revision" class="form-label">Date Prochaine Révision</label>
                                <input type="date" class="form-control @error('date_prochaine_revision') is-invalid @enderror"
                                       id="date_prochaine_revision" name="date_prochaine_revision"
                                       value="{{ old('date_prochaine_revision') }}">
                                @error('date_prochaine_revision')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prochain_kilometrage" class="form-label">Prochain Kilométrage</label>
                                <input type="number" class="form-control @error('prochain_kilometrage') is-invalid @enderror"
                                       id="prochain_kilometrage" name="prochain_kilometrage"
                                       value="{{ old('prochain_kilometrage') }}" min="0">
                                @error('prochain_kilometrage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="facture" class="form-label">Facture (PDF, JPG, PNG - max 5MB)</label>
                        <input type="file" class="form-control @error('facture') is-invalid @enderror"
                               id="facture" name="facture"
                               accept=".pdf,.jpg,.jpeg,.png">
                        @error('facture')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Formats acceptés: PDF, JPG, JPEG, PNG. Taille max: 5MB
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes & Observations</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="3"
                                  placeholder="Remarques supplémentaires...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}

                    <!-- Calcul automatique -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Coût Total Calculé:</strong>
                                <span id="coutTotal" class="fw-bold">0 FCFA</span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Main d'œuvre + Pièces</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('repair-logs.index') }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer l'Intervention
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
    const mainOeuvreInput = document.getElementById('cout_main_oeuvre');
    const piecesInput = document.getElementById('cout_pieces');
    const kilometrageInput = document.getElementById('kilometrage_vehicule');
    const coutTotalSpan = document.getElementById('coutTotal');
    const kmHelp = document.getElementById('kmHelp');

    // Calcul automatique du coût total
    function calculateTotalCost() {
        const mainOeuvre = parseFloat(mainOeuvreInput.value) || 0;
        const pieces = parseFloat(piecesInput.value) || 0;
        const totalCost = mainOeuvre + pieces;

        coutTotalSpan.textContent = totalCost.toLocaleString('fr-FR', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }) + ' FCFA';
    }
    $('#vehicle_id').on('change', function(){
        console.log('iba');
         const selected = $('#vehicle_id option:selected');
          const km = selected.data('kilometrage');
           $('#kilometrage_vehicule').val(km);
         $('#kmHelp').text(`Kilométrage actuel du véhicule: ${parseInt(km).toLocaleString('fr-FR')} km`);

    });
    // Mettre à jour le kilométrage suggéré
   /* vehicleSelect.addEventListener('change', function() {
        const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
        const kilometrageActuel = selectedOption.getAttribute('data-kilometrage');

        if (kilometrageActuel) {
            kilometrageInput.value = kilometrageActuel;
            kmHelp.textContent = `Kilométrage actuel du véhicule: ${parseInt(kilometrageActuel).toLocaleString('fr-FR')} km`;
        }
    });*/

    // Écouter les changements de coûts
    mainOeuvreInput.addEventListener('input', calculateTotalCost);
    piecesInput.addEventListener('input', calculateTotalCost);

    // Calcul initial
    calculateTotalCost();

    // Validation du kilométrage
    document.getElementById('repairForm').addEventListener('submit', function(e) {
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
