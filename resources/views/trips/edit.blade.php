@extends('layouts.app')

@section('title', 'Modifier le Trajet')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Modifier le Trajet
                        <small class="text-muted">- {{ $trip->nom_trajet ?: $trip->destination }}</small>
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

                    <form action="{{ route('trips.update', $trip->id) }}" method="POST" id="tripForm">
                        @csrf
                        @method('PUT')

                        <!-- Informations de base du trajet -->


                        <!-- Sélection des clients avec ordre -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Clients à Visiter</h5>
                                <button type="button" class="btn btn-light btn-sm" id="addClient">
                                    <i class="fas fa-plus"></i> Ajouter un client
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="clients-container">
                                    @if($trip->clients->count() > 0)
                                        @foreach($trip->clients->sortBy('pivot.ordre_visite') as $index => $client)
                                        <div class="client-item card mb-3">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-1">
                                                        <div class="form-group">
                                                            <label>Ordre</label>
                                                            <input type="number" name="clients[{{ $index }}][ordre]"
                                                                   class="form-control order-input"
                                                                   value="{{ $client->pivot->ordre_visite }}"
                                                                   min="1" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Client *</label>
                                                            <select name="clients[{{ $index }}][id]" class="form-control client-select" required>
                                                                <option value="">Sélectionner un client</option>
                                                                @foreach($clients as $clientOption)
                                                                <option value="{{ $clientOption->id }}"
                                                                    {{ $client->id == $clientOption->id ? 'selected' : '' }}
                                                                    data-adresse="{{ $clientOption->adresse_complete }}"
                                                                    data-ville="{{ $clientOption->ville }}">
                                                                    {{ $clientOption->nom }} @if($clientOption->ville) - {{ $clientOption->ville }} @endif
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label>Notes de livraison (optionnel)</label>
                                                            <textarea name="clients[{{ $index }}][notes_livraison]"
                                                                      class="form-control notes-textarea" rows="1"
                                                                      placeholder="Instructions spécifiques...">{{ $client->pivot->notes_livraison }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>&nbsp;</label>
                                                            <button type="button" class="btn btn-danger btn-block remove-client">
                                                                <i class="fas fa-trash"></i> Supprimer
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="client-info mt-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                        <span class="client-adresse">{{ $client->adresse_complete }}</span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                    <div class="alert alert-info" id="no-clients-message">
                                        <i class="fas fa-info-circle"></i> Aucun client sélectionné. Cliquez sur "Ajouter un client" pour commencer.
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Informations techniques -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">Informations Techniques</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="vehicle_id">Véhicule *</label>
                                            <select name="vehicle_id" id="vehicle_id" class="form-control" required>
                                                <option value="">Sélectionner un véhicule</option>
                                                @foreach($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}"
                                                    {{ old('vehicle_id', $trip->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                                    {{ $vehicle->immatriculation }} - {{ $vehicle->modele }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="fuel_entry_id">Plein associé *</label>
                                            <select name="fuel_entry_id" id="fuel_entry_id" class="form-control" required>
                                                <option value="">Sélectionner un plein</option>
                                                @foreach($fuelEntries as $entry)
                                                <option value="{{ $entry->id }}"
                                                    {{ old('fuel_entry_id', $trip->fuel_entry_id) == $entry->id ? 'selected' : '' }}>
                                                    {{ $entry->vehicle->immatriculation }} -
                                                    {{ $entry->date_remplissage->format('d/m/Y') }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="motif">Motif *</label>
                                            <select name="motif" id="motif" class="form-control" required>
                                                @foreach($motifs as $key => $value)
                                                <option value="{{ $key }}" {{ old('motif', $trip->motif) == $key ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="date_trajet">Date du trajet *</label>
                                            <input type="date" name="date_trajet" id="date_trajet"
                                                   class="form-control"
                                                   value="{{ old('date_trajet', $trip->date_trajet->format('Y-m-d')) }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="nombre_trajets">Nombre de trajets *</label>
                                            <input type="number" name="nombre_trajets" id="nombre_trajets"
                                                   class="form-control"
                                                   value="{{ old('nombre_trajets', $trip->nombre_trajets) }}"
                                                   min="1" max="50" required>
                                        </div>
                                    </div>
                                   {{--  <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="km_depart">KM Départ *</label>
                                            <input type="number" name="km_depart" id="km_depart"
                                                   class="form-control"
                                                   value="{{ old('km_depart', $trip->km_depart) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="km_arrivee">KM Arrivée *</label>
                                            <input type="number" name="km_arrivee" id="km_arrivee"
                                                   class="form-control"
                                                   value="{{ old('km_arrivee', $trip->km_arrivee) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Distance calculée</label>
                                            <input type="text" id="distance_calculee" class="form-control" readonly
                                                   value="{{ $trip->distance_moyenne }} km">
                                        </div>
                                    </div> --}}
                                </div>

                                <div class="form-group">
                                    <label for="destination">Destination principale *</label>
                                    <input type="text" name="destination" id="destination"
                                           class="form-control"
                                           value="{{ old('destination', $trip->destination) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="notes">Notes générales (optionnel)</label>
                                    <textarea name="notes" id="notes" rows="3"
                                              class="form-control">{{ old('notes', $trip->notes) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Mettre à jour le Trajet
                            </button>
                            <a href="{{ route('trips.show', $trip->id) }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template pour un nouveau client -->
<template id="client-template">
    <div class="client-item card mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-1">
                    <div class="form-group">
                        <label>Ordre</label>
                        <input type="number" class="form-control order-input" min="1" value="1" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Client *</label>
                        <select class="form-control client-select" required>
                            <option value="">Sélectionner un client</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}"
                                data-adresse="{{ $client->adresse_complete }}"
                                data-ville="{{ $client->ville }}">
                                {{ $client->nom }} @if($client->ville) - {{ $client->ville }} @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Notes de livraison (optionnel)</label>
                        <textarea class="form-control notes-textarea" rows="1"
                                  placeholder="Instructions spécifiques pour cette livraison..."></textarea>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block remove-client">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </div>
                </div>
            </div>
            <div class="client-info mt-2" style="display: none;">
                <small class="text-muted">
                    <i class="fas fa-map-marker-alt"></i>
                    <span class="client-adresse"></span>
                </small>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
    let currentFilters = {

    vehicle_id: null,

};
function formaterDate(date) {
  const jour = String(date.getDate()).padStart(2, '0');
  const mois = String(date.getMonth() + 1).padStart(2, '0'); // Les mois sont de 0 à 11
  const annee = date.getFullYear();

  return `${jour}/${mois}/${annee}`;
}
document.addEventListener('DOMContentLoaded', function() {
    const clientsContainer = document.getElementById('clients-container');
    const noClientsMessage = document.getElementById('no-clients-message');
    const addClientBtn = document.getElementById('addClient');
    const clientTemplate = document.getElementById('client-template');
    const tripForm = document.getElementById('tripForm');
    let clientCount = {{ $trip->clients->count() }};

    const vehicleSelect = document.getElementById('vehicle_id');

     vehicleSelect.addEventListener('change', function() {
        const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex].value;
        console.log(selectedOption);

        loadFuel(selectedOption);
     });


    // Ajouter un client
    addClientBtn.addEventListener('click', function() {
        if (noClientsMessage) noClientsMessage.style.display = 'none';

        const clone = clientTemplate.content.cloneNode(true);
        const clientItem = clone.querySelector('.client-item');
        clientItem.dataset.index = clientCount;

        // Configurer les inputs
        const orderInput = clientItem.querySelector('.order-input');
        const clientSelect = clientItem.querySelector('.client-select');
        const notesTextarea = clientItem.querySelector('.notes-textarea');
        const removeBtn = clientItem.querySelector('.remove-client');
        const clientInfo = clientItem.querySelector('.client-info');
        const clientAdresse = clientItem.querySelector('.client-adresse');

        // Mettre à jour les noms des inputs pour le formulaire
        orderInput.name = `clients[${clientCount}][ordre]`;
        clientSelect.name = `clients[${clientCount}][id]`;
        notesTextarea.name = `clients[${clientCount}][notes_livraison]`;

        // Afficher les infos du client sélectionné
        clientSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                clientAdresse.textContent = selectedOption.getAttribute('data-adresse');
                clientInfo.style.display = 'block';
            } else {
                clientInfo.style.display = 'none';
            }
        });

        // Supprimer le client
        removeBtn.addEventListener('click', function() {
            clientItem.remove();
            updateOrders();
            if (clientsContainer.querySelectorAll('.client-item').length === 0) {
                if (noClientsMessage) noClientsMessage.style.display = 'block';
            }
        });

        clientsContainer.appendChild(clone);
        clientCount++;
        updateOrders();
    });

    // Mettre à jour les ordres automatiquement
    function updateOrders() {
        const clientItems = clientsContainer.querySelectorAll('.client-item');
        clientItems.forEach((item, index) => {
            const orderInput = item.querySelector('.order-input');
            orderInput.value = index + 1;
        });
    }

    // Calcul de la distance
    const kmDepartInput = document.getElementById('km_depart');
    const kmArriveeInput = document.getElementById('km_arrivee');
    const distanceCalculee = document.getElementById('distance_calculee');

    function calculerDistance() {
        const kmDepart = parseInt(kmDepartInput.value) || 0;
        const kmArrivee = parseInt(kmArriveeInput.value) || 0;

        if (kmArrivee > kmDepart) {
            const distance = kmArrivee - kmDepart;
            distanceCalculee.value = distance + ' km';
            distanceCalculee.style.color = '#28a745';
        } else {
            distanceCalculee.value = 'Invalide';
            distanceCalculee.style.color = '#dc3545';
        }
    }

    kmDepartInput.addEventListener('input', calculerDistance);
    kmArriveeInput.addEventListener('input', calculerDistance);

    // Validation du formulaire
    tripForm.addEventListener('submit', function(e) {
        const clientItems = clientsContainer.querySelectorAll('.client-item');
        if (clientItems.length === 0) {
            e.preventDefault();
            alert('Veuillez ajouter au moins un client au trajet.');
            return;
        }
    });

    // Initialiser les événements pour les clients existants
    function initExistingClients() {
        const existingClientItems = clientsContainer.querySelectorAll('.client-item');
        existingClientItems.forEach((item, index) => {
            const clientSelect = item.querySelector('.client-select');
            const removeBtn = item.querySelector('.remove-client');
            const clientInfo = item.querySelector('.client-info');
            const clientAdresse = item.querySelector('.client-adresse');

            // Afficher les infos du client sélectionné
            if (clientSelect.value) {
                const selectedOption = clientSelect.options[clientSelect.selectedIndex];
                clientAdresse.textContent = selectedOption.getAttribute('data-adresse');
                clientInfo.style.display = 'block';
            }

            // Supprimer le client
            removeBtn.addEventListener('click', function() {
                item.remove();
                updateOrders();
                if (clientsContainer.querySelectorAll('.client-item').length === 0) {
                    if (noClientsMessage) noClientsMessage.style.display = 'block';
                }
            });
        });
    }

    initExistingClients();
});
async function loadFuel(selectedOption) {
         currentFilters.vehicle_id = selectedOption;
        const response = await fetch('{{ route("fuel.by.vehicle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
            },
            body: JSON.stringify(currentFilters)
        });
        console.log('Réponse reçue, status:', response.status);

        const data = await response.json();
        let option = "";
        data.forEach(element => {
            option += ` <option value="${element.id}">

                                           ${ formaterDate(new Date (element.date_remplissage))} -
                                            ${element.litres} litre
                                        </option>`;
        });
        $("#fuel_entry_id").empty();
         $("#fuel_entry_id").append(option);;
        console.log('Données reçues:', data);
    }
</script>
@endpush
