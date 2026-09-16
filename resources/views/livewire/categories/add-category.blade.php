<div>

    @include('components.alert')

    @if ($category)
        <form wire:submit="update_category">
        @else
            <form wire:submit="create">
    @endif

    <div class="row">
        <div class="col-sm-8">
            <div class="mb-3">
                <label for="">Nom</label>
                <input type="text" name="nom" class="form-control" wire:model="nom">
                @error('nom')
                    <span class="text-danger small"> {{ $message }} </span>
                @enderror
            </div>

         
            
        </div>
        <div class="col-sm-4">
            <div class="mb-3">
                <label for="">Photo d'illustration ((300*300))</label>
                <div class="preview-produit-illustration" onclick="preview_illustration('new-prosduit')">
                    @if ($category)
                        @if ($photo2 && is_null($photo))
                            <img src="{{ Storage::url($photo2) }}" alt="" class="w-100">
                        @else
                            <img src="{{ $photo->temporaryUrl() }}" alt="" srcset="">
                        @endif
                    @else
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" alt="" class="w-100">
                        @else
                            <img src="/icons/no-image.webp" alt="" class="w-100">
                        @endif
                    @endif
                </div>
                <input type="file" name="photo" accept="image/*" class=" d-none" id="file-input-new-prosduit"
                    wire:model="photo">
                @error('photo')
                    <span class="text-danger small"> {{ $message }} </span>
                @enderror
            </div>
        
        </div>
    </div>
    <div style="text-align: right;">
        <button class="btn btn-primary btn-sm px-5" type="submit" wire:loading.attr="disabled">
            <span wire:loading>
                <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="" srcset="">
            </span>
            @if ($category)
                Mettre a jour
            @else
                Enregistrer la category
            @endif
        </button>
    </div>
    </form>
</div>
