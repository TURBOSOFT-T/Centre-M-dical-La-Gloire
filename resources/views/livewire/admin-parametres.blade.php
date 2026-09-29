<div>
    @include('components.alert')

    <form wire:submit="update_user">

        <div class="row">


            <div class="col-sm-6">
                <h5>
                    Informations
                </h5>
                <hr>
                <div class="mb-3">
                    <label class="form-label" for="FullName">
                        Nom
                    </label>
                    <input type="text" wire:model="nom" class="form-control">
                    @error('nom')
                        <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="Email">Email</label>
                    <input type="email" wire:model="email" class="form-control">
                    @error('email')
                        <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="Email">Adresse</label>
                    <input type="text" wire:model="adresse" class="form-control">
                    @error('adresse')
                        <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="Email">Phone</label>
                    <input type="text" wire:model="phone" class="form-control">
                    @error('phone')
                        <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>


            <div class="col-sm-6">
                <h5>
                    Mot de passe
                </h5>
                <hr>
                <div class="mb-3">
                    <label class="form-label" for="RePassword">Old Password</label>
                    <input type="password" placeholder="8 - 15 Characters" wire:model="old_password"
                        class="form-control">
                    @error('old_password')
                        <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="Password">Password</label>
                    <input type="password" placeholder="8 - 15 Characters" wire:model="password" class="form-control">
                    @error('password')
                        <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="RePassword">Re-Password</label>
                    <input type="password" placeholder="8 - 15 Characters" wire:model="password_confirmation"
                        class="form-control">
                    @error('password_confirmation')
                        <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="submit">
                    <span wire:loading>
                        <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="" srcset="">
                    </span>
                    <i class="ri-save-line me-1 fs-16 lh-1"></i>
                    Enregistrer les changements
                </button>
            </div>
        </div>
    </form>
</div>
