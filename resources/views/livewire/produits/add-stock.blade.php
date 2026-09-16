<div>
    @include('components.alert')

    <div class="mb-3">
        <label for="search-product" class="form-label">
            Recherche du produit
        </label>
        <input type="text" id="search-product" class="form-control" wire:model.live="produit" placeholder="Nom, Référence du produit">
    </div>

    @if ($produits && !is_null($produits) && !$id)
        <table class="table align-middle">
            @forelse ($produits as $item)
                <tr>
                    <td>
                        <img src="{{ Storage::url($item->photo) }}" width="30" height="30" class="rounded" alt="">
                    </td>
                    <td>
                        {{ $item->nom }}
                    </td>
                    <td style="text-align: right;">
                        <button class="btn btn-sm btn-light" wire:click="copier({{ $item->id }})">
                            <i class="ri-file-copy-2-line small"></i>
                            Sélectionner
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">
                        <div class="text-center text-muted">
                            Aucun produit trouvé !
                        </div>
                    </td>
                </tr>
            @endforelse
        </table>
    @endif

    @if ($id)
        <form wire:submit="add" class="card card-body shadow-sm bg-light mb-3">
            <!-- Sélection de la boutique rattachée -->
            <div class="mb-3">
                <label for="shop_id" class="form-label fw-bold">Choisir la boutique / point de vente</label>
                <select wire:model="shop_id" id="shop_id" required class="form-select">
                    <option value="">-- Sélectionnez un shop --</option>
                    @foreach($shops as $shop)
                        <option value="{{ $shop->id }}">{{ $shop->name }} ({{ $shop->adresse ?? 'Pas d\'adresse' }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Saisie de la quantité -->
            <div class="mb-2">
                <label for="quantite" class="form-label fw-bold">Quantité à ajouter au stock particulier</label>
                <div class="input-group">
                    <input type="number" id="quantite" required min="1" class="form-control" wire:model="quantite" placeholder="Ex: 50">
                    <button class="btn btn-primary" type="submit">
                        <span wire:loading class="me-1">
                            <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="">
                        </span>
                        Valider l'ajout
                    </button>
                </div>
            </div>
        </form>
    @endif 
</div>