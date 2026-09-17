<div>

    @include('components.alert')

    @if ($produit)
    <form wire:submit="update_produit">
    @else
    <form wire:submit="create">
    @endif

        <div class="row">
            <div class="col-sm-8">

         

               

                <div class="mb-3">
                    <label for="nom">Nom du produit</label>
                    <input type="text" id="nom" name="nom" class="form-control @error('nom') is-invalid @enderror" wire:model="nom">
                    @error('nom')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
                 <div class="mb-3" wire:ignore>
                    <label><strong> Petite Description :</strong></label>
                    <textarea
                        rows="3"
                        id="meta_description"
                        name="meta_description"
                         wire:model="meta_description"
                        class="form-control">{{ old('meta_description', $produit->meta_description ?? '') }}</textarea>
                    @error('meta_description')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>


                <div class="mb-3" wire:ignore>
                    <label><strong>Description :</strong></label>
                    <textarea
                        rows="5"
                        id="description"
                         wire:model="description"
                        name="description"
                        class="form-control">{{ old('description', $produit->description ?? '') }}</textarea>
                    @error('description')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <!-- Sélection Catégorie -->
                    <div class="col-sm-6 mb-3">
                        <label for="category_id">Catégorie</label>
                        <select id="category_id" wire:model="category_id" class="form-control @error('category_id') is-invalid @enderror">
                            <option value="">Choisir une catégorie</option>
                            @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nom }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Sélection Marque -->
                    <div class="col-sm-6 mb-3">
                        <label for="marque_id">Marque </label>
                        <select id="marque_id" wire:model='marque_id' class="form-control @error('marque_id') is-invalid @enderror">
                            <option value="">Choisir une marque</option>
                            @foreach ($marques as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->nom }}</option>
                            @endforeach
                        </select>
                        @error('marque_id')
                        <span class="text-danger small"> {{ $message }} </span>
                        @enderror
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label for="prix">Prix de vente</label>
                        <input type="number" step="0.1" id="prix" name="prix" class="form-control @error('prix') is-invalid @enderror" wire:model="prix">
                        @error('prix')
                        <span class="text-danger small"> {{ $message }} </span>
                        @enderror
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label for="prix_achat">Prix d'achat</label>
                        <input type="number" step="0.1" id="prix_achat" name="prix_achat" class="form-control @error('prix_achat') is-invalid @enderror" wire:model="prix_achat">
                        @error('prix_achat')
                        <span class="text-danger small"> {{ $message }} </span>
                        @enderror
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label for="reference">Référence du produit</label>
                        <input type="text" id="reference" name="reference" class="form-control @error('reference') is-invalid @enderror" wire:model="reference">
                        @error('reference')
                        <span class="text-danger small"> {{ $message }} </span>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="col-sm-4">
                <div class="mb-3">
                    <label>Photo d'illustration (300*300)</label>
                    <div class="preview-produit-illustration" onclick="preview_illustration('new-produit')">
                        @if ($produit)
                            @if ($photo2 && is_null($photo))
                            <img src="{{ Storage::url($photo2) }}" alt="" class="w-100">
                            @else
                            <img src="{{ $photo->temporaryUrl() }}" alt="" class="w-100">
                            @endif
                        @else
                            @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" alt="" class="w-100">
                            @else
                            <img src="/icons/no-image.webp" alt="" class="w-100">
                            @endif
                        @endif
                    </div>
                    <input type="file" name="photo" accept="image/*" class="d-none" id="file-input-new-produit" wire:model="photo">
                    @error('photo')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="photos">Autres photos</label>
                    <input type="file" id="photos" multiple name="photos" accept="image/*" class="form-control @error('photos') is-invalid @enderror" wire:model="photos">
                    @error('photos')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>
        </div>

        <div style="text-align: right;" class="mt-4">
            <button class="btn btn-primary btn-sm px-5" type="submit" wire:loading.attr="disabled">
                <span wire:loading>
                    <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="loading">
                </span>
                @if ($produit)
                Mettre à jour
                @else
                Enregistrer le produit
                @endif
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#description'))
        .then(editor => {
            editor.model.document.on('change:data', () => {
                @this.set('description', editor.getData());
            });
        })
        .catch(error => {
            console.error(error);
        });
</script>