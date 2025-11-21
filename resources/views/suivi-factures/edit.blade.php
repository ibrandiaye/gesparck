@extends('layouts.app')

@section('title', 'Modifier la Facture')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Modifier la Facture
                        <small class="text-muted">- {{ $suiviFacture->numero_facture }}</small>
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

                    <form action="{{ route('suivi-factures.update', $suiviFacture->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_facture">Numéro de facture *</label>
                                    <input type="text" name="numero_facture" id="numero_facture"
                                           class="form-control @error('numero_facture') is-invalid @enderror"
                                           value="{{ old('numero_facture', $suiviFacture->numero_facture ) }}" required>
                                    @error('numero_facture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_livraison">Date de Facture *</label>
                                    <input type="date" name="date_facture" id="date_facture"
                                           class="form-control @error('date_facture') is-invalid @enderror"
                                           value="{{ old('date_facture', $suiviFacture->date_facture->format('Y-m-d')) }}" required>
                                    @error('date_facture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                           {{--  <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date_livraison">Date de livraison *</label>
                                    <input type="date" name="date_livraison" id="date_livraison"
                                           class="form-control @error('date_livraison') is-invalid @enderror"
                                           value="{{ old('date_livraison', $suiviFacture->date_livraison->format('Y-m-d')) }}" required>
                                    @error('date_livraison')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_id">Client *</label>
                                    <select name="client_id" id="client_id" class="form-control @error('client_id') is-invalid @enderror" required>
                                        <option value="">Sélectionner un client</option>
                                        @foreach($clients as $client)
                                        <option value="{{ $client->id }}"
                                            {{ old('client_id', $suiviFacture->client_id) == $client->id ? 'selected' : '' }}>
                                            {{ $client->nom }}
                                            @if($client->ville) - {{ $client->ville }} @endif
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="montant">Montant (CFA) *</label>
                                    <input type="number" name="montant" id="montant"
                                           class="form-control @error('montant') is-invalid @enderror"
                                           value="{{ old('montant', $suiviFacture->montant) }}"
                                           step="0.01" min="0" required>
                                    @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="montant_retour">Montant du retour (CFA) *</label>
                                            <input type="number" name="montant_retour" id="montant_retour"
                                                   class="form-control @error('montant_retour') is-invalid @enderror"
                                                   value="{{ old('montant_retour') }}"
                                                    max="{{ $suiviFacture->montant }}" required>
                                            <small class="form-text text-muted">
                                                Montant maximum: {{ number_format($suiviFacture->montant, 2, ',', ' ') }} CFA
                                            </small>
                                            @error('montant_retour')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Mettre à jour
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
