<!-- Product Quick View Modal Start -->
<div class="modal fade quick-view-product" id="{{ $produit->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="far fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="single-product-thumb">
                    <div class="row">
                        <!-- Image du Produit -->
                        <div class="col-lg-7 mb--40">
                            <div class="shop-details-img">
                                <div class="tab-content" id="v-pills-tabContent">
                                    <div class="shop-details-tab-img product-img--main" id="zoomContaine" data-scale="1.4" style="overflow: hidden; position: relative;">
                                        @if ($produit->inPromotion())
                                            <span class="promo-badge">PROMO</span>
                                            <span class="promo-badge right">-{{ $produit->inPromotion()->pourcentage }}%</span>
                                        @endif

                                        <img id="mainImage" src="{{ Storage::url($produit->photo) }}" height="600" width="700" 
                                             alt="{{ $produit->nom }}" 
                                             style="transition: transform 0.3s ease; object-fit: cover; aspect-ratio: 1/1; width: 100%;" 
                                             loading="lazy" />

                                        @if ($produit->new)
                                            <span class="badge-new">NEW</span>
                                        @endif
                                        @if (($produit->vendus_sum_quantite ?? 0) > 0)
                                            <span class="badge-sold">
                                                {{ $produit->vendus_sum_quantite }}
                                                {{ \App\Helpers\TranslationHelper::TranslateText('Vendu(s)') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Détails & Actions -->
                        <div class="col-lg-5 mb--40">
                            <div class="single-product-content">
                                <div class="inner">
                                    <h3 class="product-title">
                                        {{ \App\Helpers\TranslationHelper::TranslateText($produit->nom) }}
                                        @if(isset($produit->vues_count) && $produit->vues_count > 0)
                                            <br>
                                            <span class="badge-vie" style="font-size: 0.8rem;">
                                                👁️ {{ number_format($produit->vues_count) }} {{ \App\Helpers\TranslationHelper::TranslateText('vues') }}
                                            </span>
                                        @endif
                                    </h3>

                                    <div class="price-amount mb-3">
                                        @if ($produit->inPromotion())
                                            <b class="text-success" style="color: #4169E1 !important; font-size: 1.5rem;">
                                                {{ $produit->getPrice() }} <x-devise></x-devise>
                                            </b>
                                            <span style="position: relative; font-size: 1.3rem; color: #dc3545; font-weight: bold; margin-left: 10px;">
                                                {{ $produit->prix }} <x-devise></x-devise>
                                                <span style="position: absolute; top: 50%; left: 0; width: 100%; height: 2px; background-color: black;"></span>
                                            </span>
                                        @else
                                            <span style="font-size: 1.5rem; font-weight: bold;">
                                                {{ $produit->getPrice() }} <x-devise></x-devise>
                                            </span>
                                        @endif
                                    </div>

                                    <ul class="product-meta list-unstyled mb-3">
                                        <li class="mb-2">
                                            @if ($produit->stock > 0)
                                                <span class="badge btn-bg-primary2">{{ \App\Helpers\TranslationHelper::TranslateText('Stock disponible') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ \App\Helpers\TranslationHelper::TranslateText('Stock non disponible') }}</span>
                                            @endif
                                        </li>
                                        <li>
                                            {{ \App\Helpers\TranslationHelper::TranslateText('Catégorie') }}: 
                                            <span class="fw-bold">{{ $produit->categories->nom ?? '' }}</span>
                                        </li>
                                    </ul>

                                    <p class="description">{!! $produit->meta_description !!}</p>

                                    <!-- Block Actions Produit -->
                                    <div class="product-action-wrapper d-flex flex-wrap align-items-center gap-3 mt-4">
                                        <!-- Sélecteur Quantité -->
                                        <div class="pro-qty d-flex align-items-center">
                                            <input type="number" class="input-text qty text form-control text-center" name="quantite" min="1" value="1" id="qte-{{ $produit->id }}" autocomplete="off" style="width: 70px;">
                                        </div>

                                        <!-- Génération dynamique du lien WhatsApp si non fourni -->
                                        @php
                                            if (!isset($whatsappUrl)) {
                                                $config = DB::table('configs')->first();
                                                $rawPhone = $config->whatsapp ?? $config->telephone ?? '237600000000';
                                                $whatsappNumber = preg_replace('/[^0-9]/', '', $rawPhone);
                                                $productUrl = route('details-produits', ['id' => $produit->id, 'slug' => \Illuminate\Support\Str::slug($produit->nom)]);
                                                $whatsappMessage = " Bonjour *SWOOT BIO*, je souhaite commander le produit : " . $produit->nom . " (" . $produit->getPrice() . " FCFA).\nLien : " . $productUrl;
                                                $whatsappUrlModal = "https://wa.me/{$whatsappNumber}?text=" . urlencode($whatsappMessage);
                                            } else {
                                                $whatsappUrlModal = $whatsappUrl;
                                            }
                                        @endphp

                                        <!-- Boutons Responsive -->
                                        <ul class="product-action-container">
                                            <!-- Ajouter au panier -->
                                            <li>
                                                <a onclick="AddToCart({{ $produit->id }})" class="btn-cart">
                                                    {{ \App\Helpers\TranslationHelper::TranslateText('Ajouter au panier') }}
                                                </a>
                                            </li>

                                            <!-- Commander sur WhatsApp -->
                                            <li>
                                                <a href="{{ $whatsappUrlModal }}" target="_blank" rel="noopener noreferrer" class="btn-whatsapp-action">
                                                    <i class="ri-whatsapp-line fs-5"></i>
                                                    <span>{{ \App\Helpers\TranslationHelper::TranslateText('Commander sur WhatsApp') }}</span>
                                                </a>
                                            </li>

                                            <!-- Favoris -->
                                            @auth
                                                <li class="wishlist">
                                                    <a onclick="AddFavoris({{ $produit->id }})" class="axil-btn wishlist-btn btn btn-outline-secondary">
                                                        <i class="far fa-heart"></i>
                                                    </a>
                                                </li>
                                            @endauth
                                        </ul>
                                    </div>

                                    
                                    <!-- End Product Action Wrapper -->

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .product-action-container {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 0;
        padding-left: 0;
        list-style: none;
        width: auto;
    }

    .product-action-container > li {
        display: flex;
        align-items: center;
    }

    .btn-cart {
        background-color: #5EA13C;
        color: #ffffff !important;
        border: none;
        padding: 10px 18px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
    }

    .btn-whatsapp-action {
        background-color: #25D366;
        color: #ffffff !important;
        border: none;
        padding: 10px 18px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-weight: 500;
        font-size: 14px;
        white-space: nowrap;
        transition: background-color 0.2s ease;
    }

    .btn-whatsapp-action:hover {
        background-color: #1EBE5D;
        color: #ffffff !important;
    }

    @media (max-width: 575.98px) {
        .product-action-container {
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }

        .product-action-container > li {
            width: 100%;
        }

        .btn-cart,
        .btn-whatsapp-action {
            width: 100%;
            text-align: center;
            padding: 12px 15px;
        }

        .product-action-container > li.wishlist {
            justify-content: center;
        }
    }
</style>