@extends('layouts.app')

@section('title', 'Modifier l\'Intervention')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-edit"></i> Modifier l'Intervention</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('repair-logs.update', $repairLog) }}" method="POST" enctype="multipart/form-data" id="repairForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vehicle_id" class="form-label">Véhicule *</label>
                                <select class="form-select select2 @error('vehicle_id') is-invalid @enderror"
                                        id="vehicle_id" name="vehicle_id" required>
                                    <option value="">Sélectionnez un véhicule</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}"
                                            {{ old('vehicle_id', $repairLog->vehicle_id) == $vehicle->id ? 'selected' : '' }}
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
                                       value="{{ old('date_intervention', $repairLog->date_intervention->format('Y-m-d')) }}" required>
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
                                    {{-- <option value="entretien_routine" {{ old('type_intervention', $repairLog->type_intervention) == 'entretien_routine' ? 'selected' : '' }}>Entretien Routine</option> --}}
                                    <option value="divers-reparation" {{ old('type_intervention', $repairLog->type_intervention) == 'divers-reparation' ? 'selected' : '' }}>Divers Reparation</option>
                                    <option value="vidange" {{ old('type_intervention', $repairLog->type_intervention) == 'vidange' ? 'selected' : '' }}>Vidange</option>
                                    {{-- <option value="freinage" {{ old('type_intervention', $repairLog->type_intervention) == 'freinage' ? 'selected' : '' }}>Freinage</option> --}}
                                    <option value="pneu" {{ old('type_intervention', $repairLog->type_intervention) == 'pneu' ? 'selected' : '' }}>Pneu</option>
                                     <option value="batterie" {{ old('type_intervention', $repairLog->type_intervention) == 'batterie' ? 'selected' : '' }}>batterie</option>
                                   <option value="disque-plateau" {{ old('type_intervention', $repairLog->type_intervention) == 'disque-plateau' ? 'selected' : '' }}>Disque plateau</option>
                                  {{--   <option value="electrique" {{ old('type_intervention', $repairLog->type_intervention) == 'electrique' ? 'selected' : '' }}>Électrique</option>
                                    <option value="mecanique" {{ old('type_intervention', $repairLog->type_intervention) == 'mecanique' ? 'selected' : '' }}>Mécanique</option>
                                    <option value="carrosserie" {{ old('type_intervention', $repairLog->type_intervention) == 'carrosserie' ? 'selected' : '' }}>Carrosserie</option> --}}
                                    <option value="autre" {{ old('type_intervention', $repairLog->type_intervention) == 'autre' ? 'selected' : '' }}>Autre</option>
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
                                       value="{{ old('kilometrage_vehicule', $repairLog->kilometrage_vehicule) }}" min="0" required>
                                @error('kilometrage_vehicule')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted" id="kmHelp">
                                    Kilométrage au moment de l'intervention
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <input type="text" class="form-control @error('description') is-invalid @enderror"
                               id="description" name="description"
                               value="{{ old('description', $repairLog->description) }}" required>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- <div class="mb-3">
                        <label for="details_travaux" class="form-label">Détails des Travaux</label>
                        <textarea class="form-control @error('details_travaux') is-invalid @enderror"
                                  id="details_travaux" name="details_travaux" rows="3">{{ old('details_travaux', $repairLog->details_travaux) }}</textarea>
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
                                       value="{{ old('cout_pieces', $repairLog->cout_pieces) }}" min="0" required>
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
                                       value="{{ old('cout_main_oeuvre', $repairLog->cout_main_oeuvre) }}" min="0" required>
                                @error('cout_main_oeuvre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>



                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="statut" class="form-label">Statut *</label>
                                <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                    <option value="planifie" {{ old('statut', $repairLog->statut) == 'planifie' ? 'selected' : '' }}>Planifié</option>
                                    <option value="en_cours" {{ old('statut', $repairLog->statut) == 'en_cours' ? 'selected' : '' }}>En Cours</option>
                                    <option value="termine" {{ old('statut', $repairLog->statut) == 'termine' ? 'selected' : '' }}>Terminé</option>
                                    <option value="annule" {{ old('statut', $repairLog->statut) == 'annule' ? 'selected' : '' }}>Annulé</option>
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
                                       value="{{ old('garage', $repairLog->garage) }}">
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
                                       value="{{ old('technicien', $repairLog->technicien) }}">
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
                                       value="{{ old('date_prochaine_revision', $repairLog->date_prochaine_revision ? $repairLog->date_prochaine_revision->format('Y-m-d') : '') }}">
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
                                       value="{{ old('prochain_kilometrage', $repairLog->prochain_kilometrage) }}" min="0">
                                @error('prochain_kilometrage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="facture" class="form-label">Facture</label>
                        <input type="file" class="form-control @error('facture') is-invalid @enderror"
                               id="facture" name="facture"
                               accept=".pdf,.jpg,.jpeg,.png">
                        @error('facture')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($repairLog->facture)
                        <div class="mt-2">
                            <small class="text-muted">Facture actuelle: </small>
                            <a href="{{ route('repair-logs.download-facture', $repairLog) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-download"></i> Télécharger
                            </a>
                        </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes & Observations</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes" name="notes" rows="3">{{ old('notes', $repairLog->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}

                    <!-- Calcul automatique -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Coût Total Calculé:</strong>
                                <span id="coutTotal" class="fw-bold">
                                    {{ number_format($repairLog->cout_total, 0, ',', ' ') }} FCFA
                                </span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Main d'œuvre + Pièces</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('repair-logs.show', $repairLog) }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à Jour
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Section Danger -->
        <div class="card mt-4 border-danger">
            <div class="card-header bg-danger text-white">
                <h5><i class="fas fa-exclamation-triangle"></i> Zone Danger</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Cette action est irréversible. Toutes les données associées à cette intervention seront supprimées.</p>
                <form action="{{ route('repair-logs.destroy', $repairLog) }}" method="POST"
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cette intervention?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-trash"></i> Supprimer Définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>

document.addEventListener('DOMContentLoaded', function() {
    const mainOeuvreInput = document.getElementById('cout_main_oeuvre');
    const piecesInput = document.getElementById('cout_pieces');
    const coutTotalSpan = document.getElementById('coutTotal');

     $('#vehicle_id').on('change', function(){
        console.log('iba');
         const selected = $('#vehicle_id option:selected');
          const km = selected.data('kilometrage');
           $('#kilometrage_vehicule').val(km);
         $('#kmHelp').text(`Kilométrage actuel du véhicule: ${parseInt(km).toLocaleString('fr-FR')} km`);

    });


    function calculateTotalCost() {
        const mainOeuvre = parseFloat(mainOeuvreInput.value) || 0;
        const pieces = parseFloat(piecesInput.value) || 0;
        const totalCost = mainOeuvre + pieces;

        coutTotalSpan.textContent = totalCost.toLocaleString('fr-FR', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }) + ' FCFA';
    }

    mainOeuvreInput.addEventListener('input', calculateTotalCost);
    piecesInput.addEventListener('input', calculateTotalCost);
});
</script>
@endpush
