@extends('front.fixe')
@section('titre', 'Paiement')
@section('body')
<main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <body class="sticky-header">
        <a href="#top" class="back-to-top" id="backto-top"><i class="fal fa-arrow-up"></i></a>

        <main class="main-wrapper">

            <!-- Start Checkout Area  -->
            <div class="axil-checkout-area axil-section-gap">
                <div class="container">
                    <form action="{{ route('order.confirm') }}" method="post">
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            {!! implode('', $errors->all('<div>:message</div>')) !!}
                        </div>
                        @endif
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">

                                <div class="axil-checkout-billing">
                                    <h4 class="title mb--40">
                                        {{ \App\Helpers\TranslationHelper::TranslateText('Détails factures') }}
                                    </h4>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>{{ \App\Helpers\TranslationHelper::TranslateText('Nom') }} <span>*</span></label>
                                                <input type="text" name="nom"
                                                    value="{{ old('nom', Auth::user()?->nom) }}"
                                                    required />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>
                                                    {{ \App\Helpers\TranslationHelper::TranslateText('Téléphone') }}<span>*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="tel" name="phone" class="form-control" value="{{ old('phone', Auth::user()?->phone) }}" placeholder="6XXXXXXXX" required />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <br>
                                    <div class="form-group">
                                        <label>{{ \App\Helpers\TranslationHelper::TranslateText('Méthode de récupération') }}
                                            <span>*</span></label>
                                        <select name="type_commande" id="type_commande" class="form-control" required>
                                            <option value="">-- Choisir --</option>
                                            <option value="boutique" {{ old('type_commande') == 'boutique' ? 'selected' : '' }}>
                                                {{ \App\Helpers\TranslationHelper::TranslateText('Retrait en Boutique') }}
                                            </option>
                                            <option value="livraison" {{ old('type_commande') == 'livraison' ? 'selected' : '' }}>
                                                {{ \App\Helpers\TranslationHelper::TranslateText('Livraison à une destination') }}
                                            </option>
                                        </select>
                                    </div>


                                    <div class="form-group" id="transport-group" style="display:none;">
                                        <div class="form-group">
                                            <label>{{ \App\Helpers\TranslationHelper::TranslateText('Adresse de livraison') }} <span>*</span></label>
                                            <input type="text" name="adresse" class="form-control mb--15"
                                                placeholder="{{ \App\Helpers\TranslationHelper::TranslateText('Votre adresse') }}"
                                                value="{{ old('adresse', Auth::user()?->adresse) }}" />
                                        </div>
                                    </div>
                                    <br>

                                    <div class="form-group">
                                        <label for="mode_paiement">
                                            {{ \App\Helpers\TranslationHelper::TranslateText('Mode de paiement') }}
                                        </label>
                                        <select name="mode" id="mode_paiement" class="form-control" required>
                                            <option value="">-- Choisir --</option>
                                            <option value="espèce" {{ old('mode') == 'espèce' ? 'selected' : '' }}>
                                                {{ \App\Helpers\TranslationHelper::TranslateText('Payement en espèce') }}
                                            </option>
                                            <!--     <option value="orange money" {{ old('mode') == 'orange money' ? 'selected' : '' }}>
                                                {{ \App\Helpers\TranslationHelper::TranslateText('Orange Money') }}
                                            </option>
                                            <option value="momo" {{ old('mode') == 'momo' ? 'selected' : '' }}>
                                                {{ \App\Helpers\TranslationHelper::TranslateText('MOMO') }}
                                            </option>  -->
                                        </select>
                                    </div>
                                    <p>

                                    <div class="mt-2">
                                        <span class="badge-payement">
                                            🔥 {{ \App\Helpers\TranslationHelper::TranslateText('Les payements par MoMo et Orange Money en cours de développement') }}

                                        </span>
                                    </div>
                                    </p>

                                    <div id="phone-paiement-group" style="display: none; margin-top: 15px;">
                                        <div class="form-group">
                                            <label id="phone-paiement-label">
                                                {{ \App\Helpers\TranslationHelper::TranslateText('Numéro de compte pour le paiement') }} <span>*</span>
                                            </label>
                                            <input type="tel" name="phone_paiement" id="phone_paiement" class="form-control" placeholder="6XXXXXXXX" value="{{ old('phone_paiement') }}" />
                                        </div>

                                        <div class="form-group" style="margin-top: 10px;">
                                            <label>
                                                {{ \App\Helpers\TranslationHelper::TranslateText('Confirmer le numéro de paiement') }} <span>*</span>
                                            </label>
                                            <input type="tel" id="phone_paiement_confirmation" class="form-control" placeholder="6XXXXXXXX" />
                                            <small id="phone-error-msg" style="color: red; display: none; margin-top: 5px;">
                                                {{ \App\Helpers\TranslationHelper::TranslateText('Les numéros de téléphone ne correspondent pas.') }}
                                            </small>
                                        </div>
                                    </div>
                                    <br>

                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="axil-order-summery order-checkout-summery">
                                    <h5 class="title mb--20">
                                        {{ \App\Helpers\TranslationHelper::TranslateText('Votre commande') }}
                                    </h5>
                                    <div class="summery-table-wrap">
                                        <table class="table summery-table">
                                            <thead>
                                                <tr>
                                                    <th>{{ \App\Helpers\TranslationHelper::TranslateText('Produit') }}</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($paniers as $id => $details)
                                                <tr class="order-product">
                                                    <td>{{ $details['nom'] }} <span class="quantity">x {{ $details['quantite'] }}</span></td>
                                                    <td>{{ $details['total'] }} <x-devise></x-devise></td>
                                                </tr>
                                                @endforeach

                                                <tr class="order-subtotal">
                                                    <td>Subtotal</td>
                                                    <td id="subtotal">{{ $total }}</td>
                                                </tr>


                                                <tr>
                                                    <td class="tax">
                                                        {{ \App\Helpers\TranslationHelper::TranslateText('Coupon de réduction') }}
                                                    </td>
                                                    <td>-{{ session('coupon')['value'] ?? 0 }} <x-devise></x-devise></td>
                                                </tr>

                                                <tr class="order-total">
                                                    <td>Total</td>
                                                    <td class="order-total-amount" id="total">{{ $total }} <x-devise></x-devise></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Bouton unique de soumission pour tous les modes -->
                                    <button type="submit" id="submitButton" class="axil-btn btn-bg-primary2 checkout-btn">
                                        {{ \App\Helpers\TranslationHelper::TranslateText('Confirmation') }}
                                    </button>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // 1. Gestion Adresse de livraison
                    const typeCommandeSelect = document.getElementById('type_commande');
                    const transportGroup = document.getElementById('transport-group');

                    function toggleTransport() {
                        if (typeCommandeSelect.value === 'livraison') {
                            transportGroup.style.display = 'block';
                        } else {
                            /*  transportGroup.style.display = 'none';
                             document.getElementById('shipping-cost').textContent = '0'; */
                        }
                    }
                    toggleTransport();
                    typeCommandeSelect.addEventListener('change', toggleTransport);

                    // 2. Gestion Numéro Mobile Money / Orange Money et du texte du bouton
                    const modePaiementSelect = document.getElementById('mode_paiement');
                    const phonePaiementGroup = document.getElementById('phone-paiement-group');
                    const phonePaiementInput = document.getElementById('phone_paiement');
                    const phoneConfirmationInput = document.getElementById('phone_paiement_confirmation');
                    const phonePaiementLabel = document.getElementById('phone-paiement-label');
                    const phoneErrorMsg = document.getElementById('phone-error-msg');
                    const submitButton = document.getElementById('submitButton');
                    const checkoutForm = submitButton.closest('form');

                    function togglePhonePaiement() {
                        const selectedMode = modePaiementSelect.value;

                        if (selectedMode === 'orange money' || selectedMode === 'momo') {
                            phonePaiementGroup.style.display = 'block';
                            phonePaiementInput.setAttribute('required', 'required');
                            phoneConfirmationInput.setAttribute('required', 'required');

                            if (selectedMode === 'orange money') {
                                phonePaiementLabel.innerHTML = "{{ \App\Helpers\TranslationHelper::TranslateText('Numéro Orange Money') }} <span>*</span>";
                                submitButton.style.backgroundColor = '#ff6600'; // Couleur Orange
                                submitButton.style.color = '#ffffff';
                                submitButton.textContent = "{{ \App\Helpers\TranslationHelper::TranslateText('Payer avec Orange') }}";
                            } else {
                                phonePaiementLabel.innerHTML = "{{ \App\Helpers\TranslationHelper::TranslateText('Numéro MTN MoMo') }} <span>*</span>";
                                submitButton.style.backgroundColor = '#ffcc00'; // Couleur MTN
                                submitButton.style.color = '#000000';
                                submitButton.textContent = "{{ \App\Helpers\TranslationHelper::TranslateText('Payer avec MOMO') }}";
                            }

                        } else {
                            phonePaiementGroup.style.display = 'none';
                            phonePaiementInput.removeAttribute('required');
                            phoneConfirmationInput.removeAttribute('required');
                            phonePaiementInput.value = '';
                            phoneConfirmationInput.value = '';
                            phoneErrorMsg.style.display = 'none';

                            submitButton.style.backgroundColor = '#5EA13C'; // Retour couleur par défaut
                            submitButton.style.color = '#ffffff';
                            submitButton.textContent = "{{ \App\Helpers\TranslationHelper::TranslateText('Confirmation') }}";
                        }
                    }

                    togglePhonePaiement();
                    modePaiementSelect.addEventListener('change', togglePhonePaiement);

                    // 3. Validation de correspondance des numéros lors de la soumission
                    checkoutForm.addEventListener('submit', function(e) {
                        const selectedMode = modePaiementSelect.value;
                        if (selectedMode === 'orange money' || selectedMode === 'momo') {
                            if (phonePaiementInput.value !== phoneConfirmationInput.value) {
                                e.preventDefault(); // Bloque l'envoi du formulaire
                                phoneErrorMsg.style.display = 'block';
                                phoneConfirmationInput.focus();
                            } else {
                                phoneErrorMsg.style.display = 'none';
                            }
                        }
                    });
                });
            </script>

            <style>
                .btn-bg-primary2 {
                    background-color: #5EA13C;
                    color: #ffffff;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    text-decoration: none;
                    width: 100%;
                    text-align: center;
                    font-weight: bold;
                    cursor: pointer;
                }
            </style>
        </main>
    </body>
</main>
@endsection