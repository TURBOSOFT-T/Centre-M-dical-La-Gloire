 <div class="header-search-modal" id="header-search-modal">
     <button class="card-close sidebar-close"><i class="fas fa-times"></i></button>
     <div class="header-search-wrap">
         <div class="card-header">
             <form action="{{ route('shop') }}" method="GET">
                 <div class="input-group">
                     <input
                         type="text"
                         name="key"
                         id="search"
                         class="form-control"
                         value="{{ request('key') }}"
                         placeholder="{{ \App\Helpers\TranslationHelper::TranslateText('Rechercher un produit...') }}">

                     <button type="submit" class="axil-btn btn-bg-primary">
                         <i class="far fa-search"></i>
                     </button>
                 </div>
             </form>
         </div>
         <div class="card-body">
             <div class="search-result-header">
                 <h6 class="title"></h6>
                 <a href="{{ route('shop') }}" class="view-all">
                     {{ \App\Helpers\TranslationHelper::TranslateText('Voir tout') }}
                 </a>
             </div>
             <div class="psearch-results">
                 @if (isset($searchproducts))
                 @foreach ($searchproducts as $produit)
                 <div class="axil-product-list">
                     <div class="thumbnail">
                         <a href="{{ route('details-produits', ['id' => $produit->id, 'slug' => Str::slug(Str::limit($produit->nom, 10))]) }}">
                             <img  loading="lazy" width="100" height="100" src="{{ Storage::url($produit->photo) }}" alt="Yantiti Leather Bags">
                         </a>
                     </div>
                     <div class="product-content">
                         <div class="product-rating">
                             {{-- <span class="rating-icon">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fal fa-star"></i>
                            </span>
                                <span class="rating-number"><span>100+</span> Reviews</span> --}}
                         </div>
                         <h6 class="product-title"><a href="{{ route('details-produits', ['id' => $produit->id, 'slug' => Str::slug(Str::limit($produit->nom, 10))]) }}">
                                 {!! \App\Helpers\TranslationHelper::TranslateText($produit->nom ?? '') !!}

                                 <!--  @if( $produit->points > 0)
                                                   
                                                    ({{ $produit->points }}
                                                                    {{ \App\Helpers\TranslationHelper::TranslateText('Points') }})
                                                                    @endif --> </a>
                         </h6>

                         <div class="product-price-variant">
                             @if ($produit->inPromotion())
                             <span class="price current-price"><b class="text-success" style="color: #4169E1">
                                     {{ $produit->getPrice() }} <x-devise></x-devise>
                                 </b></span>
                             <span class="price old-price">
                                 <span class="price old-price" style="position: relative; font-size: 1.2rem; color: #dc3545; font-weight: bold;">
                                     {{ $produit->prix }} <x-devise></x-devise>
                                     <span style="position: absolute; top: 50%; left: 0; width: 100%; height: 2px; background-color: black;"></span>
                                 </span>
                             </span>
                             @else
                             {{ $produit->getPrice() }} <x-devise></x-devise>
                             @endif

                         </div>
                         <div class="product-cart">
                             <a onclick="AddToCart( {{ $produit->id }} )" class="cart-btn"><i class="fal fa-shopping-cart"></i></a>
                             @if (Auth()->user())
                             <a onclick="AddFavoris({{ $produit->id }})" class="cart-btn"><i class="fal fa-heart"></i></a>
                             @endif
                         </div>
                     </div>
                 </div>
                 @endforeach
                 @endif

             </div>
         </div>
     </div>
 </div>