<div>
    <form wire:submit="update">
        <div class="mb-3">
            <label for="">Logo de la marque</label>
            <input type="file" name="logo" wire:model="logo" class="form-control" id="">
            @error('logo')
                <span class="small text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="">Nom</label>
            <input type="text" name="nom" wire:model="nom" class="form-control" id="">
            @error('nom')
                <span class="small text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>

        @include('components.alert')


        <div class="modal-footer">
            <button class="btn btn-sm btn-primary" type="submit">
                <span wire:loading>
                    <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="" srcset="">
                </span>
                Metre a jour
            </button>
        </div>
    </form>
</div>
