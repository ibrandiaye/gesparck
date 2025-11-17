@extends('layouts.app')

@section('title', 'Modifier le Client')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Modifier le Client
                        <small class="text-muted">- {{ $client->nom }}</small>
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

                    <form action="{{ route('clientfactures.update', $client->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom">Nom du client *</label>
                                    <input type="text" name="nom" id="nom"
                                           class="form-control @error('nom') is-invalid @enderror"
                                           value="{{ old('nom', $client->nom) }}" required>
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
                                      required>{{ old('adresse', $client->adresse) }}</textarea>
                            @error('adresse')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                            <a href="{{ route('clientfactures.show', $client->id) }}" class="btn btn-secondary btn-lg">
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
