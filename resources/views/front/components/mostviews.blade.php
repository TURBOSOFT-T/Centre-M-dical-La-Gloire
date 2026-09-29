<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <div class="tp-blog-title-box text-center mb-60">

                <span class="tp-section-subtitle">
                    👁️ {{ __('Produits les plus consultés') }}
                </span>

                <div class="mt-2">
                    <span class="badge-new-title">
                        🔥 {{ __('Les produits les plus populaires du moment') }}
                    </span>
                </div>

                <div class="mt-2">
                    <span class="badge bg-dark text-white px-3 py-2">
                        📊 {{ __('Basé sur les vues des visiteurs') }}
                    </span>
                </div>

            </div>
        </div>
    </div>
<br><br>
      <div class="axil-best-seller-product-area bg-color-white axil-section-gap pb--0">
         <div class="container">
             <div class="product-area pb--50">



                 <div class="new-arrivals-product-activation-2 slick-layout-wrapper--15 axil-slick-arrow arrow-top-slide product-slide-mobile">

                    @foreach ($mostViewed as $produit)
                    <div class="slick-single-layout">
                        <div class="axil-product product-style-four">

                            <!-- THUMBNAIL -->
                            <div class="thumbnail" style="position:relative;">

                                <a href="{{ route('details-produits', ['id' => $produit->id, 'slug' => Str::slug($produit->nom)]) }}">

                                    <img class="main-img product-img"
                                        src="{{ Storage::url($produit->photo) }}"
                                        alt="{{ $produit->nom }}" loading="lazy" loading="lazy" style="object-fit: cover; aspect-ratio: 1/1;">

                                    <img class="hover-img product-img"
                                        src="{{ Storage::url($produit->photo) }}"
                                        alt="{{ $produit->nom }}" loading="lazy" style="object-fit: cover; aspect-ratio: 1/1;">
                                </a>

                                <!-- VIEWS BADGE -->
                                @if($produit->vues_count > 0)
                                <span class="badge-view">
                                    👁️ {{ number_format($produit->vues_count) }} vues
                                </span>
                                @endif

                                <!-- PROMO -->
                                @if($produit->inPromotion())
                                <span class="promo-badge right">
                                    -{{ $produit->inPromotion()->pourcentage }}%
                                </span>
                                @endif

                                @if($produit->new)
                                <span class="badge-new">NEW</span>
                                @endif
                                <div class="product-hover-action">
                                    <ul class="cart-action">

                                        <li class="quickview"><a href="#" data-bs-toggle="modal"
                                                data-bs-target="#{{ $produit->id }}"><i
                                                    class="far fa-eye"></i></a></li>
                                        {{-- <li class="quickview"><a href="#" data-bs-toggle="modal"
                                                                    data-bs-target="#{{ $produit->id }}"><i class="far fa-eye"></i></a></li> --}}
                                        <li class="select-option2">
                                            <a onclick="AddToCart( {{ $produit->id }} )">
                                                {{ \App\Helpers\TranslationHelper::TranslateText('Ajouter au panier') }}
                                            </a>
                                        </li>


                                        @if (Auth()->user())

                                        @php

                                        $count = DB::table('favoris')
                                        ->where('id_user', Auth()->user()->id)
                                        ->where('id_produit', $produit->id)
                                        ->count();
                                        @endphp


                                        <li class="wishlist"><a onclick="AddFavoris({{ $produit->id }})" @if ($count==0) class="" style="color:#000000" @else class="" style="color: #dc3545; background-color:#dc3545" @endif>

                                                <i class="far fa-heart"></i></a></li>
                                        @endif



                                        <style>
                                            .select-option2 {
                                                background-color: #5EA13C;
                                                color: #ffffff;
                                                border: none;
                                                padding: 10px 20px;
                                                border-radius: 5px;
                                                text-decoration: none;
                                            }

                                            .favori-actif {
                                                color: red;
                                                /* Changez la couleur selon votre besoin */
                                            }
                                        </style>
                                    </ul>
                                </div>
                            </div>

                            <!-- CONTENT -->
                            <div class="product-content">
                                <div class="inner">

                                    <h5 class="title">
                                        <a href="{{ route('details-produits', ['id' => $produit->id, 'slug' => Str::slug($produit->nom)]) }}">
                                            {{ $produit->nom }}
                                        </a>
                                    </h5>

                                    <!-- PRICE -->
                                    <div class="product__price__wrapper">

                                        @if($produit->inPromotion())
                                        <b class="text-success">
                                            {{ $produit->getPrice() }} <x-devise />
                                        </b>

                                        <strike class="text-danger ms-2">
                                            {{ $produit->prix }} <x-devise />
                                        </strike>
                                        @else
                                        <span class="price">
                                            {{ $produit->getPrice() }} <x-devise />
                                        </span>
                                        @endif

                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                    @endforeach

                </div>
                
        <!-- BUTTON -->
        <div class="col-lg-12 text-center mt--20">
            <a href="{{ route('shop', ['highlight' => 'most_viewed']) }}"
                class="axil-btn btn-bg-primary2 btn-load-more">

                👁️ {{ \App\Helpers\TranslationHelper::TranslateText('Voir tous les produits les plus vus') }}

            </a>
        </div>

            </div>
        </div>


    </div>
</div>