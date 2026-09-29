<div>

    <form wire:submit="filtrer">
        <div class="row">
            <div class="col-sm-6">
                <span>
                    <b>{{ $produits->count() }}</b> Résultats sur {{ $total }}.
                </span>
            </div>
            <div class="col-sm-6">
                <div class="input-group mb-3">
                    <input type="text" class="form-control btn-sm" wire:model="key"
                        placeholder="Titre,Description des articles">
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
                    <th>Photo</th>
                    <th>Nom</th>
                    <th>Supprimé le </th>
                    <th style="text-align: right;">
                        <span wire:loading>
                            <img src="https://i.gifer.com/ZKZg.gif" width="20" height="20" class="rounded shadow"
                                alt="">
                        </span>
                    </th>
                </tr>
            </thead>


            <tbody>
                @forelse ($produits as $produit)
                    <tr>
                        <td>
                            <img src="{{ Storage::url($produit->photo) }}" width="40 " height="40 "
                                class="rounded shadow" alt="">
                        </td>
                        <td>
                            {{ $produit->nom }}
                        </td>
                        <td>{{ $produit->deleted_at->format('d/m/Y') }} </td>
                        <td style="text-align: right;">
                            <button class="btn btn-sm btn-success" type="button"
                                wire:click="restore({{ $produit->id }})">
                                <i class="bi bi-check-circle"></i>
                                Restorer
                            </button>
                            <button class="btn btn-sm btn-danger" type="button"
                                wire:click="delete_definitif({{ $produit->id }})">
                                <i class="bi bi-check-circle"></i>
                                Supprimer définitivement
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">
                            <div>
                                <img src="/icons/icons8-ticket-100.png" height="100" width="100" alt=""
                                    srcset="">
                            </div>
                            Aucun produit trouvé dans la corbeille.
                        </td>
                    </tr>
                @endforelse

            </tbody>


        </table>
    </div>
    {{ $produits->links('pagination::bootstrap-4') }}


</div>
