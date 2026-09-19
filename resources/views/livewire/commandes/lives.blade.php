<div>
    <form wire:submit="filtrer">
        <div class="row">
            <div class="col-sm-4">
                <span>
                    <b>{{ $commandes->count() }}</b> commandes en cours
                </span>
            </div>

        </div>
    </form>

    @include('components.alert')

    <div wire:poll.10s class="table-responsive-sm">
        <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
            <thead class="table-dark">
                <tr>
                    <th></th>
                    <td></td>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prenom</th>
                    <th>Téléphone</th>

                    <th>Montant</th>
                   


                    <th>Statut</th>
                    <th>Mode</th>
                    
                    <th>Date</th>
                    <th class="text-end">
                        <span wire:loading>
                            <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="" srcset="">
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
                            <button class="btn btn-sm" data-bs-toggle="modal"
                                data-bs-target="#qr-code-{{ $commande->id }}">
                                <i class="ri-qr-scan-2-line"></i>
                            </button>
                            <!-- Center modal content -->
                            <div class="modal fade" id="qr-code-{{ $commande->id }}" tabindex="-1" role="dialog"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="myCenterModalLabel">
                                                Commande #{{ $commande->id }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h6 class="text-muted">
                                                Veuillez scanner ce code Qr pour impprimer le Reçu de commande .
                                            </h6>
                                            <div class="text-center p-2">
                                                {!! QrCode::size(100)->generate(route('print_commande', ['id' => $commande->id])) !!}
                                            </div>
                                        </div>
                                    </div><!-- /.modal-content -->
                                </div><!-- /.modal-dialog -->
                            </div><!-- /.modal -->
                        </td>
                        <td>{{ $commande->id }}</td>
                        <td>
                            {{ $commande->nom }}



                            @if ($commande->note)
                                <i class="ri-message-2-fill" title="Une note a été ajouté"></i>
                            @endif
                        </td>
                        <td>
                            {{ $commande->prenom }}
                        </td>
                        <td>{{ $commande->phone }}</td>

                        <td>{{ $commande->montant() - $commande->coupon ?? '' }} <x-devise></x-devise> </td>

                   
                        <td>
                            @switch($commande->statut)
                                @case('attente')
                                    <span class="badge bg-warning text-dark">En attente</span>
                                @break

                                @case('traitement')
                                    <span class="badge bg-info text-dark">En Traitemet</span>
                                @break

                                @case('En cours livraison')
                                    <span class="badge bg-success">En cours livraison</span>
                                @break

                                @case('livrée')
                                    <span class="badge bg-primary">Livrée</span>
                                @break

                                @case('retournée')
                                    <span class="badge bg-danger">Retournée</span>
                                @break

                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($commande->statut) }}</span>
                            @endswitch
                        </td>


                        <td>
                            <span class="text-capitalize">
                                {{ $commande->mode }}
                            </span>
                        </td>
                   
                        <td>{{ $commande->created_at }} </td>

                    </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                <div class="text-center">
                                    <div>
                                        <img src="/icons/icons8-ticket-100.png" height="100" width="100" alt=""
                                            srcset="">
                                    </div>
                                    Aucune commande trouvé
                                    @if ($key)
                                        <b> " {{ $key }} " </b>
                                    @endif
                                    .
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>


            </table>
        </div>

        {{ $commandes->links('pagination::bootstrap-4') }}


        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            function confirmStatusChange(event, commandeId) {
                const newStatus = event.target.value;

                Swal.fire({
                    title: 'Etes vous sûr de changer de status?',
                    text: ` Voulez vous réellement changer le tatus à: ${newStatus}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('updateStatus', commandeId, newStatus);
                        Swal.fire(
                            'Changed!',
                            'Le status a été changé avec succès.',
                            'success'
                        );
                    } else {
                        // Reset the dropdown to the original value if the user cancels
                        event.target.value = event.target.getAttribute('data-current-status');
                    }
                });
            }
        </script>

    </div>
