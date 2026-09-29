<div>
    <form wire:submit="create">
        <div class="modal-body">
            @include('components.alert')

            <div class="row">
                <!-- Nom -->
                <div class="col-sm-6">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" wire:model="nom" id="nom">
                        @error('nom')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Prénom -->
                <div class="col-sm-6">
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" wire:model="prenom" id="prenom">
                        @error('prenom')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Adresse E-mail -->
                <div class="col-sm-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse E-mail *</label>
                        <input type="email" class="form-control" wire:model="email" id="email">
                        @error('email')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Téléphone -->
                <div class="col-sm-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Numéro de téléphone *</label>
                        <input type="tel" class="form-control" wire:model="phone" id="phone">
                        @error('phone')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Sélection du Shop -->
                <div class="col-sm-6">
                    <div class="mb-3">
                        <label for="shop_id" class="form-label">Boutique (Shop) *</label>
                        <select class="form-select" wire:model="shop_id" id="shop_id">
                            <option value="">-- Sélectionner une boutique --</option>
                            @foreach($shops as $shop)
                            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                            @endforeach
                        </select>
                        @error('shop_id')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Sélection du Rôle dans la boutique -->
                <div class="col-sm-6">
                    <div class="mb-3">
                        <label for="role_in_shop" class="form-label">Rôle / Poste *</label>
                        <select class="form-select" wire:model="role_in_shop" id="role_in_shop">
                            <option value="caisse">Seller (Caisse)</option>
                            <option value="medecin">Medecin</option>
                            <option value="secretaire">Sécretaire</option>
                               <option value="comptable">Comptable</option>
                            

                        </select>
                        @error('role_in_shop')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Mot de passe par défaut -->
                <div class="col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">Mot de passe *</label>
                        <div class="form-control d-flex justify-content-between align-items-center">
                            <div>
                                <i class="ri-lock-line"></i> [ Par défaut : ]
                                <b><span id="spanContent">123456789</span></b>
                                <input type="hidden" id="inputContent" value="123456789">
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-secondary" type="button" id="btnCopy">
                                    <i class="ri-file-copy-2-line"></i> Copier
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-sm btn-primary" wire:loading.attr="disabled">
                <span wire:loading class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Enregistrer
            </button>
        </div>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const btnCopy = document.getElementById("btnCopy");
            if (btnCopy) {
                btnCopy.addEventListener("click", function() {
                    var input = document.getElementById("inputContent");
                    navigator.clipboard.writeText(input.value).then(function() {
                        alert("Le mot de passe par défaut a été copié dans le presse-papiers !");
                    });
                });
            }
        });
    </script>
</div>