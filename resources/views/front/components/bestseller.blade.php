 <div class="container"  >
     <div class="row">
         <div class="col-xl-12">
             <div class="tp-blog-title-box text-center mb-60">

                 <span class="best-seller-subtitle">
                     🏆{{ \App\Helpers\TranslationHelper::TranslateText('TOP VENTES') }}
                 </span>

                 <h4 class="best-seller-title">

                     {{ \App\Helpers\TranslationHelper::TranslateText('Les produits les plus vendus') }}
                 </h4>

                 <div class="mt-2">
                     <span class="best-seller-badge">
                         ⭐x{{ \App\Helpers\TranslationHelper::TranslateText('Les meilleures ventes') }}⭐
                     </span>
                 </div>

             </div>
         </div>
     </div>


     <br><br><br>

     <div class="axil-best-seller-product-area bg-color-white axil-section-gap pb--0">
         <div class="container">
             <div class="product-area pb--50">



                 <div class="new-arrivals-product-activation-2 slick-layout-wrapper--15 axil-slick-arrow arrow-top-slide product-slide-mobile">

                     @foreach ($bestSellers as $produit)
                     <div class="slick-single-layout">
                         <div class="axil-product product-style-six">
                             <div class="thumbnail">


                                 <a {{-- href="{{ route('details-produits', ['id' => $produit->id, 'slug' => Str::slug(Str::limit($produit->nom, 10))]) }}" --}}>

                                     @if ($produit->inPromotion())
                                     <span class="promo-badge">PROMO</span>
                                     <span class="promo-badge right">
                                         -{{ $produit->inPromotion()->pourcentage }}%
                                     </span>
                                     @endif

                                     <!-- Image principale du produit -->
                                     <picture>
                                         <img
                                             data-sal="zoom-out"
                                             data-sal-delay="200"
                                             data-sal-duration="800"
                                             loading="lazy"
                                             style="object-fit: cover; aspect-ratio: 1/1;"
                                             class="main-img product-img"
                                             src="{{ Storage::url($produit->photo) }}"
                                             alt="{{ $produit->nom }}">
                                     </picture>

                                     <!-- Image au survol (hover) du produit -->
                                     <picture>
                                         <img
                                             class="hover-img product-img"
                                             src="{{ Storage::url($produit->photo) }}"
                                             style="object-fit: cover; aspect-ratio: 1/1;"
                                             alt="{{ $produit->nom }}"
                                             loading="lazy">
                                     </picture>

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
                                     <!--  <div class="product-price-variant">
                                            <span class="price current-price">

                                            
                                            </span>
                                        </div> -->
                                     <h5 class="title"><a href="{{ route('details-produits', ['id' => $produit->id, 'slug' => Str::slug(Str::limit($produit->nom, 10))]) }}">
                                             {{ \App\Helpers\TranslationHelper::TranslateText($produit->nom) }}


                                         </a>
                                     </h5>


                                     <div class="product-hover-action">
                                         <ul class="cart-action">
                                             <li class="select-option">


                                                 <a href="{{ route('details-produits', ['id' => $produit->id, 'slug' => Str::slug(Str::limit($produit->nom, 10))]) }}">
                                                     {{ \App\Helpers\TranslationHelper::TranslateText( 'Achetez maintenant') }}


                                                 </a>
                                             </li>
                                         </ul>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     @endforeach



                 </div>
                 <div class="row">
                     <div class="col-lg-12 text-center mt--20 mt_sm--0">
                         <a href="{{ route('shop', ['highlight' => 'best']) }}"
                             class="axil-btn btn-bg-primary2 btn-load-more">
                             {{ \App\Helpers\TranslationHelper::TranslateText('Voir toutes les meilleures ventes') }}
                         </a>
                     </div>
                 </div>
             </div>
         </div>

     </div>