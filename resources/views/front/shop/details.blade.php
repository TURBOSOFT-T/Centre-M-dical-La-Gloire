@extends('front.fixe')

@section('titre', $produit->nom)

@php
$shareUrl = urlencode(url()->current());
$shareTitle = urlencode($produit->nom);
@endphp

<head>
    @section('produits')
    <meta name="author" content="swootbio.com">
    <meta name="description" content="{{ $produit->description ?? '' }}">
    <meta property="og:title" content="{{ $produit->nom }}">
    <meta property="og:description" content="{{ $produit->meta_description ?? '' }}">
    <meta property="og:brand" content="{{ $produit->marques->nom ?? '' }}">



    <meta property="og:type" content="product">
    <meta property="og:price:amount" content="{{ $produit->prix }}">

    <meta property="og:description" content="Prix : {{ $produit->getPrice() }} FCFA" />

    <meta property="og:url" content="{{ $productUrl }}" />
    <meta name="robots" content="index, follow">


    <!-- Balises Open Graph pour l'aperçu WhatsApp -->
<meta property="og:title" content="{{ $produit->nom }}" />
    <meta property="og:description" content="Prix : {{ number_format($produit->prix, 0, ',', ' ') }} FCFA | Livraison rapide à Douala et partout au Cameroun." />
   <meta property="og:image" content="{{ asset('storage/' . ltrim($produit->photo, '/')) }}" />
    <meta property="og:image:secure_url" content="{{ asset('storage/' . ltrim($produit->photo, '/')) }}" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="product" />
    @endsection
</head>

<style>
    .product-action-container {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 0;
        padding-left: 0;
        list-style: none;
        width: 100%;
    }

    .product-action-container>li {
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
        width: auto;
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
        width: auto;
    }

    .btn-whatsapp-action:hover {
        background-color: #1EBE5D;
        color: #ffffff !important;
    }

    @media (max-width: 575.98px) {
        .product-action-container {
            flex-direction: column;
            gap: 8px;
        }

        .product-action-container>li {
            width: 100%;
        }

        .btn-cart,
        .btn-whatsapp-action {
            width: 100%;
            text-align: center;
            padding: 12px 15px;
        }

        .product-action-container>li.wishlist {
            justify-content: center;
        }
    }
</style>


@section('body')
<main class="main-wrapper">
    <!-- Le reste de la section body reste identique -->
    <!-- Start Shop Area  -->
    <div class="axil-single-product-area axil-section-gap pb--0 bg-color-white">
        <div class="single-product-thumb mb--40">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="shop-details-img">
                            <div class="tab-content" id="v-pills-tabContent">

                                <div class="shop-details-tab-img product-img--main" id="zoomContainers"
                                    data-scale="1.4" style="overflow: hidden; position: relative;">
                                    @if ($produit->inPromotion())
                                    <span class="promo-badge">PROMO</span>
                                    <span class="promo-badge right">
                                        -{{ $produit->inPromotion()->pourcentage }}%
                                    </span>
                                    @endif
                                    <img id="mainImage" src="{{ Storage::url($produit->photo) }}" height="600"
                                        width="600" alt="Product image"
                                        style="transition: transform 0.3s ease;" loading="lazy" fetchpriority="high" style="object-fit: cover; aspect-ratio: 1/1;" />

                                    @if ($produit->new)
                                    <span class="badge-new">NEW</span>
                                    @endif
                                    @if ($produit->vendus_sum_quantite > 0)
                                    <span class="badge-sold">
                                        {{ $produit->vendus_sum_quantite }}
                                        {{ \App\Helpers\TranslationHelper::TranslateText('Vendu(s)') }}
                                    </span>
                                    @endif
                                </div>


                            </div>
                            <br><br>

                            <div class="nav nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                @foreach (json_decode($produit->photos) ?? [] as $image)
                                <div class="slider__item">
                                    <img onclick="changeMainImage('{{ Storage::url($image) }}')"
                                        src="{{ Storage::url($image) }}" width="100" height="100"
                                        style="border-radius: 8px;" alt="Additional product image" />
                                </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-5 mb--40">
                        <div class="single-product-content">
                            <div class="inner">
                                <h3 class="product-title"> {{ \App\Helpers\TranslationHelper::TranslateText($produit->nom) }}

                                </h3>

                                <span class="price-amount">

                                    @if ($produit->inPromotion())
                                    <div class="row">
                                        <div class="col-sm-6 col-6">

                                            <b class="text-success" style="color: #4169E1">
                                                {{ $produit->getPrice() }} <x-devise></x-devise>
                                            </b>
                                        </div>

                                        <div class="col-sm-6 col-6 text-end">


                                            <span
                                                style="position: relative; font-size: 1.7rem; color: #dc3545; font-weight: bold;">
                                                {{ $produit->prix }} <x-devise></x-devise>
                                                <span
                                                    style="position: absolute; top: 50%; left: 0; width: 100%; height: 2px; background-color: black;"></span>
                                            </span>


                                        </div>
                                        @else

                                        <span class="price current-price">

                                            {{ $produit->getPrice() }} <x-devise></x-devise>
                                            </b></span>

                                        @endif
                                </span>
                                <div class="product-rating">

                                </div>
                                <ul class="product-meta">
                                    @if ($produit->stock > 0)
                                    <label class="badge btn-bg-primary2"> {{ \App\Helpers\TranslationHelper::TranslateText('Stock disponible') }}</label>
                                    @else
                                    <label class="badge bg-danger"> {{ \App\Helpers\TranslationHelper::TranslateText('Stock non disponible') }}</label>
                                    @endif
                                    <br>

                                    <li><span style="color: #EFB121"> {{ \App\Helpers\TranslationHelper::TranslateText('Categorie') }}:</span> <span style="color: #5EA13C">

                                            {{ \App\Helpers\TranslationHelper::TranslateText($produit->categories->nom ?? ' ') }}
                                        </span></li>



                                </ul>
                                <p class="description">

                                    {!! \App\Helpers\TranslationHelper::TranslateText($produit->meta_description) !!}
                                </p>

                                <div class="product-variations-wrapper">


                                    <div class="product-variation">

                                    </div>



                                    <div class="product-variation product-size-variation">

                                    </div>


                                </div>

                                <!-- Start Product Action Wrapper  -->
                                <div class="product-action-wrapper d-flex-center">
                                    <!-- Start Quentity Action  -->
                                    <div class="pro-qty">
                                        {{-- <input type="text" value="1"> --}}
                                        <span class="quantity-control minus"></span>
                                        <input type="number" class="input-text qty text" name="quantite"
                                            min="1" value="1" id="qte-{{ $produit->id }}"
                                            autocomplete="off">
                                        <span class="quantity-control plus"></i></span>
                                    </div>

                                    <ul class="product-action-container">

                                        <li>
                                            <a onclick="AddToCart({{ $produit->id }})" class="btn-cart">
                                                {{ \App\Helpers\TranslationHelper::TranslateText('Ajouter au panier') }}
                                            </a>
                                        </li>

                                        
                                        @if(!empty($whatsappUrl))
                                        <li>
                                       

                                             <a href="{{ $whatsappUrl }}" target="_blank"  class="btn-cart">
                                                <i class="ri-whatsapp-line fs-5"></i>
                                                <span>{{ \App\Helpers\TranslationHelper::TranslateText('Commander sur WhatsApp') }}</span>
                                            </a>

                                        </li>
                                        @endif 
                                        @auth
                                        <li class="wishlist">
                                            <a onclick="AddFavoris({{ $produit->id }})" class="axil-btn wishlist-btn btn btn-outline-secondary">
                                                <i class="far fa-heart"></i>
                                            </a>
                                        </li>
                                        @endauth
                                    </ul>
                                    <!-- End Product Action  -->

                                </div>

                                <div class="social-share-wrapper mt--20">
                                    <span class="social-share-title">
                                        <i class="fas fa-share-alt"></i> {{ \App\Helpers\TranslationHelper::TranslateText('Partager sur') }} :
                                    </span>

                                    <!-- WhatsApp Share -->
                                    <a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ $shareUrl }}"
                                        target="_blank" rel="noopener noreferrer" class="share-btn share-whatsapp" title="Partager via WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End .single-product-thumb -->

        <div class="woocommerce-tabs wc-tabs-wrapper bg-vista-white">
            <div class="container">
                <ul class="nav tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="active" id="description-tab" data-bs-toggle="tab" href="#description"
                            role="tab" aria-controls="description" aria-selected="true"><span style="color: #EFB121"> {{ \App\Helpers\TranslationHelper::TranslateText('Description') }}</span></a>
                    </li>

                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="description" role="tabpanel"
                        aria-labelledby="description-tab">
                        <div class="product-desc-wrapper">
                            <div class="row">
                                <div class="col-lg-12 mb--30">
                                    <div class="single-desc">

                                        <p>
                                            {!! \App\Helpers\TranslationHelper::TranslateText($produit->description ?? ' ') !!}
                                        </p>
                                    </div>
                                </div>
                                <!-- End .col-lg-6 -->

                                <!-- End .col-lg-6 -->
                            </div>
                            <!-- End .row -->

                            <!-- End .row -->
                        </div>
                        <!-- End .product-desc-wrapper -->
                    </div>

                </div>
            </div>
        </div>
        <!-- woocommerce-tabs -->

    </div>
    <!-- End Shop Area  -->

    <!-- Start Recently Viewed Product Area  -->
    <div class="axil-product-area bg-color-white axil-section-gap pb--50 pb_sm--30">
        <div class="container">
            <div class="section-title-wrapper">
                <h4> <span class="axil-breadcrumb-item1 active" aria-current="page"> <i class="far fa-shopping-basket"></i>

                        {{ \App\Helpers\TranslationHelper::TranslateText('Les produits de la même categorie') }}
                    </span> </h4>


                <h2 class="title"> {{ \App\Helpers\TranslationHelper::TranslateText('Parcourir') }}</h2>
            </div>
            <div class="recent-product-activation slick-layout-wrapper--15 axil-slick-arrow arrow-top-slide">

                @if ($produitsSimilaires)
                @foreach ($produitsSimilaires as $produit)
                <div class="slick-single-layout">
                    <div class="axil-product">
                        <div class="thumbnail">
                            <a

                                href="{{ route('details-produits', ['id' => $produit->id, 'slug' => Str::slug(Str::limit($produit->nom, 10))]) }}">
                                @if ($produit->inPromotion())
                                <span class="promo-badge">PROMO</span>
                                <span class="promo-badge right">
                                    -{{ $produit->inPromotion()->pourcentage }}%
                                </span>
                                @endif
                                <img src="{{ Storage::url($produit->photo) }}" alt="Product Images">

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
                                    @if (Auth()->user())
                                    <li class="wishlist"><a
                                            onclick="AddFavoris({{ $produit->id }})"><i
                                                class="far fa-heart"></i></a></li>
                                    @endif
                                    <li class="axil-btn  btn-bg-primary2 "><a
                                            onclick="AddToCart( {{ $produit->id }} )">
                                            {{ \App\Helpers\TranslationHelper::TranslateText('Ajouter au panier') }}
                                        </a></li>
                                    {{-- <li class="quickview"><a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#quick-view-modal"><i
                                                            class="far fa-eye"></i></a></li> --}}

                                </ul>
                            </div>
                        </div>
                        <div class="product-content">
                            <div class="inner">
                                <h5 class="title"><a
                                        href="{{ route('details-produits', ['id' => $produit->id, 'slug' => Str::slug(Str::limit($produit->nom, 10))]) }}"> {{ \App\Helpers\TranslationHelper::TranslateText(Str::limit($produit->nom, 15)) }}


                                    </a>
                                </h5>
                                <div class="product-price-variant">
                                    <h6 class="product-price--main">


                                        @if ($produit->inPromotion())
                                        <div class="row">
                                            <div class="col-sm-6 col-6">

                                                <b class="text-success" style="color: #4169E1">
                                                    {{ $produit->getPrice() }} <x-devise></x-devise>
                                                </b>
                                            </div>

                                            <div class="col-sm-6 col-6 text-end">



                                                {{-- <span style="font-size: 1.2rem; color: #dc3545; font-weight: bold;">
                                                            {{ $produit->prix }} DT
                                                </span> --}}
                                                <span class="price old-price"
                                                    style="position: relative; font-size: 1.2rem; color: #dc3545; font-weight: bold;">
                                                    {{ $produit->prix }} <x-devise></x-devise>
                                                    <span
                                                        style="position: absolute; top: 50%; left: 0; width: 100%; height: 2px; background-color: black;"></span>
                                                </span>


                                            </div>
                                            @else
                                            {{ $produit->getPrice() }} <x-devise></x-devise>
                                            @endif



                                    </h6>
                                </div>
                                <div class="color-variant-wrapper">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif



            </div>
        </div>
        <style>
            .axil-breadcrumb-item1 {
                font-size: 14px;
                color: #EFB121;
                /* Default breadcrumb color */
            }

            .axil-breadcrumb-item.active {
                font-weight: bold;
                color: #EFB121;
                /* Distinct color for active item */
            }

            .axil-breadcrumb-item:not(.active)::after {
                content: " / ";
                /* Adds a separator after non-active items */
                color: #EFB121;
            }
        </style>
        <!-- End Axil Newsletter Area  -->





        <script>
            const zoomContainers = document.getElementById('zoomContainers');
            const mainImage = document.getElementById('mainImage');
            const scale = zoomContainers.getAttribute('data-scale') || 1.4;


            zoomContainers.addEventListener('mouseover', function() {
                mainImage.style.transform = `scale(${scale})`;
                mainImage.style.cursor = "zoom-in";
            });


            zoomContainers.addEventListener('mouseout', function() {
                mainImage.style.transform = "scale(1)";
            });


            function changeMainImage(imageUrl) {
                mainImage.src = imageUrl;
                mainImage.style.transform = "scale(1)";
            }
        </script>



        <style>
            /* Boutons de partage */
            .social-share-wrapper {
                margin-top: 15px;
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
            }

            .social-share-title {
                font-size: 13px;
                font-weight: 600;
                color: #555;
                margin-right: 5px;
            }

            .share-btn {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #ffffff !important;
                font-size: 16px;
                transition: transform 0.2s ease, opacity 0.2s ease;
                text-decoration: none;
            }

            .share-btn:hover {
                transform: translateY(-2px);
                opacity: 0.9;
            }

            .share-facebook {
                background-color: #1877F2;
            }

            .share-twitter {
                background-color: #000000;
            }

            .share-linkedin {
                background-color: #0A66C2;
            }

            .share-whatsapp {
                background-color: #25D366;
            }
        </style>
</main>


@endsection