<form wire:submit="save" class="singin-form">
    <div class="form-group">
        <label> {{ \App\Helpers\TranslationHelper::TranslateText('Nom') }}</label>
        <input type="text" class="form-control"wire:model="nom" name="nom" placeholder=" {{ \App\Helpers\TranslationHelper::TranslateText('Votre nom') }}">
    </div>

    <div class="form-group">
        <label> {{ \App\Helpers\TranslationHelper::TranslateText('Numéro de téléphone') }}</label>
        <input type="tel" class="form-control" placeholder=" {{ \App\Helpers\TranslationHelper::TranslateText('Votre numéro de téléphone') }}" wire:model="phone" name="phone">
    </div>
    <div class="form-group">
        <label> {{ \App\Helpers\TranslationHelper::TranslateText('Mot de passe') }}</label>
        <input type="password" wire:model="password" id="password1" class="form-control" placeholder=" {{ \App\Helpers\TranslationHelper::TranslateText('Votre mot de passe') }}"
            required />
      {{--   <i class="bi bi-eye-slash" id="togglePassword2" style="cursor: pointer;"></i> --}}
      <span class="oeil">
        <i class="fas fa-eye-slash password-toggleregister"></i>
    </span>

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

    </div>
    <div class="form-group">
        <label> {{ \App\Helpers\TranslationHelper::TranslateText('Confirmationde mot de passe') }}</label>
        <input type="password" class="form-control" wire:model="password_confirmation" placeholder=" {{ \App\Helpers\TranslationHelper::TranslateText('Confirmation de mot de passe') }}"
            aria-describedby="password" required /> <i class="bi bi-eye-slash" id="togglePassword3"></i>

    </div>
    <div class="form-group">
        @if ($errors->any())
            <div class="alert alert-danger small">
                @foreach ($errors->all() as $error)
                    {{ $error }} <br>
                @endforeach
            </div>
        @endif
        <button type="submit" class="axil-btn btn-bg-primary2 submit-btn">

            <span wire:loading>
                <img src="/icons/kOnzy.gif" height="20" width="20" alt="" srcset="">
            </span>
            <span>
                {{ \App\Helpers\TranslationHelper::TranslateText('Confirmation') }}
            </span>
        </button>
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
        background-color: #EFB121; 
        color: #ffffff; 
        border: none;
        padding: 10px 20px; 
        border-radius: 5px; 
        text-decoration: none; 
    }
    </style>
</form>
