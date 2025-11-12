@extends('layouts.app')

@section('title', 'Nouvelle Facture')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice"></i> Nouvelle Facture
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

                    <form action="{{ route('suivi-factures.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_facture">Numéro de facture *</label>
                                    <input type="text" name="numero_facture" id="numero_facture"
                                           class="form-control @error('numero_facture') is-invalid @enderror"
                                           value="{{ old('numero_facture') }}"
                                           placeholder="Ex: FACT-2024-001" required>
                                    @error('numero_facture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_livraison">Date de livraison *</label>
                                    <input type="date" name="date_livraison" id="date_livraison"
                                           class="form-control @error('date_livraison') is-invalid @enderror"
                                           value="{{ old('date_livraison', now()->format('Y-m-d')) }}" required>
                                    @error('date_livraison')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_id">Client *</label>
                                    <select name="client_id" id="client_id" class="form-control @error('client_id') is-invalid @enderror" required>
                                        <option value="">Sélectionner un client</option>
                                        @foreach($clients as $client)
                                        <option value="{{ $client->id }}"
                                            {{ old('client_id', request('client_id')) == $client->id ? 'selected' : '' }}>
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
                                    <label for="montant">Montant (€) *</label>
                                    <input type="number" name="montant" id="montant"
                                           class="form-control @error('montant') is-invalid @enderror"
                                           value="{{ old('montant') }}"
                                           step="0.01" min="0" required>
                                    @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Enregistrer la Facture
                            </button>
                            <a href="{{ route('suivi-factures.index') }}" class="btn btn-secondary btn-lg">
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
