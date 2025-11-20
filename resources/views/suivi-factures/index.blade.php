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
                        <a href="{{ route('paiements.index') }}" class="btn btn-success">
                            <i class="fas fa-money-bill-wave"></i> Tous les Paiements
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
                                <label>État livraison</label>
                                <select name="etat" class="form-control">
                                    <option value="">Tous les états</option>
                                    <option value="livré" {{ request('etat') == 'livré' ? 'selected' : '' }}>Livré</option>
                                    <option value="non livré" {{ request('etat') == 'non livré' ? 'selected' : '' }}>Non livré</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Statut paiement</label>
                                <select name="statut_paiement" class="form-control">
                                    <option value="">Tous les statuts</option>
                                    <option value="payé" {{ request('statut_paiement') == 'payé' ? 'selected' : '' }}>Payé</option>
                                    <option value="partiel" {{ request('statut_paiement') == 'partiel' ? 'selected' : '' }}>Partiel</option>
                                    <option value="impayé" {{ request('statut_paiement') == 'impayé' ? 'selected' : '' }}>Impayé</option>
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
                            <!-- Dans les filtres, ajouter -->
                            <div class="col-md-2">
                                <label>Avec retour</label>
                                <select name="avec_retour" class="form-control">
                                    <option value="">Toutes</option>
                                    <option value="1" {{ request('avec_retour') == '1' ? 'selected' : '' }}>Avec retour</option>
                                    <option value="0" {{ request('avec_retour') == '0' ? 'selected' : '' }}>Sans retour</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100 mr-2">
                                    <i class="fas fa-filter"></i> Filtrer
                                </button>
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
                        <button type="button" class="close" data-bs-dismiss="alert">
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
                                    <th>État Livraison</th>
                                    <th>Paiement</th>
                                    <th>Montant Restant</th>
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
                                        <a href="{{ route('suivi-factures.by-client', $facture->client->id) }}" class="text-dark">
                                            {{ $facture->client->nom }}
                                        </a>
                                        @if($facture->client->ville)
                                        <br><small class="text-muted">{{ $facture->client->ville }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- <span class="badge bg-dark bg-pill" style="font-size: 1em;">
                                            {{ number_format($facture->montant, 0, ',', ' ') }} CFA
                                        </span> --}}
                                        <div class="text-center">
                                            <span class="badge bg-dark bg-pill" style="font-size: 1em;">
                                                {{ number_format($facture->montant, 0, ',', ' ') }} CFA
                                            </span>
                                            @if($facture->a_retour)
                                            <br>
                                            <small class="badge bg-danger">
                                                <i class="fas fa-undo"></i> -
                                                 {{ number_format($facture->montant_retour, 0, ',', ' ') }}CFA
                                            </small>
                                            <br>
                                            <small class="badge bg-success">
                                                Net: {{ number_format($facture->montant_net, 0, ',', ' ') }}CFA
                                            </small>
                                            @endif
                                        </div>
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
                                            <button class="btn btn-sm btn-primary changer-etat"
                                                    data-toggle="tooltip"
                                                    title="Changer l'état">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="paiement-info">
                                            <!-- Barre de progression -->
                                            <div class="progress" style="height: 8px; width: 100px;" data-toggle="tooltip"
                                                 title="Payé: {{ number_format($facture->montant_paye, 0, ',', ' ') }}CFA / Restant: {{ number_format($facture->montant_restant, 0, ',', ' ') }}CFA">
                                                <div class="progress-bar
                                                    @if($facture->statut_paiement == 'payé') bg-success
                                                    @elseif($facture->statut_paiement == 'partiel') bg-warning
                                                    @else bg-danger @endif"
                                                     style="width: {{ $facture->pourcentage_paiement }}%">
                                                </div>
                                            </div>

                                            <!-- Statut et bouton paiement -->
                                            <div class="d-flex align-items-center mt-1">
                                                <span class="badge bg-{{ $facture->statut_paiement == 'payé' ? 'success' : ($facture->statut_paiement == 'partiel' ? 'warning' : 'danger') }} mr-2">
                                                    {{ ucfirst($facture->statut_paiement) }}
                                                </span>
                                                <button class="btn btn-sm btn-outline-success ajouter-paiement"
                                                        data-facture-id="{{ $facture->id }}"
                                                        data-facture-montant="{{ $facture->montant }}"
                                                        data-facture-restant="{{ $facture->montant_restant }}"
                                                        data-facture-numero="{{ $facture->numero_facture }}"
                                                        data-toggle="tooltip"
                                                        title="Ajouter un paiement">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($facture->montant_restant > 0)
                                        <span class="badge bg-danger font-weight-bold">
                                            {{ number_format($facture->montant_restant, 0, ',', ' ') }} CFA
                                        </span>
                                        @else
                                        <span class="badge bg-success font-weight-bold">0,00 CFA</span>
                                        @endif
                                    </td>
                                    <td>
                                              <!-- Bouton Retour -->
                                                @if($facture->a_retour)
                                                <a href="{{ route('suivi-factures.retour', $facture->id) }}"
                                                class="btn btn-danger" title="Voir le retour">
                                                    <i class="fas fa-undo"></i> Retour
                                                </a>
                                                @else
                                                <a href="{{ route('suivi-factures.retour', $facture->id) }}"
                                                class="btn btn-danger" title="Ajouter un retour">
                                                    <i class="fas fa-undo"></i> Retour
                                                </a>
                                                @endif
                                            <a href="{{ route('suivi-factures.show', $facture->id) }}"
                                               class="btn btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('suivi-factures.edit', $facture->id) }}"
                                               class="btn btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('paiements.by-facture', $facture->id) }}"
                                               class="btn btn-success" title="Voir les paiements">
                                                <i class="fas fa-money-bill-wave"></i>
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
                        @if(request()->hasAny(['client_id', 'date_debut', 'date_fin', 'etat', 'statut_paiement']))
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
                                {{ number_format($factures->sum('montant'), 0, ',', ' ') }} CFA
                            </h5>
                            <small>Montant Total</small>
                        </div>
                        <div class="col-md-2">
                            <h5 class="mb-0 text-info">
                                {{ $factures->where('etat', 'livré')->count() }}
                            </h5>
                            <small>Livrées</small>
                        </div>
                        <div class="col-md-2">
                            <h5 class="mb-0 text-warning">
                                {{ $factures->where('statut_paiement', 'payé')->count() }}
                            </h5>
                            <small>Payées</small>
                        </div>
                        <div class="col-md-2">
                            <h5 class="mb-0 text-danger">
                                {{ $factures->where('statut_paiement', '!=', 'payé')->count() }}
                            </h5>
                            <small>En Attente</small>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal pour changer l'état de livraison -->
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

<!-- Modal pour ajouter un paiement -->
<div class="modal fade" id="paiementModal" tabindex="-1" role="dialog" aria-labelledby="paiementModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paiementModalLabel">Ajouter un Paiement</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="paiementForm">
                    @csrf
                    <input type="hidden" name="suivi_facture_id" id="suivi_facture_id">

                    <div class="alert alert-info">
                        <strong>Facture:</strong> <span id="facture-numero"></span><br>
                        <strong>Montant total:</strong> <span id="facture-montant-total"></span><br>
                        <strong>Montant restant:</strong> <span id="facture-montant-restant" class="font-weight-bold"></span>
                    </div>

                    <div class="form-group">
                        <label for="montant">Montant du paiement *</label>
                        <input type="number" name="montant" id="montant"
                               class="form-control" step="0.01" min="0.01" required>
                        <small class="form-text text-muted" id="montant-help"></small>
                    </div>

                    <div class="form-group">
                        <label for="date_paiement">Date du paiement *</label>
                        <input type="date" name="date_paiement" id="date_paiement"
                               class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="mode_paiement">Mode de paiement *</label>
                        <select name="mode_paiement" id="mode_paiement" class="form-control" required>
                            <option value="cheque">Chèque</option>
                            <option value="espece">Espèces</option>
                            <option value="virement">Virement</option>

                        </select>
                    </div>

                   {{--  <div class="form-group">
                        <label for="reference">Référence (optionnel)</label>
                        <input type="text" name="reference" id="reference"
                               class="form-control" placeholder="N° chèque, référence virement...">
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes (optionnel)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="2"
                                  placeholder="Informations complémentaires..."></textarea>
                    </div> --}}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="savePaiement">Enregistrer le Paiement</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== GESTION ÉTAT LIVRAISON ==========
    const etatSelect = document.getElementById('etat');
    const dateLivraisonGroup = document.getElementById('date-livraison-group');

    if (etatSelect && dateLivraisonGroup) {
        etatSelect.addEventListener('change', function() {
            if (this.value === 'livré') {
                dateLivraisonGroup.style.display = 'block';
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
            document.getElementById('facture_id').value = factureId;
            $('#etatModal').modal('show');
        });
    });

    // Sauvegarder l'état
    const saveEtatButton = document.getElementById('saveEtat');
    if (saveEtatButton) {
        saveEtatButton.addEventListener('click', function() {
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
                    const container = document.querySelector(`[data-facture-id="${factureId}"]`);
                    const etatBadge = container.querySelector('.etat-badge');

                    if (data.facture.etat === 'livré') {
                        etatBadge.className = 'badge bg-success etat-badge';
                        etatBadge.innerHTML = '<i class="fas fa-check"></i> Livré le '+data.facture.date_livraison;
                     //   const dateCell = container.closest('tr').querySelector('td:nth-child(3)');
                     //   dateCell.textContent = data.facture.date_livraison;
                    } else {
                        etatBadge.className = 'badge bg-warning etat-badge';
                        etatBadge.innerHTML = '<i class="fas fa-clock"></i> Non livré';
                        const dateCell = container.closest('tr').querySelector('td:nth-child(3)');
                        dateCell.innerHTML = '<span class="text-muted">-</span>';
                    }

                    $('#etatModal').modal('hide');
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

    // ========== GESTION PAIEMENTS ==========
    const montantInput = document.getElementById('montant');
    const montantHelp = document.getElementById('montant-help');
    let montantRestant = 0;

    // Gérer le clic sur le bouton ajouter paiement
    const ajouterPaiementButtons = document.querySelectorAll('.ajouter-paiement');
    ajouterPaiementButtons.forEach(button => {
        button.addEventListener('click', function() {
            const factureId = this.getAttribute('data-facture-id');
            const factureMontant = parseFloat(this.getAttribute('data-facture-montant'));
            const factureRestant = parseFloat(this.getAttribute('data-facture-restant'));
            const factureNumero = this.getAttribute('data-facture-numero');

            // Remplir le modal
            document.getElementById('suivi_facture_id').value = factureId;
            document.getElementById('facture-numero').textContent = factureNumero;
            document.getElementById('facture-montant-total').textContent = factureMontant.toFixed(2).replace('.', ',') + ' CFA';
            document.getElementById('facture-montant-restant').textContent = factureRestant.toFixed(2).replace('.', ',') + ' CFA';

            // Mettre à jour l'aide du montant
            montantRestant = factureRestant;
            montantHelp.textContent = `Montant maximum: ${factureRestant.toFixed(2).replace('.', ',')} CFA`;

            // Réinitialiser et configurer le champ montant
            montantInput.value = '';
            montantInput.max = factureRestant;

            // Définir la date d'aujourd'hui par défaut
            document.getElementById('date_paiement').value = new Date().toISOString().split('T')[0];

            $('#paiementModal').modal('show');
        });
    });

    // Validation du montant en temps réel
    if (montantInput) {
        montantInput.addEventListener('input', function() {
            const valeur = parseFloat(this.value) || 0;

            if (valeur > montantRestant) {
                this.classList.add('is-invalid');
                montantHelp.classList.add('text-danger');
                montantHelp.textContent = `Erreur: Le montant dépasse le montant restant (${montantRestant.toFixed(2).replace('.', ',')} CFA)`;
            } else {
                this.classList.remove('is-invalid');
                montantHelp.classList.remove('text-danger');
                montantHelp.textContent = `Montant maximum: ${montantRestant.toFixed(2).replace('.', ',')} CFA`;
            }
        });
    }

    // Sauvegarder le paiement
    const savePaiementButton = document.getElementById('savePaiement');
    if (savePaiementButton) {
        savePaiementButton.addEventListener('click', function() {
            const formData = new FormData(document.getElementById('paiementForm'));
            const montant = parseFloat(formData.get('montant'));

            // Validation côté client
            if (montant > montantRestant) {
                showAlert('Le montant du paiement dépasse le montant restant!', 'error');
                return;
            }

            if (montant <= 0) {
                showAlert('Le montant doit être supérieur à 0!', 'error');
                return;
            }

            fetch('{{ route("paiements.store") }}', {
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
                    $('#paiementModal').modal('hide');
                    showAlert('Paiement enregistré avec succès!', 'success');

                    // Recharger la page pour mettre à jour les données
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Erreur lors de l\'enregistrement du paiement', 'error');
            });
        });
    }

    // ========== FONCTIONS UTILITAIRES ==========
    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="close" data-bs-dismiss="alert">
                <span>&times;</span>
            </button>
        `;
        document.querySelector('.card-body').insertBefore(alert, document.querySelector('.card-body').firstChild);

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
            ordering:false,
            order: [[0, 'desc']],
            lengthMenu: [10, 25, 50, 100, 200,500,1000,5000,10000]

    });
</script>
@endpush

@push('styles')
<style>
.etat-livraison, .paiement-info {
    display: flex;
    align-items: center;
    gap: 5px;
}

.changer-etat, .ajouter-paiement {
    padding: 2px 6px;
    font-size: 0.8em;
}

.badge {
    font-size: 0.9em;
}

.progress {
    cursor: help;
}

.paiement-info .progress {
    min-width: 100px;
}
</style>
@endpush
