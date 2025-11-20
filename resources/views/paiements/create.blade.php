@extends('layouts.app')

@section('title', 'Nouveau Paiement')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-money-bill-wave"></i> Nouveau Paiement
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

                    <form action="{{ route('paiements.store') }}" method="POST" id="paiementForm">
                        @csrf

                        <!-- Sélection de la facture -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Sélection de la Facture</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="suivi_facture_id">Facture *</label>
                                    <select name="suivi_facture_id" id="suivi_facture_id"
                                            class="form-control @error('suivi_facture_id') is-invalid @enderror" required>
                                        <option value="">Sélectionner une facture</option>
                                        @foreach($factures as $facture)
                                        @php
                                            $montantPaye = $facture->paiements->sum('montant');
                                            $montantRestant = $facture->montant - $montantPaye;
                                        @endphp
                                        <option value="{{ $facture->id }}"
                                            {{ old('suivi_facture_id', $facture->id ?? '') == $facture->id ? 'selected' : '' }}
                                            data-montant="{{ $facture->montant }}"
                                            data-restant="{{ $montantRestant }}"
                                            data-client="{{ $facture->client->nom }}"
                                            data-numero="{{ $facture->numero_facture }}">
                                            {{ $facture->numero_facture }} - {{ $facture->client->nom }}
                                            (Total: {{ number_format($facture->montant, 2, ',', ' ') }}€ -
                                            Restant: {{ number_format($montantRestant, 2, ',', ' ') }}€)
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('suivi_facture_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Informations de la facture sélectionnée -->
                                <div id="facture-info" class="alert alert-info" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Client:</strong> <span id="info-client"></span><br>
                                            <strong>N° Facture:</strong> <span id="info-numero"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Montant total:</strong> <span id="info-montant"></span><br>
                                            <strong>Montant restant:</strong> <span id="info-restant" class="font-weight-bold"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations du paiement -->
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
                                                   value="{{ old('montant') }}"
                                                   step="0.01" min="0.01" required>
                                            <small class="form-text text-muted" id="montant-help">
                                                Montant maximum: 0,00 €
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
                                                   value="{{ old('date_paiement', now()->format('Y-m-d')) }}" required>
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
                                                <option value="{{ $key }}" {{ old('mode_paiement') == $key ? 'selected' : '' }}>
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
                                                   value="{{ old('reference') }}"
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
                                              placeholder="Informations complémentaires...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Enregistrer le Paiement
                            </button>
                            <a href="{{ route('paiements.index') }}" class="btn btn-secondary btn-lg">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const factureSelect = document.getElementById('suivi_facture_id');
    const factureInfo = document.getElementById('facture-info');
    const montantInput = document.getElementById('montant');
    const montantHelp = document.getElementById('montant-help');
    let montantRestant = 0;

    // Mettre à jour les informations de la facture sélectionnée
    factureSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (selectedOption.value) {
            const montantTotal = parseFloat(selectedOption.getAttribute('data-montant'));
            montantRestant = parseFloat(selectedOption.getAttribute('data-restant'));
            const client = selectedOption.getAttribute('data-client');
            const numero = selectedOption.getAttribute('data-numero');

            // Afficher les informations
            document.getElementById('info-client').textContent = client;
            document.getElementById('info-numero').textContent = numero;
            document.getElementById('info-montant').textContent = montantTotal.toFixed(2).replace('.', ',') + ' €';
            document.getElementById('info-restant').textContent = montantRestant.toFixed(2).replace('.', ',') + ' €';
            factureInfo.style.display = 'block';

            // Mettre à jour l'aide du montant
            montantHelp.textContent = `Montant maximum: ${montantRestant.toFixed(2).replace('.', ',')} €`;
            montantInput.max = montantRestant;
        } else {
            factureInfo.style.display = 'none';
            montantRestant = 0;
            montantHelp.textContent = 'Montant maximum: 0,00 €';
        }
    });

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

    // Déclencher l'événement change au chargement si une facture est déjà sélectionnée
    if (factureSelect.value) {
        factureSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
