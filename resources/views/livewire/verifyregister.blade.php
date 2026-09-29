<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6">
            <div class="card">
                <div class="card-body">
                    <div class="app-brand justify-content-center mb-6">
                        <h4 class="mb-1">{{ \App\Helpers\TranslationHelper::TranslateText('Vérification de sécurité') }}</h4>
                    </div>
                    
                    <p class="text-center">
                        {{ \App\Helpers\TranslationHelper::TranslateText('Un code de vérification a été envoyé à votre adresse email.') }}
                    </p>

                    @if (session()->has('error'))
                        <div class="alert alert-danger p-3 small">{{ session('error') }}</div>
                    @endif

                    <form wire:submit.prevent="registerverify">
                        <div class="mb-6 form-group">
                            <label for="code" class="form-label">{{ \App\Helpers\TranslationHelper::TranslateText('Code de vérification') }}</label>
                            <input 
                                type="text" 
                                class="form-control text-center fw-bold" 
                                style="font-size: 24px; letter-spacing: 5px;"
                                wire:model="code" 
                                placeholder="000000" 
                                maxlength="6"
                                autofocus />
                            @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <button  class="axil-btn btn-bg-primary2 submit-btn" type="submit">
                            {{ \App\Helpers\TranslationHelper::TranslateText('Vérifier') }}
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}"  class="axil-btn btn-bg-primary2 submit-btn">
                            {{ \App\Helpers\TranslationHelper::TranslateText('Retour à la connexion') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>