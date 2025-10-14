@extends('layouts.app')

@section('title', 'Nouveau Remplissage')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-gas-pump"></i> Carburant</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('carburants.store') }}" method="POST" id="fuelForm">
                    @csrf



                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="libelle" class="form-label">libelle *</label>
                                <input type="text"  class="form-control @error('libelle') is-invalid @enderror"
                                       id="libelle" name="libelle"
                                       value="{{ old('libelle') }}"  required>
                                @error('libelle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="montant" class="form-label">Prix du Litre (FCFA) *</label>
                                <input type="number" step="0.001" class="form-control @error('montant') is-invalid @enderror"
                                       id="montant" name="montant"
                                       value="{{ old('montant') }}"  required>
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>





                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('carburants.index') }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


