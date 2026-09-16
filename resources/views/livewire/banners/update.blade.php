<div>
    <form wire:submit="update">
        <div class="row">
            <div class="col-sm-8">
                <img src="{{ Storage::url($image) }}" alt="{{ $titre }}" class="w-100" srcset="">
            </div>
            <div class="col-sm-4">
                <div class="mb-3">
                    <label for="">Nouvelle photo</label>
                    <input type="file" wire:model="photo" class="form-control">
                    @error('photo')
                        <span class="small text-danger"> {{ $message }} </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="">Titre</label>
                    <input type="text" class="form-control" wire:model="titre">
                    @error('titre')
                        <span class="small text-danger"> {{ $message }} </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="">Sous Titre</label>
                    <input type="text" class="form-control" wire:model="sous_titre">
                    @error('sous_titre')
                        <span class="small text-danger"> {{ $message }} </span>
                    @enderror
                </div>

                @include('components.alert')

                <div class="modal-footer">
                    <button class="btn btn-primary btn-sm px-5" type="submit" wire:loading.attr="disabled">
                        <span wire:loading>
                            <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="" srcset="">
                        </span>
                        Enregistrer
                    </button>
                </div>
                
            </div>
        </div>
    </form>
</div>
