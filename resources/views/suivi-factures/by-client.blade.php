@extends('layouts.app')

@section('title', 'Factures - ' . $client->nom)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice-dollar"></i> Factures de {{ $client->nom }}
                    </h4>
                    <div>
                        <a href="{{ route('suivi-factures.create', ['client_id' => $client->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle Facture
                        </a>
                        <a href="{{ route('clients.show', $client->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour au client
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Informations du client -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <strong>Client:</strong><br>
                                        {{ $client->nom }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Ville:</strong><br>
                                        {{ $client->ville ?? '-' }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Total factures:</strong><br>
                                        {{ $factures->total() }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Montant total:</strong><br>
                                        <span class=" badge bg-danger">
                                            <h6>{{ number_format($factures->sum('montant'), 0, ',', ' ') }} CFA </h6>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($factures->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Date Facture</th>
                                    <th>N° Facture</th>

                                    <th>Montant</th>
                                    <th>Etat</th>
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
                                        <span class="badge bg-success badge-pill" style="font-size: 1em;">
                                            {{ number_format($facture->montant, 0, ',', ' ') }} CFA
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
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $factures->links() }}
                    </div>
                    @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Aucune facture enregistrée pour ce client.
                        <br>
                        <a href="{{ route('suivi-factures.create', ['client_id' => $client->id]) }}"
                           class="btn btn-primary mt-2">
                            <i class="fas fa-plus"></i> Créer la première facture
                        </a>
                    </div>
                    @endif
                </div>
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
            order: [[0, 'desc']],
            lengthMenu: [10, 25, 50, 100, 200,500,1000,5000,10000]

    });
</script>
@endpush
