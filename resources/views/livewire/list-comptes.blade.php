<div>

    <form wire:submit="filtrer">
        <div class="row">
            <div class="col-sm-6">
                <span>
                    <b>{{ $clients->count() }}</b> Résultats sur {{ $total }}.
                </span>
            </div>
            <div class="col-sm-6">
                <div class="input-group mb-3">
                    <input type="text" class="form-control btn-sm" wire:model="key" placeholder="Nom, email, phone">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            Filtrer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>


    @include('components.alert')

    <div class="table-responsive-sm">
        <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
            <thead class="table-dark cusor">
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                     
                    <th>Phone</th>
                    <th>Adresse</th>
                   
                    <th>création</th>
                    <th style="text-align: right;">
                        <span wire:loading>
                            <img src="https://i.gifer.com/ZKZg.gif" width="20" height="20" class="rounded shadow"
                                alt="">
                        </span>
                    </th>
                </tr>
            </thead>


            <tbody>
                @forelse ($clients as $client)
                    <tr>
                        <td>
                            {{ $client->nom }}
                        </td>
                        <td>
                            {{ $client->prenom }}
                        </td>

                        
                        <td>
                            {{ $client->phone }}
                        </td>
                        <td>
                            {{ $client->adresse }}
                        </td>
                       
                        <td>{{ $client->created_at }} </td>
                        <td style="text-align: right;">

                             
                            
                            @can('clients_view')
                                <div class="btn-group">
                                    {{-- <button class="btn btn-sm btn-primary" type="button">
                                        <i class="ri-phone-fill"></i> Appeler
                                    </button> --}}
                                    <button class="btn btn-sm btn-danger"
                                        onclick="toggle_confirmation({{ $client->id }})">
                                        <i class="ri-delete-bin-6-line"></i>
                                    </button>
                                </div>
                                <button class="btn btn-sm btn-success d-none" type="button"
                                    id="confirmBtn{{ $client->id }}" wire:click="delete({{ $client->id }})">
                                    <i class="bi bi-check-circle"></i>
                                    <span class="hide-tablete">
                                        Confirmer
                                    </span>
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Aucun client trouvé</td>
                    </tr>
                @endforelse

            </tbody>

            

        </table>
    </div>
    {{ $clients->links('pagination::bootstrap-4') }}

     <style>
        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }

        .badge-success {
            background-color: green;
        }

        .badge-danger {
            background-color: red;
        }
    </style>




 <!-- Modal Historique des Transactions -->
<div class="modal fade {{ $isModalOpen ? 'show' : '' }}" id="transactionHistoryModal" tabindex="-1" aria-labelledby="transactionHistoryModalLabel" aria-hidden="true" style="{{ $isModalOpen ? 'display: block;' : '' }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionHistoryModalLabel">Historique des Transactions</h5>
                <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Historique des Transactions -->
                <div wire:loading>
                    <p>Chargement...</p>
                </div>
                <table class="table" wire:loading.remove>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Type</th>
                            <th>Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->created_at }}</td>
                                <td>{{ $transaction->montant }} <x-devise></x-devise></td>
                                <td>{{ $transaction->type }}</td>
                                <td>{{ $transaction->description }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Aucune transaction disponible</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeModal">Fermer</button>
            </div>
        </div>
    </div>
</div>





 <!-- Modal Historique des Transactions des points -->
 <div class="modal fade {{ $isModalPointOpen ? 'show' : '' }}" id="transactionHistoryModalPoints" tabindex="-1" aria-labelledby="transactionHistoryModalLabel" aria-hidden="true" style="{{ $isModalPointOpen ? 'display: block;' : '' }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionHistoryModalLabel">Historique des points</h5>
                <button type="button" class="btn-close" wire:click="closeModalPoint" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Historique des Transactions -->
                <div wire:loading>
                    <p>Chargement...</p>
                </div>
                <table class="table" wire:loading.remove>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Commande</th>
                            <th>Article</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactionPoints as $point)
                            <tr>
                                <td>{{ $point->created_at }}</td>
                                <td>{{ $point->montant }} <x-devise></x-devise></td>
                                <td>
                                    @if($point->commande)
                                        # {{ $point->commande->id }}
                                    @endif
                                </td>
                                <td>
                                    @if($point->produit)
                                        {{ $point->produit->nom }}
                                        @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Aucune transaction de points</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeModal">Fermer</button>
            </div>
        </div>
    </div>
</div>




    @if ($showModal)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter solde pour {{ $selectedClient }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('showModal', false)"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="addSolde">
                            <div class="mb-3">
                                <label for="solde" class="form-label">Solde à ajouter</label>
                                <input type="number" id="solde" wire:model="solde" class="form-control"
                                    min="1">
                                @error('solde')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:load', function() {
            Livewire.on('open-transaction-history-modal', () => {
                var myModal = new bootstrap.Modal(document.getElementById('transactionHistoryModal'));
                myModal.show();
            });
        });
    </script>



    <script>
        document.addEventListener('livewire:load', function() {
            Livewire.on('openModal', () => {
                var modal = new bootstrap.Modal(document.getElementById('add-solde-modal'));
                modal.show();
            });
        });
    </script>
    <script>
        document.addEventListener('livewire:load', function() {
            Livewire.on('open-transaction-history-modal', () => {
                var myModal = new bootstrap.Modal(document.getElementById('transactionHistoryModal'));
                myModal.show();
            });
        });
    </script>




</div>
