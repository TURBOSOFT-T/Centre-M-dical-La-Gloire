



<form   wire:submit='connexion' class="singin-form">
    @if (session()->has('error'))
    <div class="alert alert-danger p-3 small">
        {{ session('error') }}
    </div>
@endif
@if (session()->has('success'))
    <div class="alert alert-success p-3 small">
        {{ session('success') }}
    </div>
@endif
    <div class="form-group">
        <label>{{ \App\Helpers\TranslationHelper::TranslateText('Votre numéro de téléphone') }}</label>
        <input type="tel" class="form-control " wire:model="phone" placeholder=" {{ \App\Helpers\TranslationHelper::TranslateText('Votre numéro de téléphone') }}">
        @error('phone')
            <span class="text-danger small">
                {{ $message }}
            </span>
        @enderror
    </div>
    <div class="form-group position-relative">
        <label> {{ \App\Helpers\TranslationHelper::TranslateText('Mot de passe') }}</label>
        <input 
            type="password" 
            id="password1" 
            class="form-control" 
            wire:model="password" 
            placeholder=" {{ \App\Helpers\TranslationHelper::TranslateText('Mot de passe') }}"
        >
        <span class="oeil">
            <i class="fas fa-eye-slash password-toggleregister"></i>
        </span>

        <style>
            .signup-item {
                position: relative;
            }

            .oeil {
                cursor: pointer;
                position: absolute;
                right: 20px;
                top: 20px;
            }
        </style>
     
        
        @error('password')
        <span class="text-danger small">
            {{ $message }}
        </span>
        @enderror
    </div>



<script>
    const passwordField = document.getElementById('password1');
    const toggleButton = document.querySelector('.password-toggleregister');

    toggleButton.addEventListener('click', function() {
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            this.classList.remove('fa-eye-slash');
            this.classList.add('fa-eye');
        } else {
            passwordField.type = 'password';
            this.classList.remove('fa-eye');
            this.classList.add('fa-eye-slash');
        }
    });
</script>
    

 
    <div class="form-group d-flex align-items-center justify-content-between">
        <button type="submit" class="axil-btn btn-bg-primary2 submit-btn">
            <span wire:loading>
                <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="" srcset="">
            </span>
            <i class="ri-git-repository-private-line"></i>
            {{ \App\Helpers\TranslationHelper::TranslateText('Connexion') }}</button>
        <a href="{{ route('forgot_password') }}" class="forgot-btn"> {{ \App\Helpers\TranslationHelper::TranslateText('Mot de passe oublié') }}?</a>
    </div>

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
</form>


