<div class="modal-body">

    <form wire:submit="save">

        @if ($photo)
            <div class="mb-2 preview-banner-upload">
                <img src="{{ $photo->temporaryUrl() }}" class="w-100">
            </div>
        @endif

        <div class="mb-3">
            <label for="">Image de couverture</label>
            <label for="" class="small text-warning">
                ( 960px x 500px )
            </label>
            <input type="file" wire:model="photo" class="form-control @error('photo') is-invalid @enderror" name="photo">
            @error('photo')
                <span class="small text-danger"> {{ $message }} </span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="">Titre principal</label>
            <input type="text" name="titre" wire:model="titre" class="form-control @error('titre') is-invalid @enderror" id="">
            @error('titre')
                <span class="small text-danger"> {{ $message }} </span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="">Sous - titre </label>
            <input type="text" name="sous_titre" wire:model="sous_titre" class="form-control @error('sous_titre') is-invalid @enderror" id="">
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

    </form>

</div>
