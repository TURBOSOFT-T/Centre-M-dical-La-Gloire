<div class="axil-dashboard-account">
    @include('components.alert')

    <form wire:submit="update_user" class="account-details-form">


        <div class="row">

            <div class="col-lg-6">
                <div class="form-group">
                    <label class="small mb-1" for="nom"> {{ \App\Helpers\TranslationHelper::TranslateText('Nom') }}</label>
                    <input wire:model="nom" type="text" {{ Auth::user()->nom }} class= "form-control"
                        style="font-size: 18px; color:black">
                    @error('nom')
                        <span class="small text-danger">
                            {{ $message }}
                        </span>
                    @enderror

                </div>
            </div>


            <div class="col-lg-6">
                <div class="form-group">
                    <label class="small mb-1" for="nom"> {{ \App\Helpers\TranslationHelper::TranslateText('Prénom') }}</label>
                    <input wire:model="prenom" type="text" {{ Auth::user()->prenom }} class= "form-control"
                        style="font-size: 18px; color:black">
                    @error('prenom')
                        <span class="small text-danger">
                            {{ $message }}
                        </span>
                    @enderror

                </div>
            </div>

        </div>
        

        <div class="row ">

            <div class="col-lg-6">
                <div class="form-group">
                    <label class="small mb-1" for="inputOrgName">Email</label>

                    <input type="text" value=" {{ Auth::user()->email }}" wire:model="email" class= "form-control"
                        style="font-size: 18px; color:black">
                    @error('email')
                        <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    <label class="small mb-1" for="inputLocation"> {{ \App\Helpers\TranslationHelper::TranslateText('Téléphone') }}</label>

                    <input value=" {{ Auth::user()->phone }}" wire:model="phone" id="inputLocation" type="text"
                        class= "form-control" style="font-size: 18px; color:black">
                    @error('phone')
                        <span class="text-danger small"> {{ $message }} </span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">

            <label class="small mb-1" for="adresse"> {{ \App\Helpers\TranslationHelper::TranslateText('Adresse') }}</label>
            <input type="text" value=" {{ Auth::user()->adresse }}" wire:model="adresse"
                style="font-size: 18px; color:black" class= "form-control">
            @error('adresse')
                <span class="text-danger small"> {{ $message }} </span>
            @enderror
        </div>
        </div>
   
        <div class="form-group mb--0">
          {{--   <input type="submit" class="axil-btn2 " value="Confirmer les changements"> --}}
            <button type="submit" class="axil-btn btn-bg-primary2 submit-btn">

                <span wire:loading>
                    <img src="/icons/kOnzy.gif" height="20" width="20" alt="" srcset="">
                </span>
                <span>
                    {{ \App\Helpers\TranslationHelper::TranslateText('Confirmer les changements') }}
                </span>
            </button>
        </div>
    </form>

    <style>
        .axil-btn2 {
            background-color: #5EA13C;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-bg-secondary2 {
        background-color: #EFB121; /* Couleur de fond, bleu dans cet exemple */
        color: #ffffff; /* Couleur du texte, blanc dans cet exemple */
        border: none;
        padding: 10px 20px; /* Optionnel, ajuste la taille */
        border-radius: 5px; /* Optionnel, arrondit les coins */
        text-decoration: none; /* Supprime le soulignement */
    }
    </style>
</div>

{{-- 
<div class="axil-dashboard-account">
    <form class="account-details-form">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" class="form-control" value="Annie">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" class="form-control" value="Mario">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group mb--40">
                    <label>Country/ Region</label>
                    <select class="select2">
                        <option value="1">United Kindom (UK)</option>
                        <option value="1">United States (USA)</option>
                        <option value="1">United Arab Emirates (UAE)</option>
                        <option value="1">Australia</option>
                    </select>
                    <p class="b3 mt--10">This will be how your name will be displayed in the account section and in reviews</p>
                </div>
            </div>
            <div class="col-12">
                <h5 class="title">Password Change</h5>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" value="123456789101112131415">
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" class="form-control">
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" class="form-control">
                </div>
                <div class="form-group mb--0">
                    <input type="submit" class="axil-btn" value="Save Changes">
                </div>
            </div>
        </div>
    </form>
</div> --}}
