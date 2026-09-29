<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <div class="tp-blog-title-box text-center mb-60">
                <span class="promo-subtitle">
                    {{ \App\Helpers\TranslationHelper::TranslateText(' 🔥 OFFRES SPÉCIALES') }}
                </span>
                <h4 class="promo-title">
                    {{ \App\Helpers\TranslationHelper::TranslateText('Produits en promotion') }}💥
                </h4>
                <div class="mt-2">
                    <span class="promos-badge">
                        {{ \App\Helpers\TranslationHelper::TranslateText('JUSQU\'À -50%') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Start Expolre Product Area -->
    <div class="axil-product-area bg-color-white axil-section-gapcommon">
        <div class="container">
            <div class="popular-product-activation slick-layout-wrapper slick-layout-wrapper--15 axil-slick-angle angle-top-slide">
                <div class="slick-single-layout">
                    <div class="row">

                        @foreach ($produitsPromo as $key => $produit)
                        <div class="col-md-6 col-12 mb-4">
                            <div class="axil-product product-style-eight product-list-style-3">
                                <div class="thumbnail">
                                    <a href="{{ route('details-produits', ['id' => $produit->id, 'slug' => Str::slug(Str::limit($produit->nom, 10))]) }}">
                                        <img class="main-img product-img" width="300" height="300"
                                             src="{{ Storage::url($produit->photo) }}"
                                             alt="Product Images" loading="lazy" style="object-fit: cover; aspect-ratio: 1/1;">

                                        @if ($produit->new)
                                        <span class="badge-new">NEW</span>
                                        @endif

                                        @if ($produit->vendus_sum_quantite > 0)
                                        <span class="badge-sold">
                                            {{ $produit->vendus_sum_quantite }}
                                            {{ \App\Helpers\TranslationHelper::TranslateText('Vendu(s)') }}
                                        </span>
                                        @endif

                                        @if ($produit->inPromotion())
                                        <span class="promo-badge left">PROMO</span>
                                        <span class="promo-badge right">
                                            -{{ $produit->inPromotion()->pourcentage }}%
                                        </span>
                                        @endif
                                    </a>
                                </div>

                                <div class="product-content">
                                    <div class="col-sm-12 inner">
                                        <div class="top-right">
                                            <br>
                                            @if ($produit->stock > 0)
                                            <label class="badge btn-bg-primary2">
                                                {{ \App\Helpers\TranslationHelper::TranslateText('Stock disponible') }}
                                            </label>
                                            <span class="text-primary text-capitalize">Stock</span> | {{ $produit->stock }} U
                                            @else
                                            <label class="badge bg-danger">
                                                {{ \App\Helpers\TranslationHelper::TranslateText('Stock non disponible') }}
                                            </label>
                                            <span class="text-primary text-capitalize">Stock</span> | {{ $produit->stock }} U
                                            @endif
                                        </div>

                                        <div class="color-variant-wrapper"></div>
                                        <br>
                                        
                                        <h5 class="title">
                                            <a href="{{ route('details-produits', ['id' => $produit->id, 'slug' => Str::slug(Str::limit($produit->nom, 10))]) }}">
                                                {{ \App\Helpers\TranslationHelper::TranslateText(Str::limit($produit->nom, 20)) }}
                                            </a>
                                        </h5>

                                        <div class="product-rating"></div>

                                        <div class="product-price-variant">
                                            <span class="price-text">
                                                {!! \App\Helpers\TranslationHelper::TranslateText('Coût') !!}:
                                            </span>
                                            <span class="price current-price">
                                                <b class="text-succes" style="color: #4169E1">
                                                    {{ $produit->getPrice() }}
                                                </b>
                                            </span>
                                            <span class="price current-price">
                                                <b class="text-succes fs-7" style="color: #4169E1">
                                                    <x-devise></x-devise>
                                                </b>
                                            </span>
                                        </div>

                                        <button class="axil-btn btn-bg-primary2" type="button" onclick="AddToCart({{ $produit->id }})" style="min-height: 48px; display: inline-flex; align-items: center; justify-content: center;">
                                            <span>
                                                {{ \App\Helpers\TranslationHelper::TranslateText('Ajouter au panier') }}
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 text-center mt--20 mt_sm--0">
                        <a href="{{ route('shop', ['highlight' => 'promo']) }}"
                           class="axil-btn btn-bg-primary2 btn-load-more" style="min-height: 48px; display: inline-flex; align-items: center; justify-content: center;">
                            {{ \App\Helpers\TranslationHelper::TranslateText('Voir toutes les promotions') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-bg-primary2 {
        background-color: #5EA13C;
        color: #ffffff;
        border: none;
        padding: 12px 24px;
        min-height: 48px;
        border-radius: 5px;
        text-decoration: none;
    }
    .btn-bg-primary2:hover {
        background-color: #F46B02;
        color: #ffffff;
    }
</style>