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