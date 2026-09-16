<div>

    @livewireStyles
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
    <form wire:submit="save" class="axil-contact-form">
        <div class="row row--10">
            <div class="col-lg-4">
                <div class="form-group">
                    <label> {{ \App\Helpers\TranslationHelper::TranslateText('Nom') }}*</label>
                    <input wire:model="nom" type="text" id="nom" placeholder="Votre nom">
                    @error('nom')
                        <span class="small text-danger">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label>Email*</label>
                    <input wire:model="email" type="email" id="email" placeholder=" {{ \App\Helpers\TranslationHelper::TranslateText('Votre email') }}">
                    @error('email')
                        <span class="small text-danger">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-lg-4">
                <div class="form-group">
                    <label> {{ \App\Helpers\TranslationHelper::TranslateText('Sujet') }}*</label>
                    <input wire:model="sujet" type="text" id="sujet" placeholder=" {{ \App\Helpers\TranslationHelper::TranslateText('Sujet') }}">
                    @error('sujet')
                        <span class="small text-danger">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label> {{ \App\Helpers\TranslationHelper::TranslateText('Message') }}*</label>
                    <textarea wire:model="message" rows="10" cols="30" id="message" placeholder=" {{ \App\Helpers\TranslationHelper::TranslateText('Votre message') }}"></textarea>
                    @error('message')
                        <span class="small text-danger">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-12">
                <div class="form-group mb--0">


                    <button class="axil-btn btn-bg-primary2" type="submit">
                        <span wire:loading>
                            <img src="/icons/kOnzy.gif" height="20" width="20" alt="" srcset="">
                        </span>
                        <span> {{ \App\Helpers\TranslationHelper::TranslateText('Envoyer') }}</span>
                    </button>
                </div>
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


        </div>
    </form>
</div>
