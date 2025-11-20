@extends('layouts.app')

@section('title', 'Modifier le Paiement')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Modifier le Paiement
                        <small class="text-muted">- #{{ $paiement->id }}</small>
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

                    <!-- Informations de la facture -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Informations de la Facture</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>N° Facture:</strong> {{ $paiement->facture->numero_facture }}<br>
                                    <strong>Client:</strong> {{ $paiement->facture->client->nom }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Montant total:</strong> {{ number_format($paiement->facture->montant, 2, ',', ' ') }} €<br>
                                    <strong>Montant restant (hors ce paiement):</strong>
                                    <span class="font-weight-bold" id="montant-restant-info">
                                        {{ number_format($paiement->facture->montant - ($paiement->facture->paiements->sum('montant') - $paiement->montant), 2, ',', ' ') }} €
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('paiements.update', $paiement->id) }}" method="POST" id="paiementForm">
                        @csrf
                        @method('PUT')

                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">Informations du Paiement</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="montant">Montant (€) *</label>
                                            <input type="number" name="montant" id="montant"
                                                   class="form-control @error('montant') is-invalid @enderror"
                                                   value="{{ old('montant', $paiement->montant) }}"
                                                   step="0.01" min="0.01" required>
                                            <small class="form-text text-muted" id="montant-help">
                                                Montant maximum:
                                                {{ number_format($paiement->facture->montant - ($paiement->facture->paiements->sum('montant') - $paiement->montant), 2, ',', ' ') }} €
                                            </small>
                                            @error('montant')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_paiement">Date du paiement *</label>
                                            <input type="date" name="date_paiement" id="date_paiement"
                                                   class="form-control @error('date_paiement') is-invalid @enderror"
                                                   value="{{ old('date_paiement', $paiement->date_paiement->format('Y-m-d')) }}" required>
                                            @error('date_paiement')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mode_paiement">Mode de paiement *</label>
                                            <select name="mode_paiement" id="mode_paiement"
                                                    class="form-control @error('mode_paiement') is-invalid @enderror" required>
                                                @foreach($modesPaiement as $key => $value)
                                                <option value="{{ $key }}"
                                                    {{ old('mode_paiement', $paiement->mode_paiement) == $key ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('mode_paiement')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="reference">Référence (optionnel)</label>
                                            <input type="text" name="reference" id="reference"
                                                   class="form-control @error('reference') is-invalid @enderror"
                                                   value="{{ old('reference', $paiement->reference) }}"
                                                   placeholder="N° chèque, référence virement...">
                                            @error('reference')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="notes">Notes (optionnel)</label>
                                    <textarea name="notes" id="notes" rows="3"
                                              class="form-control @error('notes') is-invalid @enderror"
                                              placeholder="Informations complémentaires...">{{ old('notes', $paiement->notes) }}</textarea>
                                    @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                            <a href="{{ route('paiements.show', $paiement->id) }}" class="btn btn-secondary btn-lg">
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
    const montantInput = document.getElementById('montant');
    const montantHelp = document.getElementById('montant-help');

    // Calculer le montant restant maximum
    const montantRestant = {{ $paiement->facture->montant - ($paiement->facture->paiements->sum('montant') - $paiement->montant) }};

    // Validation du montant en temps réel
    montantInput.addEventListener('input', function() {
        const valeur = parseFloat(this.value) || 0;

        if (valeur > montantRestant) {
            this.classList.add('is-invalid');
            montantHelp.classList.add('text-danger');
            montantHelp.textContent = `Erreur: Le montant dépasse le montant restant (${montantRestant.toFixed(2).replace('.', ',')} €)`;
        } else {
            this.classList.remove('is-invalid');
            montantHelp.classList.remove('text-danger');
            montantHelp.textContent = `Montant maximum: ${montantRestant.toFixed(2).replace('.', ',')} €`;
        }
    });

    // Validation du formulaire
    const paiementForm = document.getElementById('paiementForm');
    paiementForm.addEventListener('submit', function(e) {
        const montant = parseFloat(montantInput.value) || 0;

        if (montant > montantRestant) {
            e.preventDefault();
            alert('Le montant du paiement dépasse le montant restant!');
            return false;
        }
    });
});
</script>
@endpush
