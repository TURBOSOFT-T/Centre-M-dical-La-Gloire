<div>
    @include('components.alert')

    <form wire:submit="update_user">
       
        <div class="col-lg-12">
            <div class="form-group">
            <label class="form-label" for="RePassword"> {{ \App\Helpers\TranslationHelper::TranslateText('Acien mot de passe') }}</label>
            <input type="password" value=" {{ Auth::user()->old_password }}"placeholder="8 - 15  {{ \App\Helpers\TranslationHelper::TranslateText('Caractères') }}"
                wire:model="old_password" class= "form-control" style="font-size: 18px; color:black">
            @error('old_password')
                <span class="text-danger small"> {{ $message }} </span>
            @enderror
        </div>
        </div>
        <br>
        
        <div class="col-lg-12">
            <div class="form-group">
            <label class="form-label" for="RePassword"> {{ \App\Helpers\TranslationHelper::TranslateText('Nouveau mot de passe') }}</label>
            <input type="password" placeholder="8 - 15  {{ \App\Helpers\TranslationHelper::TranslateText('Caractères') }}" wire:model="password" class= "form-control"
                style="font-size: 18px; color:black">
            @error('password')
                <span class="text-danger small"> {{ $message }} </span>
            @enderror
        </div>
        </div>

        
        <div class="col-lg-12">
            <div class="form-group">
            <label class="form-label" for="RePassword"> {{ \App\Helpers\TranslationHelper::TranslateText('Confirmation') }}</label>
            <input type="password" placeholder="8 - 15  {{ \App\Helpers\TranslationHelper::TranslateText('Caractères') }}" wire:model="password_confirmation"
                class= "form-control" style="font-size: 18px; color:black">
            @error('password_confirmation')
                <span class="text-danger small"> {{ $message }} </span>
            @enderror
        </div>
        </div>
     

     {{--    <div class="col-12">
            <div class="button-group">
                <button type="submit"
                    class="primary-btn3 black-bg  hover-btn5 hover-white"> Confirmer les modification</button>
                
            </div>
        </div> --}}
        <div class="form-group mb--0">
          {{--   <input type="submit" class="axil-btn" value="Confirmer les modification"> --}}
          <button type="submit" class="axil-btn btn-bg-primary2 submit-btn">

            <span wire:loading>
                <img src="/icons/kOnzy.gif" height="20" width="20" alt="" srcset="">
            </span>
            <span>
                {{ \App\Helpers\TranslationHelper::TranslateText('Confirmation') }}
            </span>
        </button>
        </div>
       
    </form>

    <style>
        .btn-bg-primary2 {
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
