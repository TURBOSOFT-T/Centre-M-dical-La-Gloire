@extends('front.fixe')
@section('titre', 'Shop')
@section('body')

<main>
    <div class="sticky-header">
        <a href="#top" class="back-to-top" id="backto-top"><i class="fal fa-arrow-up"></i></a>

        <main class="main-wrapper">
            <!-- Start Breadcrumb Area  -->
            <div class="axil-breadcrumb-area">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-md-8">
                            <div class="inner">
                                <ul class="axil-breadcrumb">
                                    <li class="axil-breadcrumb-item"><a href="{{ route('home') }}"> {{ \App\Helpers\TranslationHelper::TranslateText('Accueil') }}</a></li>
                                    <li class="separator"></li>
                                    <li class="axil-breadcrumb-item1 active" aria-current="page"> {{ __('boutique') }}</li>
                                </ul>

                                <style>
                                    .axil-breadcrumb-item1 {
                                        font-size: 14px;
                                        color: #EFB121;
                                    }

                                    .axil-breadcrumb-item.active {
                                        font-weight: bold;
                                        color: #EFB121;
                                    }
                                </style>
                                <h1 class="title">
                                    {{ \App\Helpers\TranslationHelper::TranslateText('Explorez tous les produits') }}
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Breadcrumb Area  -->

            <!-- Start Shop Area  -->
            <div class="axil-shop-area axil-section-gap bg-color-white">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="axil-shop-sidebar">
                                <div class="d-lg-none">
                                    <button class="sidebar-close filter-close-btn"><i class="fas fa-times"></i></button>
                                </div>
                                <div class="toggle-list product-categories active">
                                    <h6 class="title"> {{ \App\Helpers\TranslationHelper::TranslateText('CATEGORIES') }}</h6>
                                    <div class="shop-submenu">
                                      <ul style="display: flex; flex-direction: column; gap: 8px; list-style: none; padding-left: 0; margin-bottom: 0;">
                                              <li class="current-cat"><a href="/shop"> {{ \App\Helpers\TranslationHelper::TranslateText('Tous les produits') }}</a></li>

                                            @foreach ($categories as $category)
                                            <li>
                                                <a style="min-height: 24px; display: flex; align-items: center; padding: 1px 24px; text-decoration: none;"
                                                href="/shop?id_categorie={{ $category->id }}"
                                                    class="small {{ isset($current_category) && $current_category->id === $category->id ? 'selected' : '' }}">
                                                    {{ \App\Helpers\TranslationHelper::TranslateText( Str::limit($category->nom, 25)) }}
                                                    <span>({{ $category->produits->count() }})</span>
                                                </a>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="toggle-list product-categories product-gender active">
                                    <h6 class="title">MARQUES</h6>
                                    <div class="shop-submenu">
                                      <ul style="display: flex; flex-direction: column; gap: 8px; list-style: none; padding-left: 0; margin-bottom: 0;">
                                     
                                            @foreach ($marques as $marque)
                                            <li><a style="min-height: 24px; display: flex; align-items: center; padding: 1px 24px; text-decoration: none;"
                                             href="/shop?marque_id={{ $marque->id }}"
                                                    class="{{ isset($current_marque) && $current_marque->id === $marque->id ? 'selected' : '' }}">{{ $marque->nom }}
                                                    ({{ $marque->produits->count() }})</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <a href="{{ route('shop.reset') }}" class="axil-btn btn-bg-primary2 mt-3 w-100">
                                    {{ \App\Helpers\TranslationHelper::TranslateText('Réinitialiser les filtres') }}
                                </a>
                            </div>
                            <!-- End .axil-shop-sidebar -->
                        </div>
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="axil-shop-top mb--40">
                                        <div
                                            class="category-select align-items-center justify-content-lg-end justify-content-between">
                                            <!-- Start Single Select  -->
                                            <span class="filter-results tp-product__filter"> {{ \App\Helpers\TranslationHelper::TranslateText('Filtrer') }}</span>

                                            <select class="single-select" onchange="applyFilter(this)">
                                                <option value="/shop">Default</option>
                                                <option value="promo">
                                                    {{ \App\Helpers\TranslationHelper::TranslateText('Promotions') }}
                                                </option>
                                                <option value="is_new">
                                                    {{ \App\Helpers\TranslationHelper::TranslateText('Nouveautés') }}
                                                </option>
                                                <option value="best">
                                                    {{ \App\Helpers\TranslationHelper::TranslateText('Les plus vendus') }}
                                                </option>
                                                <option value="most_viewed">
                                                    {{ \App\Helpers\TranslationHelper::TranslateText('Les plus vus') }}
                                                </option>
                                            </select>
                                            <!-- End Single Select  -->
                                            <div class="d-lg-none">
                                                <button class="product-filter-mobile filter-toggle"><i
                                                        class="fas fa-filter"></i> {{ \App\Helpers\TranslationHelper::TranslateText('Filtrer') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End .row -->
                            <div class="row row--15">
                                @if ($produits)
                                @foreach ($produits as $key => $produit)
                                <div class="col-xl-4 col-sm-6">
                                    <div class="axil-product product-style-one mb--30">
                                        <div class="thumbnail">
                                            <a
                                                href="{{ route('details-produits', ['id' => $produit->id, 'slug' => Str::slug(Str::limit($produit->nom, 10))]) }}">
                                                @if ($produit->inPromotion())
                                                <span class="promo-badge">PROMO</span>
                                                <span class="promo-badge right">
                                                    -{{ $produit->inPromotion()->pourcentage }}%
                                                </span>
                                                @endif
                                                <img class="main-img product-img" src="{{ Storage::url($produit->photo) }}"
                                                    alt="{{ $produit->nom }}"
                                                    style="max-width: 300px; max-height: 300px;"  fetchpriority="high" loading="lazy"  style="object-fit: cover; aspect-ratio: 1/1;">

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
                                                        }
                                                    </style>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="product-content">
                                            <div class="inner">
                                                <h5 class="title"><a
                                                        href="{{ route('details-produits', ['id' => $produit->id, 'slug' => Str::slug(Str::limit($produit->nom, 10))]) }}">
                                                        {{ \App\Helpers\TranslationHelper::TranslateText(Str::limit($produit->nom, 15)) }}
                                                    </a>
                                                </h5>

                                                <div class="product-price-variant">
                                                    <h6 class="product-price--main">
                                                        @if ($produit->inPromotion())
                                                        <div class="row">
                                                            <div class="col-sm-6 col-6">
                                                                <b class="text-success"
                                                                    style="color: #4169E1">
                                                                    {{ $produit->getPrice() }} <x-devise></x-devise>
                                                                </b>
                                                            </div>
                                                            <div class="col-sm-6 col-6 text-end">
                                                                <span class="price old-price"
                                                                    style="position: relative; font-size: 1.5rem; color: #dc3545; font-weight: bold;">
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>

                            <!-- Pagination -->
                            <div class="row mt--40">
                                <div class="col-lg-12">
                                    <div class="axil-pagination text-center">
                                        {{ $produits->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Shop Area  -->
        </main>

        <!-- Product Quick View Modal Start -->
        @if ($produits)
        @foreach ($produits as $key => $produit)
        @include('front.components.modal')
        @endforeach
        @endif
        <!-- Product Quick View Modal End -->
    </div>

    
    

    <style>
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            gap: 10px;
        }

        .page-item .page-link {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
        }

        .page-item.active .page-link {
            background-color: #5EA13C;
            color: white;
            border-color: #5EA13C;
        }
    </style>
     <script src="/Script.js"></script>
</main>
@endsection