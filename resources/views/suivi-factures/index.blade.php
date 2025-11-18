@extends('layouts.app')

@section('title', 'Suivi des Factures')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice-dollar"></i> Suivi des Factures
                    </h4>
                    <div>
                        <a href="{{ route('suivi-factures.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle Facture
                        </a>
                        <a href="{{ route('suivi-factures.statistics') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> Statistiques
                        </a>
                    </div>
                </div>

                <!-- Filtres et Recherche -->
                <div class="card-body bg-light">
                    <form method="GET" action="{{ route('suivi-factures.index') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Client</label>
                                <select name="client_id" class="form-control">
                                    <option value="">Tous les clients</option>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}"
                                        {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->nom }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>État</label>
                                <select name="etat" class="form-control">
                                    <option value="">Tous les états</option>
                                    <option value="livré" {{ request('etat') == 'livré' ? 'selected' : '' }}>Livré</option>
                                    <option value="non livré" {{ request('etat') == 'non livré' ? 'selected' : '' }}>Non livré</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Date début</label>
                                <input type="date" name="date_debut" class="form-control"
                                    value="{{ request('date_debut') }}">
                            </div>
                            <div class="col-md-2">
                                <label>Date fin</label>
                                <input type="date" name="date_fin" class="form-control"
                                    value="{{ request('date_fin') }}">
                            </div>
                            <div class="col-md-3">
                                <label>Recherche</label>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control"
                                           placeholder="N° facture, client..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <a href="{{ route('suivi-factures.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    @endif

                    @if($factures->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="fact_table">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Date Facture</th>
                                    <th>N° Facture</th>
                                    <th>Client</th>
                                    <th>Montant</th>
                                    <th>État</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($factures as $facture)
                                <tr>
                                    <td>
                                        {{ $facture->date_facture->format('d/m/Y') }}

                                    </td>
                                    <td>
                                        <strong>{{ $facture->numero_facture }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{ route('clients.show', $facture->client->id) }}" class="text-dark">
                                            {{ $facture->client->nom }}
                                        </a>
                                        @if($facture->client->ville)
                                        <br><small class="text-muted">{{ $facture->client->ville }}</small>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge bg-success badge-pill" style="font-size: 1em;">
                                            {{ number_format($facture->montant, 2, ',', ' ') }} XOF
                                        </span>
                                    </td>
                                    <td>
                                        <div class="etat-livraison" data-facture-id="{{ $facture->id }}">
                                            @if($facture->etat === 'livré')
                                                <span class="badge bg-success etat-badge">
                                                    <i class="fas fa-check"></i> Livré le {{ $facture->date_livraison->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning etat-badge">
                                                    <i class="fas fa-clock"></i> Non livré
                                                </span>
                                            @endif
                                            <button class="btn btn-sm btn-outline-primary changer-etat"
                                                    data-toggle="tooltip"
                                                    title="Changer l'état">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                   {{--  <td>
                                        <small class="text-muted">
                                            {{ $facture->created_at->format('d/m/Y') }}
                                        </small>
                                    </td> --}}
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('suivi-factures.show', $facture->id) }}"
                                               class="btn btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('suivi-factures.edit', $facture->id) }}"
                                               class="btn btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('suivi-factures.destroy', $facture->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"
                                                        title="Supprimer"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    {{-- <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Affichage de {{ $factures->firstItem() }} à {{ $factures->lastItem() }}
                            sur {{ $factures->total() }} factures
                        </div>
                        <div>
                            {{ $factures->links() }}
                        </div>
                    </div> --}}
                    @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Aucune facture trouvée.
                        @if(request()->hasAny(['client_id', 'date_debut', 'date_fin', 'search', 'etat']))
                        <br><a href="{{ route('suivi-factures.index') }}" class="btn btn-primary mt-2">Afficher toutes les factures</a>
                        @else
                        <br><a href="{{ route('suivi-factures.create') }}" class="btn btn-primary mt-2">Créer la première facture</a>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Totaux -->
                @if($factures->count() > 0)
                <div class="card-footer bg-dark text-white">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h5 class="mb-0">{{ $factures->count() }}</h5>
                            <small>Total Factures</small>
                        </div>
                        <div class="col-md-3">
                            <h5 class="mb-0 text-success">
                                {{ number_format($factures->sum('montant'), 2, ',', ' ') }} XOF
                            </h5>
                            <small>Montant Total</small>
                        </div>
                        <div class="col-md-3">
                            <h5 class="mb-0 text-info">
                                {{ $factures->where('etat', 'livré')->count() }}
                            </h5>
                            <small>Factures Livrées</small>
                        </div>
                        <div class="col-md-3">
                            <h5 class="mb-0 text-warning">
                                {{ $factures->where('etat', 'non livré')->count() }}
                            </h5>
                            <small>Factures en Attente</small>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal pour changer l'état -->
<div class="modal fade" id="etatModal" tabindex="-1" role="dialog" aria-labelledby="etatModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="etatModalLabel">Changer l'état de livraison</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="etatForm">
                    @csrf
                    <input type="hidden" name="facture_id" id="facture_id">
                    <div class="form-group">
                        <label for="etat">État de livraison</label>
                        <select name="etat" id="etat" class="form-control" required>
                            <option value="non livré">Non livré</option>
                            <option value="livré">Livré</option>
                        </select>
                    </div>
                    <div class="form-group" id="date-livraison-group" style="display: none;">
                        <label for="date_livraison">Date de livraison</label>
                        <input type="date" name="date_livraison" id="date_livraison" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="saveEtat">Enregistrer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Afficher/masquer le champ date de livraison
    const etatSelect = document.getElementById('etat');
    const dateLivraisonGroup = document.getElementById('date-livraison-group');

    if (etatSelect && dateLivraisonGroup) {
        etatSelect.addEventListener('change', function() {
            if (this.value === 'livré') {
                dateLivraisonGroup.style.display = 'block';
                // Définir la date d'aujourd'hui par défaut
                document.getElementById('date_livraison').value = new Date().toISOString().split('T')[0];
            } else {
                dateLivraisonGroup.style.display = 'none';
            }
        });
    }

    // Gérer le clic sur le bouton changer état
    const changerEtatButtons = document.querySelectorAll('.changer-etat');
    changerEtatButtons.forEach(button => {
        button.addEventListener('click', function() {
            const container = this.closest('.etat-livraison');
            const factureId = container.getAttribute('data-facture-id');

            // Remplir le modal avec les données actuelles
            document.getElementById('facture_id').value = factureId;

            // Afficher le modal
            $('#etatModal').modal('show');
        });
    });

    // Sauvegarder l'état
    const saveButton = document.getElementById('saveEtat');
    if (saveButton) {
        saveButton.addEventListener('click', function() {
            const formData = new FormData(document.getElementById('etatForm'));
            const factureId = formData.get('facture_id');

            fetch(`/suivi-factures/${factureId}/update-etat`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mettre à jour l'affichage
                    const container = document.querySelector(`[data-facture-id="${factureId}"]`);
                    const etatBadge = container.querySelector('.etat-badge');

                    if (data.facture.etat === 'livré') {
                        etatBadge.className = 'badge bg-success etat-badge';
                        etatBadge.innerHTML = '<i class="fas fa-check"></i> Livré le '+ data.facture.date_livraison;
                        // Mettre à jour la date de livraison dans la ligne
                      //  const dateCell = container.closest('tr').querySelector('td:nth-child(3)');
                       // dateCell.textContent = data.facture.date_livraison;
                    } else {
                        etatBadge.className = 'badge bg-warning etat-badge';
                        etatBadge.innerHTML = '<i class="fas fa-clock"></i> Non livré';
                        // Mettre à jour la date de livraison dans la ligne
                       // const dateCell = container.closest('tr').querySelector('td:nth-child(3)');
                      //  dateCell.innerHTML = '<span class="text-muted">-</span>';
                    }

                    $('#etatModal').modal('hide');

                    // Afficher un message de succès
                    showAlert('État mis à jour avec succès!', 'success');
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Erreur lors de la mise à jour', 'error');
            });
        });
    }

    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;
        document.querySelector('.card-body').insertBefore(alert, document.querySelector('.card-body').firstChild);

        // Supprimer l'alerte après 5 secondes
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }

    // Initialiser les tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
$('#fact_table').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/fr-FR.json',
        },
            ordering:true,
            order: [[0, 'desc']]

    });
</script>
@endpush

@push('styles')
<style>
.etat-livraison {
    display: flex;
    align-items: center;
    gap: 5px;
}

.changer-etat {
    padding: 2px 6px;
    font-size: 0.8em;
}

.badge {
    font-size: 0.9em;
}
</style>
@endpush
