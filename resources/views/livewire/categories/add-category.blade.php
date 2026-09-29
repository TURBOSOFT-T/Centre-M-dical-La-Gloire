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
