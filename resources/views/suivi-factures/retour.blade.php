@extends('layouts.app')

@section('title', 'Ajouter un Retour - ' . $suiviFacture->numero_facture)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-undo"></i> Ajouter un Retour
                        <small class="text-muted">- {{ $suiviFacture->numero_facture }}</small>
                    </h4>
                </div>

                <div class="card-body">
                    <!-- Informations de la facture -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Informations de la Facture</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>N° Facture:</strong> {{ $suiviFacture->numero_facture }}<br>
                                    <strong>Client:</strong> {{ $suiviFacture->client->nom }}<br>
                                    <strong>Date livraison:</strong>
                                    @if($suiviFacture->date_livraison)
                                        {{ $suiviFacture->date_livraison->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <strong>Montant total:</strong> {{ number_format($suiviFacture->montant, 2, ',', ' ') }} €<br>
                                    <strong>Montant payé:</strong> {{ number_format($suiviFacture->montant_paye, 2, ',', ' ') }} €<br>
                                    <strong>Montant restant:</strong> {{ number_format($suiviFacture->montant_restant, 2, ',', ' ') }} €
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('suivi-factures.enregistrer-retour', $suiviFacture->id) }}" method="POST">
                        @csrf

                        <div class="card mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">Informations du Retour</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="montant_retour">Montant du retour (€) *</label>
                                            <input type="number" name="montant_retour" id="montant_retour"
                                                   class="form-control @error('montant_retour') is-invalid @enderror"
                                                   value="{{ old('montant_retour') }}"
                                                   step="0.01" min="0.01" max="{{ $suiviFacture->montant }}" required>
                                            <small class="form-text text-muted">
                                                Montant maximum: {{ number_format($suiviFacture->montant, 2, ',', ' ') }} €
                                            </small>
                                            @error('montant_retour')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_retour">Date du retour *</label>
                                            <input type="date" name="date_retour" id="date_retour"
                                                   class="form-control @error('date_retour') is-invalid @enderror"
                                                   value="{{ old('date_retour', now()->format('Y-m-d')) }}" required>
                                            @error('date_retour')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                               {{--  <div class="form-group">
                                    <label for="raison_retour">Raison du retour *</label>
                                    <textarea name="raison_retour" id="raison_retour" rows="4"
                                              class="form-control @error('raison_retour') is-invalid @enderror"
                                              placeholder="Décrivez la raison du retour (produit défectueux, erreur de livraison, etc.)..."
                                              required>{{ old('raison_retour','neant') }}</textarea>
                                    @error('raison_retour')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div> --}}
                                <input type="hidden" value="neant" name="raison_retour">

                                <!-- Aperçu de l'impact -->
                                <div class="alert alert-info mt-3">
                                    <h6>Impact du retour:</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Nouveau montant net:</strong><br>
                                            <span id="nouveau-montant-net" class="font-weight-bold">0,00 €</span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Nouveau montant restant:</strong><br>
                                            <span id="nouveau-montant-restant" class="font-weight-bold">0,00 €</span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Économie pour le client:</strong><br>
                                            <span id="economie-client" class="text-success font-weight-bold">0,00 €</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-save"></i> Enregistrer le Retour
                            </button>
                            <a href="{{ route('suivi-factures.show', $suiviFacture->id) }}" class="btn btn-secondary btn-lg">
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
    const montantRetourInput = document.getElementById('montant_retour');
    const montantTotal = {{ $suiviFacture->montant }};
    const montantPaye = {{ $suiviFacture->montant_paye }};

    function calculerImpact() {
        const montantRetour = parseFloat(montantRetourInput.value) || 0;

        if (montantRetour > montantTotal) {
            return;
        }

        const nouveauMontantNet = montantTotal - montantRetour;
        const nouveauMontantRestant = Math.max(0, nouveauMontantNet - montantPaye);

        document.getElementById('nouveau-montant-net').textContent = nouveauMontantNet.toFixed(2).replace('.', ',') + ' €';
        document.getElementById('nouveau-montant-restant').textContent = nouveauMontantRestant.toFixed(2).replace('.', ',') + ' €';
        document.getElementById('economie-client').textContent = montantRetour.toFixed(2).replace('.', ',') + ' €';
    }

    montantRetourInput.addEventListener('input', calculerImpact);

    // Calcul initial
    calculerImpact();
});
</script>
@endpush
