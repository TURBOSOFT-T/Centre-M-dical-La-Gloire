<div>
    @include('components.alert')


    <form wire:submit="update_form">

        <hr class="my-6 mx-n4" />
        <div class="text-center bg-primary card my-auto p-1 mb-3">
            <h6 class="text-white">
                Logos, et Adresses
            </h6>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="">Logo(157*40) </label>
                    <input type="file" wire:model="logo" accept="image/*" class="form-control">
                    @error('logo')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="">Icone(157*40)</label>
                    <input type="file" wire:model="icon" accept="image/*" class="form-control">
                    @error('icon')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="">Logo Footer </label>
                    <input type="file" wire:model="logofooter" accept="image/*" class="form-control">
                    @error('logofooter')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>

            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="">Image Page Connexion(689*1080) </label>
                    <input type="file" wire:model="image_login" accept="image/*" class="form-control">
                    @error('image_login')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="">Image Page Enregistrement (689*1000)</label>
                    <input type="file" wire:model="image_register" accept="image/*" class="form-control">
                    @error('image_register')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>

         



            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="">Email</label>
                    <input type="mail" wire:model="email" step="0.1" class="form-control">
                    @error('email')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="">Telephone</label>
                    <input type="number" wire:model="telephone" step="0.1" class="form-control">
                    @error('telephone')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="">Addresse</label>
                    <input type="text" wire:model="addresse" step="0.1" class="form-control">
                    @error('addresse')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label><strong>Description :</strong></label>
                <textarea class="ckeditor form-control" name="description" wire:model="description"></textarea>
                @error('description')
                <span class="text-danger small"> {{ $message }} </span>
                @enderror
            </div>


            
                <div class="mb-3">
                    <label for="">Message slogan header</label>
                     <textarea type="text" wire:model="slogan" step="0.1" class="form-control"></textarea>
                    @error('slogan')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
           
            <hr class="my-6 mx-n4" />
            <div class="text-center bg-primary card my-auto p-1 mb-3">
                <h6 class="text-white">
                    A propos de nous
                </h6>
            </div>


            <div class="text-center  card my-auto p-1 mb-3">
                <h6 class="text-red">
                    Section 1
                </h6>
            </div>
            <div class="row g-6">
                <div class="col-md-12">
                    <label class="form-label" for="multicol-username">Titre</label>

                    <input type="text" wire:model="titre_apropos" placeholder="Le titre " rows="3"
                        class="form-control">
                    @error('titre_apropos')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label" for="multicol-username">Description </label>

                    <textarea type="text" wire:model="des_apropos" placeholder="La description" rows="3" class="form-control"> </textarea>
                    @error('des_apropos')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="multicol-username">Image(420*501) </label>

                    <input type="file" wire:model="image_apropos" accept="image/*" placeholder="Cargez les images"
                        class="form-control">
                    @error('image_apropos')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
                <br><br>

                <div class="text-center  card my-auto p-1 mb-3">
                    <h6 class="text-red">
                        Section 2
                    </h6>
                </div>

                <div class="col-md-12">
                    <label class="form-label" for="multicol-username">Titre</label>

                    <input type="text" wire:model="titre_apropos1" placeholder="Le titre " rows="3"
                        class="form-control">
                    @error('titre_apropos1')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label" for="multicol-username">Description </label>

                    <textarea type="text" wire:model="des_apropos1" placeholder="La description" rows="3"
                        class="form-control"> </textarea>
                    @error('des_apropos1')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="multicol-username">Image(445*440) </label>

                    <input type="file" wire:model="image_apropos1" accept="image/*"
                        placeholder="Cargez les images" class="form-control">
                    @error('image_apropos1')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>

                <br><br>

                <div class="text-center  card my-auto p-1 mb-3">
                    <h6 class="text-red">
                        Section 3
                    </h6>
                </div>

                <div class="col-md-12">
                    <label class="form-label" for="multicol-username">Titre</label>

                    <input type="text" wire:model="titre_apropos2" placeholder="Le titre " rows="3"
                        class="form-control">
                    @error('titre_apropos2')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label" for="multicol-username">Description </label>

                    <textarea type="text" wire:model="des_apropos2" placeholder="La description" rows="3"
                        class="form-control"> </textarea>
                    @error('des_apropos2')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="multicol-username">Image(445*440) </label>

                    <input type="file" wire:model="image_apropos2" accept="image/*"
                        placeholder="Cargez les images" class="form-control">
                    @error('image_apropos2')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>



            </div>

            <br><br>

            <hr class="my-6 mx-n4" />
            <div class="text-center bg-primary card my-auto p-1 mb-3">
                <h6 class="text-white">
                    Statistiques
                </h6>
            </div>


            <div class="text-center  card my-auto p-1 mb-3">
                <h6 class="text-red">
                    Statistique 1
                </h6>
            </div>
            <div class="row g-6">
                <div class="col-md-2">
                    <label class="form-label" for="multicol-username">Titre</label>

                    <input type="text" wire:model="titre_annee" placeholder="Titre " rows="3"
                        class="form-control">
                    @error('titre_annee')
                    <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
                {{-- <div class="col-md-3">
                    <label class="form-label" for="multicol-username">Nombre</label>

                    <input type="number" wire:model="annee" placeholder="Nombre " rows="3"
                        class="form-control">
                    @error('annee')
                        <span class="text-danger small"> {{ $message }} </span>
                @enderror
            </div>
            --}}
            <div class="col-md-6">
                <label class="form-label" for="multicol-username">Description </label>

                <textarea type="text" wire:model="des_annee" placeholder="La description" rows="3" class="form-control"> </textarea>
                @error('des_annee')
                <span class="text-danger small"> {{ $message }} </span>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="multicol-username">Icone(100*100) </label>

                <input type="file" wire:model="icone_annee" accept="image/*" placeholder="Cargez les images"
                    class="form-control">
                @error('icone_annee')
                <span class="text-danger small"> {{ $message }} </span>
                @enderror
            </div>
            <br><br>

            <div class="text-center  card my-auto p-1 mb-3">
                <h6 class="text-red">
                    Statistique 2
                </h6>
            </div>

            <div class="col-md-2">
                <label class="form-label" for="multicol-username">Titre</label>

                <input type="text" wire:model="titre_satisfaction" placeholder="Nombre " rows="3"
                    class="form-control">
                @error('titre_satisfation')
                <span class="text-danger small"> {{ $message }} </span>
                @enderror
            </div>

            {{--
                <div class="col-md-3">
                    <label class="form-label" for="multicol-username">Nombre</label>

                    <input type="number" wire:model="satisfaction" placeholder="Nombre " rows="3"
                        class="form-control">
                    @error('satisfaction')
                        <span class="text-danger small"> {{ $message }} </span>
            @enderror
        </div> --}}

        <div class="col-md-6">
            <label class="form-label" for="multicol-username">Description </label>

            <textarea type="text" wire:model="des_satisfaction" placeholder="La description" rows="3"
                class="form-control"> </textarea>
            @error('des_satisfaction')
            <span class="text-danger small"> {{ $message }} </span>
            @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label" for="multicol-username">Icone(100*100) </label>

            <input type="file" wire:model="icone_satisfaction" accept="image/*"
                placeholder="Cargez les images" class="form-control">
            @error('icone_satisfaction')
            <span class="text-danger small"> {{ $message }} </span>
            @enderror
        </div>

        <br><br>

        <div class="text-center  card my-auto p-1 mb-3">
            <h6 class="text-red">
                Statistique 3
            </h6>
        </div>

        <div class="col-md-2">
            <label class="form-label" for="multicol-username">Titre</label>

            <input type="text" wire:model="titre_prix" placeholder="Le titre " rows="3"
                class="form-control">
            @error('titre_prix')
            <span class="text-danger small"> {{ $message }} </span>
            @enderror
        </div>

        {{-- <div class="col-md-3">
                    <label class="form-label" for="multicol-username">Nombre</label>

                    <input type="number" wire:model="prix" placeholder="Le nombre " rows="3"
                        class="form-control">
                    @error('prix')
                        <span class="text-danger small"> {{ $message }} </span>
        @enderror
</div> --}}

<div class="col-md-6">
    <label class="form-label" for="multicol-username">Description </label>

    <textarea type="text" wire:model="des_prix" placeholder="La description" rows="3"
        class="form-control"> </textarea>
    @error('des_prix')
    <span class="text-danger small"> {{ $message }} </span>
    @enderror
</div>
<div class="col-md-4">
    <label class="form-label" for="multicol-username">Icone(100*100) </label>

    <input type="file" wire:model="icone_prix" accept="image/*"
        placeholder="Cargez les images" class="form-control">
    @error('icone_prix')
    <span class="text-danger small"> {{ $message }} </span>
    @enderror
</div>



</div>


<br>
<div class="modal-footer">
    <button class="btn btn-primary btn-sm" type="submit">
        <span wire:loading>
            <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="" srcset="">
        </span>
        <i class="ri-save-line me-1 fs-16 lh-1"></i>
        Enregistrer les changements
    </button>
</div>
</form>

</div>