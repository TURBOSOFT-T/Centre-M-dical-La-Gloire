<div>
    <form wire:submit="save">

        {{-- Nom de l'examen général --}}
        <div class="mb-3">
            <label for="nom" class="form-label font-weight-bold">Nom du groupe d'examen <span class="text-danger">*</span></label>
            <input type="text" wire:model="nom" class="form-control @error('nom') is-invalid @enderror" id="nom" placeholder="Ex: Hémogramme (NFS), Bilan Parasitologique...">
            @error('nom')
            <span class="small text-danger d-block mt-1">
                {{ $message }}
            </span>
            @enderror
        </div>

        <div class="mb-3">
    <label class="form-label font-weight-bold">Type d'Examen <span class="text-danger">*</span></label>
    <select wire:model="type" class="form-select @error('type') is-invalid @enderror">
        <option value="">-- Sélectionnez le type --</option>
        <option value="biologie">Biologie</option>
        <option value="imagerie">Imagerie</option>
        
    </select>
    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

        {{-- Liste dynamique des sous-analyses & tarifs --}}
        <div class="mb-3 p-3 border rounded bg-light">
            <label class="form-label font-weight-bold mb-2">Sous-analyses & Tarifs</label>
            
            @foreach($caracteristiques as $index => $caracteristique)
            <div class="row g-2 align-items-center mb-2" wire:key="carac-{{ $index }}">
                {{-- Nom de la sous-analyse --}}
                <div class="col-md-4">
                    <input type="text" 
                           class="form-control @error("caracteristiques.{$index}.nom") is-invalid @enderror" 
                           placeholder="Ex: ECBU, Taux d'Hémoglobine..." 
                           wire:model="caracteristiques.{{ $index }}.nom">
                    @error("caracteristiques.{$index}.nom")
                    <span class="small text-danger d-block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Prix / Tarif --}}
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="number" 
                               class="form-control @error("caracteristiques.{$index}.prix") is-invalid @enderror" 
                               placeholder="Ex: 3000" 
                               wire:model="caracteristiques.{{ $index }}.prix">
                        <span class="input-group-text bg-white">FCFA</span>
                    </div>
                    @error("caracteristiques.{$index}.prix")
                    <span class="small text-danger d-block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Bouton Supprimer --}}
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-outline-danger w-100" wire:click="removeCaracteristique({{ $index }})" title="Supprimer">
                        <i class="bx bx-trash me-1"></i>
                    </button>
                </div>
            </div>
            @endforeach

            <div class="mt-3">
                 @can('examen_add')
                <button type="button" class="btn btn-outline-primary radius-30" wire:click="addCaracteristique">
                    <i class="bx bx-plus me-1"></i> Ajouter une analyse
                </button>
                @endcan
            </div>
        </div>

        @include('components.alert')

        {{-- Pied du Formulaire --}}
        <div class="modal-footer px-0 pb-0 pt-3 border-top">
             @can('examen_add')
            <button class="btn btn-primary px-4 radius-30" type="submit" wire:loading.attr="disabled">
                <span wire:loading class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                <i class="bx bx-save me-1" wire:loading.remove></i>
                Enregistrer
            </button>
            @endcan
        </div>
    </form>
</div>