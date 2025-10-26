@extends('layouts.app')

@section('title', 'Nouveau Client')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus"></i> Nouveau Client
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

                    <form action="{{ route('clients.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom">Nom du client *</label>
                                    <input type="text" name="nom" id="nom"
                                           class="form-control @error('nom') is-invalid @enderror"
                                           value="{{ old('nom') }}"
                                           placeholder="Nom de l'entreprise ou du client" required>
                                    @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                        </div>

                        <div class="form-group">
                            <label for="adresse">Adresse complète *</label>
                            <textarea name="adresse" id="adresse" rows="2"
                                      class="form-control @error('adresse') is-invalid @enderror"
                                      placeholder="Numéro, rue, complément d'adresse..." required>{{ old('adresse') }}</textarea>
                            @error('adresse')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>



                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Créer le Client
                            </button>
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary btn-lg">
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
