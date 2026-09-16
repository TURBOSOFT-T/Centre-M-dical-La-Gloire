<div>
    <div>
        <!-- Formulaire de filtrage -->
        <form wire:submit="filtrer">
            <div class="row g-2 align-items-end">

                <div class="col-sm-6 col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" wire:model.live="key" placeholder="Nom, prénom, téléphone..." class="form-control">
                </div>

                <div class="col-sm-6 col-md-2">
                    <label class="form-label">Confirmation</label>
                    <select class="form-control" wire:model="statut2">
                        <option value="">Tous</option>
                        <option value="confirmé">Confirmé</option>
                        <option value="non_confirmer">Non confirmé</option>
                    </select>
                </div>

                <div class="col-sm-6 col-md-2">
                    <label class="form-label">État</label>
                    <select class="form-control" wire:model="statut">
                        <option value="">Tous</option>
                        <option value="créé">Créé</option>
                        <option value="traitement">Traitement</option>
                        <option value="livrée">Livrée</option>
                        <option value="payée">Payée</option>
                        <option value="retournée">Retournée</option>
                    </select>
                </div>

                <div class="col-sm-6 col-md-2">
                    <label class="form-label">Caisses/Boutique</label>
                    <select class="form-control" wire:model="commercial_id">
                        <option value="">Tous</option>
                        @foreach ($commerciaux as $comm)
                            <option value="{{ $comm->id }}">{{ $comm->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-6 col-md-3">
                    <label class="form-label">Date exacte</label>
                    <input type="date" class="form-control" wire:model="date">
                </div>

            </div>

            <div class="row g-2 mt-1 align-items-end">
                <div class="col-sm-6 col-md-3">
                    <label class="form-label">Du</label>
                    <input type="datetime-local" class="form-control" wire:model="date_debut">
                </div>

                <div class="col-sm-6 col-md-3">
                    <label class="form-label">Au</label>
                    <input type="datetime-local" class="form-control" wire:model="date_fin">
                </div>

                <div class="col-sm-4 col-md-2 mt-2">
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="ri-filter-3-line"></i> Filtrer
                    </button>
                </div>

                <div class="col-sm-4 col-md-2 mt-2">
                    <button class="btn btn-secondary w-100" type="button" wire:click="resetFilters">
                        Réinitialiser
                    </button>
                </div>

                @if ($selectedCommandes)
                    <div class="col-sm-4 col-md-2 mt-2">
                        <button type="button" class="btn btn-outline-secondary w-100" wire:click="getSelectedCommandes">
                            Exporter ({{ count($selectedCommandes) }})
                        </button>
                    </div>
                @endif
            </div>
        </form>

        <!-- Tableau des ventes par caisse -->
        @if (!empty($totauxParCommercial))
            <div class="mt-4 mb-3">
                <h5 class="mb-2">Total des ventes par caisse :</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Caisse</th>
                                <th>Total des ventes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($totauxParCommercial as $id => $data)
                                <tr>
                                    <td>{{ $data['nom'] ?? 'Client' }}</td>
                                    <td><strong>{{ $data['total'] }} <x-devise></x-devise></strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="mt-3 mb-2">
            <b>{{ $commandes->count() }}</b> résultats sur {{ $total }}
        </div>

        @include('components.alert')

        <!-- Tableau principal des commandes -->
        <div wire:poll.50s class="table-responsive-sm">
            <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 30px;"></th>
                        <th style="width: 40px;"></th>
                        <th>Nom</th>
                        <th>Responsable</th> 
                        <th>Montant</th>
                        <th>Traitement</th>
                        <th>Statut</th>
                        <th>Mode</th>
                        <th>Coupon (Valeur)</th>
                        <th class="text-end">
                            <span wire:loading>
                                <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="Chargement...">
                            </span>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($commandes as $commande)
                        <tr>
                            <td>
                                <input type="checkbox" wire:click="toggleCommandeSelection({{ $commande->id }})">
                            </td>
                            <td>
                                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#qr-code-{{ $commande->id }}">
                                    <i class="ri-qr-scan-2-line"></i>
                                </button>

                                <!-- Modal QR Code -->
                                <div class="modal fade" id="qr-code-{{ $commande->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Commande #{{ $commande->id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <h6 class="text-muted mb-3">
                                                    Veuillez scanner ce code QR pour imprimer le reçu de commande.
                                                </h6>
                                                <div class="p-2 d-inline-block border rounded">
                                                    {!! QrCode::size(120)->generate(route('print_commande', ['id' => $commande->id])) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                {{ $commande->nom }}
                                @if ($commande->note)
                                    <i class="ri-message-2-fill text-warning" title="Une note a été ajoutée"></i>
                                @endif
                            </td>

                            <td>
                                {{ $commande->caissier->nom ?? ' -------' }} 
                            </td>

                            <td>
                                <strong>{{ $commande->montant() - $commande->coupon ?? '' }} <x-devise></x-devise></strong>
                            </td>

                            <td>
                                @can('live_order_edit')
                                    @if ($commande->statut === 'payée')
                                        <b class="text-success">
                                            <i class="ri-check-double-fill"></i> Payée
                                        </b>
                                    @elseif($commande->statut == 'retournée')
                                        <b class="text-danger">
                                            @if ($commande->etat == 'confirmé')
                                                <i class="ri-text-wrap"></i> Retournée
                                            @else
                                                <i class="ri-close-circle-line"></i> Annulée
                                            @endif
                                        </b>
                                    @else
                                        @if ($commande->etat == 'confirmé')
                                            <select class="form-select form-select-sm"
                                                onchange="confirmStatusChange(event, {{ $commande->id }})"
                                                data-current-status="{{ $commande->statut }}">
                                                <option value="créé" {{ $commande->statut === 'créé' ? 'selected' : '' }}>Créé</option>
                                                <option value="traitement" {{ $commande->statut === 'traitement' ? 'selected' : '' }}>En Traitement</option>
                                                <option value="En cours livraison" {{ $commande->statut === 'En cours livraison' ? 'selected' : '' }}>En cours de Livraison</option>
                                                <option value="livrée" {{ $commande->statut === 'livrée' ? 'selected' : '' }}>Livrée</option>
                                                <option value="payée" {{ $commande->statut === 'payée' ? 'selected' : '' }}>Payée</option>
                                                <option value="retournée" {{ $commande->statut === 'retournée' ? 'selected' : '' }}>Retournée</option>
                                            </select>
                                        @elseif($commande->etat == 'attente')
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-primary" wire:click="confirmer({{ $commande->id }})">
                                                    <i class="ri-checkbox-circle-line"></i> Valider
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" wire:click="annuler({{ $commande->id }})">
                                                    <i class="ri-close-line"></i> Annuler
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                <i class="ri-close-circle-line"></i> Annulée
                                            </span>
                                        @endif
                                    @endif
                                @endcan
                            </td>

                            <td>
                                @switch($commande->statut)
                                    @case('attente')
                                        <span class="badge bg-warning text-dark">En attente</span>
                                        @break
                                    @case('traitement')
                                        <span class="badge bg-info text-dark">En Traitement</span>
                                        @break
                                    @case('En cours livraison')
                                        <span class="badge bg-primary">En cours livraison</span>
                                        @break
                                    @case('livrée')
                                        <span class="badge bg-success">Livrée</span>
                                        @break
                                    @case('retournée')
                                        <span class="badge bg-danger">Retournée</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($commande->statut) }}</span>
                                @endswitch
                            </td>

                            <td>
                                <span class="text-capitalize">{{ $commande->mode }}</span>
                            </td>

                            <td>
                                @if ($commande->coupon)
                                    {{ $commande->coupon }} <x-devise></x-devise>
                                @else
                                    <span class="text-muted">---</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <div class="btn-group">
                                    @can('order_edit')
                                        @if ($commande->modifiable())
                                            <button class="btn btn-sm btn-warning" onclick="url('{{ route('edit_commande', ['id' => $commande->id]) }}')" title="Modifier">
                                                <i class="ri-edit-2-line"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-sm btn-primary" onclick="add_note({{ $commande->id }}, '{{ $commande->nom }}')" title="Ajouter une note">
                                            <i class="ri-sticky-note-add-line"></i> Note
                                        </button>
                                    @endcan

                                    <button class="btn btn-info btn-sm" type="button" title="Imprimer la commande" onclick="url('{{ route('print_commande', ['id' => $commande->id]) }}')">
                                        <i class="ri-printer-line"></i>
                                    </button>

                                    <button class="btn btn-sm btn-dark" title="Détails" onclick="url('{{ route('details_commande', ['id' => $commande->id]) }}')">
                                        <i class="ri-eye-line"></i>
                                    </button>

                                    @can('order_delete')
                                        <button class="btn btn-sm btn-danger" title="Supprimer" onclick="toggle_confirmation({{ $commande->id }})">
                                            <i class="ri-delete-bin-6-line"></i>
                                        </button>
                                    @endcan
                                </div>

                                @can('order_delete')
                                    <button class="btn btn-sm btn-success d-none mt-1" type="button" id="confirmBtn{{ $commande->id }}" wire:click="delete({{ $commande->id }})">
                                        Confirmer
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <img src="/icons/icons8-ticket-100.png" height="80" width="80" alt="Aucun résultat" class="mb-2">
                                <p class="text-muted mb-0">
                                    Aucune commande trouvée
                                    @if ($key)
                                        pour <strong>" {{ $key }} "</strong>
                                    @endif.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $commandes->links('pagination::bootstrap-4') }}
        </div>

        <!-- Script SweetAlert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmStatusChange(event, commandeId) {
                const selectElement = event.target;
                const newStatus = selectElement.value;
                const currentStatus = selectElement.getAttribute('data-current-status');

                Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: `Voulez-vous réellement changer le statut à : "${newStatus}" ?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, modifier',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('updateStatus', commandeId, newStatus);
                        Swal.fire(
                            'Modifié !',
                            'Le statut a été mis à jour avec succès.',
                            'success'
                        );
                    } else {
                        // Réinitialiser la valeur du select au statut d'origine si annulé
                        selectElement.value = currentStatus;
                    }
                });
            }
        </script>

    </div>
</div>