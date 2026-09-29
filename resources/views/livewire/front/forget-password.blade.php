<div>
    @if ($step == 1)
        <form wire:submit="form_1">
            <div class="tp-error-content">
                <h3><i class="fa fa-lock fa-4x" style="font-size:48px;color:rgb(235, 21, 46)"></i></h3>
                <h2 class="text-center">
                    {{ \App\Helpers\TranslationHelper::TranslateText('Mot de passe oublié') }}?
                </h2>
                <p>
                    {{ \App\Helpers\TranslationHelper::TranslateText('Vous pouvez changer votre mot de passe') }}.
                </p>
               
                <div class="form-group mb-6">
                    <div class="tp-contact-input-box p-relative">
                        <!-- Remplacement de type email par text/tel pour le numéro -->
                        <input type="text" class="form-control" id="user_login_phone" name="phone"
                            placeholder="Numéro de téléphone" wire:model="phone" />
                    </div>
                    
                    @error('phone')
                        <span class="small text-danger"> {{ $message }} </span>
                    @enderror
                </div>
                <br>
                <br>
                <button type="submit" class="axil-btn btn-bg-primary2 submit-btn">
                    <span wire:loading>
                        <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="" srcset="">
                    </span>
                    <i class="ri-git-repository-private-line"></i>
                    <span>
                        {{ \App\Helpers\TranslationHelper::TranslateText('Envoyer le code de vérification') }}
                    </span>
                </button>
            </div>
        </form>
    @endif

    @if ($step == 2)
        <div class="text-center">
            <form wire:submit="form_2">
                <img width="100" height="100" src="https://img.icons8.com/glyph-neue/100/578b07/private2.png"
                    alt="private2" />
                <br>
                <h5 class="tp-section-title pb-10">
                    {{ \App\Helpers\TranslationHelper::TranslateText('Code de sécurité') }}!</h5>
                <p>
                    {{ \App\Helpers\TranslationHelper::TranslateText('Veuillez saisir le code de verification que vous avez reçu par SMS') }}.
                </p>
                <div class="form-group mb-6">
                    <div class="tp-contact-input-box p-relative">
                        <input type="text" class="form-control" id="enter_code" name="enter_code"
                            wire:model="enter_code" placeholder="XXXXXX" />
                        <span class="tp-contact-icon">
                            <svg width="16" height="18" viewBox="0 0 16 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.9886 0.666687C10.5459 0.666687 12.6036 2.67901 12.6036 5.16336V6.44114C14.0389 6.88915 15.0846 8.18847 15.0846 9.74036V13.8544C15.0846 15.7757 13.4918 17.3334 11.5282 17.3334H4.4753C2.51077 17.3334 0.917969 15.7757 0.917969 13.8544V9.74036C0.917969 8.18847 1.96459 6.88915 3.39904 6.44114V5.16336C3.40751 2.67901 5.46519 0.666687 7.9886 0.666687ZM7.99707 10.1536C7.59061 10.1536 7.26037 10.4766 7.26037 10.874V12.7125C7.26037 13.1182 7.59061 13.4412 7.99707 13.4412C8.41199 13.4412 8.74224 13.1182 8.74224 12.7125V10.874C8.74224 10.4766 8.41199 10.1536 7.99707 10.1536ZM8.00554 2.11589C6.28657 2.11589 4.88938 3.474 4.88091 5.1468V6.26144H11.1217V5.16336C11.1217 3.48228 9.7245 2.11589 8.00554 2.11589Z"
                                    fill="#578B07" />
                            </svg>
                        </span>
                    </div>
                    @error('enter_code')
                        <span class="small text-danger"> {{ $message }} </span>
                    @enderror
                </div>
                <br>
                <button type="submit" class="axil-btn btn-bg-primary2 submit-btn">
                    <span wire:loading>
                        <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="" srcset="">
                    </span>
                    <i class="ri-git-repository-private-line"></i>
                    <span> {{ \App\Helpers\TranslationHelper::TranslateText('Vérifier') }}</span>
                </button>
            </form>
        </div>
    @endif

    @if ($step == 3)
        <div class="text-center">
            <form wire:submit="form_3">
                <img width="100" height="100" src="https://img.icons8.com/external-vitaliy-gorbachev-lineal-vitaly-gorbachev/100/e6bd00/external-password-internet-security-vitaliy-gorbachev-lineal-vitaly-gorbachev.png" alt="external-password-internet-security-vitaliy-gorbachev-lineal-vitaly-gorbachev"/>
                <br>
                <h5 class="tp-section-title pb-10">
                    {{ \App\Helpers\TranslationHelper::TranslateText('Nouveau mot de passe') }}
                </h5>
                <p>
                    {{ \App\Helpers\TranslationHelper::TranslateText('Veuillez entrer votre nouveau mot de passe') }}!
                </p>
                <div class="form-group mb-6">
                    <div class="tp-contact-input-box p-relative">
                        <input type="password" class="form-control" id="password" name="password"
                            wire:model="password" placeholder="Mot de passe" />
                        <span class="tp-contact-icon">
                            <svg width="16" height="18" viewBox="0 0 16 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.9886 0.666687C10.5459 0.666687 12.6036 2.67901 12.6036 5.16336V6.44114C14.0389 6.88915 15.0846 8.18847 15.0846 9.74036V13.8544C15.0846 15.7757 13.4918 17.3334 11.5282 17.3334H4.4753C2.51077 17.3334 0.917969 15.7757 0.917969 13.8544V9.74036C0.917969 8.18847 1.96459 6.88915 3.39904 6.44114V5.16336C3.40751 2.67901 5.46519 0.666687 7.9886 0.666687ZM7.99707 10.1536C7.59061 10.1536 7.26037 10.4766 7.26037 10.874V12.7125C7.26037 13.1182 7.59061 13.4412 7.99707 13.4412C8.41199 13.4412 8.74224 13.1182 8.74224 12.7125V10.874C8.74224 10.4766 8.41199 10.1536 7.99707 10.1536ZM8.00554 2.11589C6.28657 2.11589 4.88938 3.474 4.88091 5.1468V6.26144H11.1217V5.16336C11.1217 3.48228 9.7245 2.11589 8.00554 2.11589Z"
                                    fill="#578B07" />
                            </svg>
                        </span>
                    </div>
                    @error('password')
                        <span class="small text-danger"> {{ $message }} </span>
                    @enderror

                    <div class="tp-contact-input-box p-relative mt-3">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                            wire:model="password_confirmation" placeholder="Confirmation du mot de passe" />
                        <span class="tp-contact-icon">
                            <svg width="16" height="18" viewBox="0 0 16 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.9886 0.666687C10.5459 0.666687 12.6036 2.67901 12.6036 5.16336V6.44114C14.0389 6.88915 15.0846 8.18847 15.0846 9.74036V13.8544C15.0846 15.7757 13.4918 17.3334 11.5282 17.3334H4.4753C2.51077 17.3334 0.917969 15.7757 0.917969 13.8544V9.74036C0.917969 8.18847 1.96459 6.88915 3.39904 6.44114V5.16336C3.40751 2.67901 5.46519 0.666687 7.9886 0.666687ZM7.99707 10.1536C7.59061 10.1536 7.26037 10.4766 7.26037 10.874V12.7125C7.26037 13.1182 7.59061 13.4412 7.99707 13.4412C8.41199 13.4412 8.74224 13.1182 8.74224 12.7125V10.874C8.74224 10.4766 8.41199 10.1536 7.99707 10.1536ZM8.00554 2.11589C6.28657 2.11589 4.88938 3.474 4.88091 5.1468V6.26144H11.1217V5.16336C11.1217 3.48228 9.7245 2.11589 8.00554 2.11589Z"
                                    fill="#578B07" />
                            </svg>
                        </span>
                    </div>
                    @error('password_confirmation')
                        <span class="small text-danger"> {{ $message }} </span>
                    @enderror
                </div>
                <br>
                <button type="submit" class="axil-btn btn-bg-primary2 submit-btn">
                    <span wire:loading>
                        <img src="https://i.gifer.com/ZKZg.gif" height="15" alt="" srcset="">
                    </span>
                    <i class="ri-git-repository-private-line"></i>
                    <span>
                        {{ \App\Helpers\TranslationHelper::TranslateText('Enregistrer') }}    
                    </span>
                </button>
            </form>
        </div>
    @endif

    <style>
        .btn-bg-primary2 {
            background-color: #5EA13C;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
</div>