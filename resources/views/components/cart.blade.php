<div>
    @foreach ($produits as $produit)
        <li class="cart-item" id="item-{{ $produit['id_produit'] }}-{{ $produit['shop_id'] }}">
            <div class="item-img">
                <a href="#"><img src="{{ $produit['photo'] }}" alt="{{ $produit['nom'] }}"></a>
                
                {{-- Bouton suppression avec shop_id --}}
                <button onclick="DeleteToCart({{ $produit['id_produit'] }}, {{ $produit['shop_id'] }})" class="close-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="15" style="fill:red" height="15" fill="currentColor">
                        <path d="M6.45455 19L2 22.5V4C2 3.44772 2.44772 3 3 3H21C21.5523 3 22 3.44772 22 4V18C22 18.5523 21.5523 19 21 19H6.45455ZM13.4142 11L15.8891 8.52513L14.4749 7.11091L12 9.58579L9.52513 7.11091L8.11091 8.52513L10.5858 11L8.11091 13.4749L9.52513 14.8891L12 12.4142L14.4749 14.8891L15.8891 13.4749L13.4142 11Z"></path>
                    </svg>
                </button>
            </div>
            
            <div class="item-content">
                <h3 class="item-title">{{ Str::limit($produit['nom'], 15) }}</h3>
                <div class="item-price">{{ $produit['prix'] }} <x-devise></x-devise></div>
                
                {{-- Conteneur avec les données de contexte --}}
                <div class="pro-qty item-quantity" data-id="{{ $produit['id_produit'] }}" data-shop="{{ $produit['shop_id'] }}">
                    <span class="quantity-control minus"></span>
                    <input type="number" value="{{ $produit['quantite'] }}" class="quantity-input" readonly>
                    <span class="quantity-control plus"></span>
                </div>
            </div>
        </li>
    @endforeach

    <script>
        // Logique commune pour plus (+) et moins (-)
        $(document).on('click', '.quantity-control', function () {
            var parent = $(this).closest('.pro-qty');
            var input = parent.find('.quantity-input');
            var id = parent.data('id');
            var shopId = parent.data('shop');
            var currentVal = parseInt(input.val());
            var newVal = $(this).hasClass('plus') ? currentVal + 1 : currentVal - 1;

            if (newVal >= 0) {
                // Appel Livewire
                Livewire.dispatch('update-cart-qty', { id: id, shopId: shopId, qty: newVal });
                input.val(newVal);
            }
        });

        // Fonction de suppression (AJAX classique)
        function DeleteToCart(id, shopId) {
            $.post("/client/delete_produit_au_panier", {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id_produit: id,
                shop_id: shopId
            }, function(data) {
                if (data.statut) {
                    location.reload(); // Ou appeler get_panier()
                }
            });
        }
    </script>
</div>