@extends('layouts.app')

@section('title', 'Utilisateur')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-users"></i> Gestion des Utilisateurs</h1>
    <a href="{{ route('user.create') }}" class="btn btn-primary">
        <i class="fas fa-add"></i> Ajouter Utilisateur
    </a>
</div>
<div class="row">

</div>
<div class="row">
    <div class="col-12">

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        @if ($message = Session::get('error'))
            <div class="alert alert-danger">
                <p>{{ $message }}</p>
            </div>
        @endif

        <div class="card ">
            <div class="card-header">LISTES DES UTILISATEURS</div>
            <div class="card-body">

                <table id="datatable-buttons" class="table table-bordered  table-striped text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom </th>
                            <th>EMAIL</th>

                            <th>ROLE</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email}}</td>
                            <td>{{ $user->role}}</td>
                            <td>
                                <a href="{{ route('user.edit', $user->id) }}" role="button" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                {!! Form::open(['method' => 'DELETE', 'route'=>['user.destroy', $user->id], 'style'=> 'display:inline', 'onclick'=>"if(!confirm('Êtes-vous sûr de vouloir supprimer cet enregistrement ?')) { return false; }"]) !!}
                                <button class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
                                {!! Form::close() !!}

                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#exampleModalform2{{$user->id}}">
                                    modifier Mot de passe
                                </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModalform2{{$user->id}}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modification mot de passe</h5>
                                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('user.password.update') }}" method="POST">
                                                @csrf
                                            <div class="modal-body">

                                                <input type="hidden" name="id" value="{{$user->id}}">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="field-3" class="control-label">Mot de passe</label>
                                                            <input type="password" class="form-control" id="field-3" placeholder="Mot de passe"  name="password">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group no-margin">
                                                            <label for="field-7" class="control-label">Repetez Mot de passe</label>
                                                            <input type="password" name="password_confirmation" class="form-control" id="field-3" placeholder="Address">                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submint" class="btn btn-primary">Modifier mot de passe</button>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </td>

                        </tr>
                        @endforeach

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

@endsection
