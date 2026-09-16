<main class="main-wrapper">
    @php

    $configs = DB::table('configs')->first();
    @endphp

    <!-- Start Cart Area  -->
    <div class="axil-product-cart-area axil-section-gap">
        <div class="container">
            <div class="axil-product-cart-wrap">
                <div class="product-table-heading">
                    <h4 class="title"> {{ \App\Helpers\TranslationHelper::TranslateText('Votre panier') }}</h4>
                    <a href="#" class="cart-clear"></a>
                </div>
                <div wire:poll.10s class="table-responsive">
                    <table class="table axil-product-table axil-cart-table mb--40">
                        <thead>
                            <tr>
                                <th scope="col" class="product-remove"></th>
                                <th scope="col" class="product-thumbnail"> {{ \App\Helpers\TranslationHelper::TranslateText('Produit') }}</th>
                                <th scope="col" class="product-title"></th>
                                <th scope="col" class="product-price"> {{ \App\Helpers\TranslationHelper::TranslateText('Coût') }}</th>
                                <th scope="col" class="product-quantity"> {{ \App\Helpers\TranslationHelper::TranslateText('Quantité') }}</th>
                                <th scope="col" class="product-subtotal">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($paniers ?? [] as $id => $details)
                            <tr data-id="{{ $id }}">
                                <td class="product-remove">
                                    <div class="delete-icon">


                                        <button class=" remove-from-cart  .btn-danger" wire:click="delete({{ $details['id_produit'] }}, {{ $details['shop_id'] }})">

                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" style=" fill:#f80a0a;" height="15" fill="currentColor">
                                                <path d="M6.45455 19L2 22.5V4C2 3.44772 2.44772 3 3 3H21C21.5523 3 22 3.44772 22 4V18C22 18.5523 21.5523 19 21 19H6.45455ZM13.4142 11L15.8891 8.52513L14.4749 7.11091L12 9.58579L9.52513 7.11091L8.11091 8.52513L10.5858 11L8.11091 13.4749L9.52513 14.8891L12 12.4142L14.4749 14.8891L15.8891 13.4749L13.4142 11Z">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="product-thumbnail"><a href="{{ route('details-produits', ['id' => $details['id_produit'], 'slug'=>Str::slug(Str::limit($details['nom'], 10))]) , }}"><img src="{{ Storage::url($details['photo']) }}" alt="Digital Product"></a></td>
                                <td class="product-title">

                                    <div class="product-content">
                                        <h6><a href="#"> {{ $details['nom'] }}</a></h6>
                                    </div>
                                </td>
                                <td class="product-price" data-title="Price"><span class="currency-symbol"></span>
                                    <p class="price">
                                        {{ $details['prix'] }} <x-devise></x-devise>
                                    </p>
                                </td>
                                <td>

                                    <input type="number"
                                        min="1"
                                        max="{{ \App\Models\ProductShop::where('produit_id', $details['id_produit'])->where('shop_id', $details['shop_id'])->first()->stock_particulier ?? 0 }}"
                                        value="{{ $details['quantite'] }}"
                                        wire:change.lazy="update({{ $details['id_produit'] }}, {{ $details['shop_id'] }}, $event.target.value)">
                                    <!-- <input type="number" value="{{ $details['quantite'] }}" min="1" 
                                
                                       wire:change="update({{ $details['id_produit'] }}, {{ $details['shop_id'] }}, $event.target.value)" 
                                       class="quantity-input"> -->
                                </td>
                                <td class="product-subtotal" data-title="Subtotal"><span class="currency-symbol"></span> {{ $details['prix'] * $details['quantite'] }}
                                    <x-devise></x-devise>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">
                                    <div class="text-center p-5">
                                        <img width="64" height="64" src="https://img.icons8.com/pastel-glyph/64/578b07/shopping-cart--v2.png" alt="shopping-cart--v2" /><br>

                                        <h6>

                                            {{ \App\Helpers\TranslationHelper::TranslateText('Vous n\'avez pas de produits au panier') }}
                                        </h6>
                                        <br>

                                    </div>
                                </td>
                            </tr>
                            @endforelse



                        </tbody>
                    </table>
                </div>
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif
                <div class="cart-update-btn-area">

                    <form action="{{ route('apply.coupon') }}" method="POST">
                        @csrf

                        <div class="input-group product-cupon">

                            <input type="text" name="code" placeholder="Entrez le code du coupon">
                            <div class="product-cupon-btn">
                                <button type="submit" class="axil-btn1  btn-bg-primary1 btn-outline1"> {{ \App\Helpers\TranslationHelper::TranslateText('Appliquer') }}</button>
                                <style>
                                    .axil-btn1 {
                                        padding: 10px 20px;
                                        font-size: 16px;
                                        border-radius: 5px;
                                        cursor: pointer;
                                        transition: background-color 0.3s, color 0.3s;
                                    }

                                    .btn-bg-primary1 {
                                        background-color: #5EA13C;
                                        /* Example primary color */
                                        color: #fff;
                                        border: none;
                                    }

                                    .btn-outline1 {
                                        background-color: transparent;
                                        color: #5EA13C;
                                        border: 2px solid #5EA13C;
                                    }

                                    .axil-btn1.btn-bg-primary1.btn-outline1:hover {
                                        background-color: #5EA13C;
                                        /* Darker shade on hover */
                                        color: #fff;
                                    }
                                </style>


                            </div>
                        </div>
                    </form>
                    <div class="update-btn">
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-5 col-lg-7 offset-xl-7 offset-lg-5">
                        <div class="axil-order-summery mt--80">
                            <h5 class="title mb--20"> {{ \App\Helpers\TranslationHelper::TranslateText('Résummé de la commande') }}</h5>
                            <div class="summery-table-wrap">
                                @if ($total > 0)
                                <table class="table summery-table mb--30">
                                    <tbody>
                                        <tr class="order-subtotal">
                                            <td>Subtotal</td>
                                            <td>{{ $total }} <x-devise></x-devise></td>
                                        </tr>


                                        <tr class="order-total">
                                            <td>Total</td>
                                            <td class="order-total-amount">{{ $total  }} <x-devise></x-devise></td>
                                        </tr>
                                    </tbody>
                                </table>
                                @endif
                            </div>
                            @if ($total > 0)
                            <a class="axil-btn btn-bg-primary2 checkout-btn" href="{{ url('/commander') }}"> {{ \App\Helpers\TranslationHelper::TranslateText('Passer le commande') }}</a>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Cart Area  -->

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
            /* Couleur de fond, bleu dans cet exemple */
            color: #ffffff;
            /* Couleur du texte, blanc dans cet exemple */
            border: none;
            padding: 10px 20px;
            /* Optionnel, ajuste la taille */
            border-radius: 5px;
            /* Optionnel, arrondit les coins */
            text-decoration: none;
            /* Supprime le soulignement */
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.update-quantity').forEach(select => {
                select.addEventListener('change', function() {
                    const quantity = this.value;
                    const productId = this.getAttribute('data-product-id');

                    fetch(`/update-quantity/${productId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                quantity
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Met à jour le total ou affiche un message de succès
                                console.log('Quantité mise à jour avec succès!');
                                location.reload(); // Optionnel : pour recharger les totaux ou autres parties du panier
                            } else {
                                console.error('Erreur lors de la mise à jour de la quantité');
                            }
                        })
                        .catch(error => console.error('Erreur:', error));
                });
            });
        });
    </script>

    <script>
        // Écoute l'événement envoyé par le composant Livewire
        window.addEventListener('panierUpdated', event => {
            // Cette fonction actualise vos éléments du header (compteur, prix, etc.)
            get_panier_header();
        });

        function get_panier_header() {
            $.get("/client/count_panier", function(data) {
                // Met à jour uniquement le header, pas besoin de toucher au panier Livewire
                $("#count-panier-span").text(data.total);
                $("#list_content_panier").html(data.html);
                $("#montant_total_panier").html(data.montant_total + " XCFA");
            });
        }

        // 1. Écoute l'événement de mise à jour pour rafraîchir le header
        window.addEventListener('panierUpdated', event => {
            get_panier(); // Votre fonction existante qui met à jour le compteur/total
        });

        // 2. Écoute l'événement d'alerte pour afficher le SweetAlert
        window.addEventListener('alert', event => {
            const data = event.detail[0] || event.detail;
            sweet_alert(
                "Attention !",
                data.type || 'warning',
                data.message,
                3000
            );
        });

        window.addEventListener('alert', event => {
            // Si vous êtes en Livewire v3, utilisez event.detail
            const data = event.detail[0] || event.detail;
            sweet_alert("Attention", data.type, data.message, 3000);
        });
    </script>
</main>