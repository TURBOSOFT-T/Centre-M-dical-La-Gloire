<div>

    @include('components.alert')

    @if ($sous_category)
        <form wire:submit="update_sous_category">
        @else
            <form wire:submit="create">
    @endif

    <div class="row">
        <div class="col-sm-8">
            <div class="col-sm-12 mb-3">
                <label for="">Libellé</label>
                <input type="text" name="nom"  class="form-control" wire:model="nom">
                @error('nom')
                    <span class="text-danger small"> {{ $message }} </span>
                @enderror
            </div>
            <div class="col-sm-12 mb-3">
                <label for=""> Categorie </label>
                <select wire:model='categorie_id' class="form-control @error('categorie_id') is-invalid @enderror">
                    <option value=""></option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nom }}</option>
                    @endforeach
                </select>
                @error('categorie_id')
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
            @if ($sous_category)
                Mettre a jour
            @else
                Enregistrer la category
            @endif
        </button>
    </div>
    </form>
</div>
