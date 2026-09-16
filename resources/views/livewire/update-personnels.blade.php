<form wire:submit.prevent="update">
    <div class="modal-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nom</label>
                <input type="text" wire:model="nom" class="form-control @error('nom') is-invalid @enderror">
                @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Prénom</label>
                <input type="text" wire:model="prenom" class="form-control @error('prenom') is-invalid @enderror">
                @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Téléphone</label>
                <input type="text" wire:model="phone" class="form-control @error('phone') is-invalid @enderror">
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Boutique</label>
                <select wire:model="shop_id" class="form-select @error('shop_id') is-invalid @enderror">
                    <option value="">Sélectionner une boutique</option>
                    @foreach($shops as $shop)
                        <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                    @endforeach
                </select>
                @error('shop_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Rôle</label>
                <select wire:model="role_in_shop" class="form-select @error('role_in_shop') is-invalid @enderror">
                    <option value="caisse">Caisse</option>
                    <option value="personnel">Personnel</option>
                    <option value="vendeur">Vendeur</option>
                    <option value="gerant">Gérant</option>
                    <option value="commercial">Commercial</option>
                </select>
                @error('role_in_shop') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <div class="modal-footer mt-3">
        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-sm btn-warning">
            <span wire:loading.remove wire:target="update">Enregistrer les modifications</span>
            <span wire:loading wire:target="update">Mise à jour...</span>
        </button>
    </div>
</form>