<div class="axil-product-area bg-color-white axil-section-gap">
    <div class="container">
        <div class="section-title-wrapper">
            <div class="row">
                <div class="col-xl-5 col-lg-6">
                    <div class="tp-service-title-box mb-55">
                        <span class="tp-section-subtitle">
                            ⭐ 
                             
                               {{ \App\Helpers\TranslationHelper::TranslateText('Nos meilleures offres') }}
                        </span>
                        <h4 class="tp-section-title">
                            
                              {{ \App\Helpers\TranslationHelper::TranslateText('Des produits sélectionnés pour répondre à tous vos besoins') }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="explore-product-activation slick-layout-wrapper slick-layout-wrapper--15 axil-slick-arrow arrow-top-slide">
            <div class="slick-single-layout">
                <div class="row row--15">
                    @foreach ($produitshome as $produit)
                    @if ($produit)
                    <div class="col-xl-3 col-lg-4 col-sm-6 col-12 mb--30">
                        <div class="axil-product product-style-one">
                            <div class="thumbnail">
                                <a href="{{ route('details-produits', ['id' => $produit->id, 'slug' => Str::slug(Str::limit($produit->nom, 10))]) }}">
                                  @if ($produit->inPromotion())
                                                    <span class="promo-badge left">PROMO</span>
                                                    <span class="promo-badge right">
                                                        -{{ $produit->inPromotion()->pourcentage }}%
                                                    </span>
                                                    @endif    
                                <img
                                         data-sal="zoom-out"
                                         data-sal-delay="200"
                                         data-sal-duration="800"
                                         loading="lazy"
                                         class="main-img product-img"
                                         src="{{ Storage::url($produit->photo) }}"
                                         alt="{{ $produit->nom }}" loading="lazy" style="object-fit: cover; aspect-ratio: 1/1;">

                                     <img
                                         class="hover-img product-img"
                                         src="{{ Storage::url($produit->photo) }}"
                                         alt="{{ $produit->nom }}" loading="lazy" style="object-fit: cover; aspect-ratio: 1/1;"> 
                                        
                                        
                                                    @if ($produit->new)
                                                    <span class="badge-new">NEW</span>
                                                    @endif
                                                    @if ($produit->vendus_sum_quantite > 0)
                                                    <span class="badge-sold">
                                                        {{ $produit->vendus_sum_quantite }} 
                                                           {{ \App\Helpers\TranslationHelper::TranslateText('Vendu(s)') }}
                                                    </span>
                                                    @endif
                                        </a>

                               
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
                            <div class="product-content">
                                <div class="inner">
                                    <div class="product-rating">

                                    </div>

                                    <div class="">
                                        <h5 class="title"><a href="{{ route('details-produits', ['id' => $produit->id, 'slug' => Str::slug(Str::limit($produit->nom, 10))]) }}">
                                                {{ \App\Helpers\TranslationHelper::TranslateText($produit->nom) }}

                                              
                                            </a>
                                        </h5>
                                    </div>
                                    <div class="product__price__wrapper">
                                        <h6 class="product-price--main">


                                            @if ($produit->inPromotion())
                                            <div class="row">
                                                <div class="col-sm-6 col-6">

                                                    <b class="text-success" style="color: #4169E1">
                                                        {{ $produit->getPrice() }} <x-devise></x-devise>
                                                    </b>
                                                </div>

                                                <div class="col-sm-6 col-6 text-end">
                                                    <strike>


                                                        <span style="font-size: 1.7rem; color: #dc3545; font-weight: bold;">
                                                            {{ $produit->prix }} <x-devise></x-devise>
                                                        </span>


                                                    </strike>
                                                </div>
                                                @else
                                                {{-- {{ $produit->getPrice() }}DT --}}


                                                <span class="price current-price" style="font-size: 1.7rem;">
                                                    {{ $produit->getPrice() }} <x-devise></x-devise>
                                                    </b></span>
                                                @endif


                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach

                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-lg-12 text-center mt--20 mt_sm--0">
                <a href="{{ route('shop') }}" class="axil-btn btn-bg-primary2 btn-load-more">

                    {{ \App\Helpers\TranslationHelper::TranslateText('Voir tous les produits') }}
                </a>
            </div>
        </div>

    </div>
</div>