<div>

    <div class="d-flex justify-content-end">
        <span wire:loading>
            <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="" srcset="">
        </span>
    </div>
    @include('components.alert')

    <div class="row">
        <div class="col-sm-8 table-responsive-sm">
            <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                <thead class="table-dark">
                    <tr>
                        <th>Titre</th>
                        <th>Valeur(%) </th>
                        <th>Debut</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($promotions as $promotion)
                    <tr>
                        <td> {{ $promotion->titre }} </td>
                        <td> {{ $promotion->pourcentage }} % </td>
                        <td> {{ $promotion->debut }} -> {{ $promotion->fin }} </td>
                        <td> {{ $promotion->statut }} </td>
                        <td>
                            <button class="btn btn-sm btn-danger" wire:confirm="Voulez-vous supprimer ?" wire:click="delete({{ $promotion->id }})">
                                    <i class="ri-delete-bin-6-line"></i>
                                </button>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="alert text-center">
                                    <div>
                                        <img src="/icons/icons8-ticket-100.png" width="100" height="100"
                                            alt="no" srcset="">
                                    </div>
                                    <b>Aucune promotion disponible.</b>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="col-sm-4">
            <form wire:submit="create">
                <h6>
                    <b>Nouvelle Promotion</b>
                </h6>
                <hr>
                <div class="mb-3">
                    <label for="">Titre de la promotion</label>
                    <input type="text" class="form-control" wire:model="titre">
                    @error('titre')
                        <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="">Pourcentage (%) </label>
                    <input type="text" min="2" max="100" class="form-control" wire:model="pourcentage" placeholder="entre 2 et 100">
                    @error('pourcentage')
                        <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
                {{-- <div class="mb-3">
                    <label for="">Description</label>
                    <textarea class="form-control" wire:model="description" rows="3"></textarea>
                    @error('description')
                        <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div> --}}
                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <label for="">Date début</label>
                            <input type="date" class="form-control" wire:model="debut">
                            @error('debut')
                                <span class="text-danger small"> {{ $message }} </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label for="">Date fin</label>
                            <input type="date" class="form-control" wire:model="fin">
                            @error('fin')
                                <span class="text-danger small"> {{ $message }} </span>
                            @enderror
                        </div>
                    </div>
                </div>
                @if ($produit)
                <div class="mb-3 alert p-1 alert-warning">
                    <label for="">Produit selectionner  </label>
                    <div class="text-muted">
                        ( {{ $produit->nom }} )
                    </div>
                </div>
                @endif
                <div class="mb-3">
                    <input type="checkbox" class="form-check-input" wire:model="all">
                    Selectionner tous les produits.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-primary">
                        Enregistrer la promotion.
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
