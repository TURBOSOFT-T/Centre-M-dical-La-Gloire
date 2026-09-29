 <!-- Modal -->
 <div class="modal modal-2 fade" id="exampleModal{{ $produit->id }}{{ $key }}" tabindex="-1" role="dialog">
     <div class="modal-dialog modal-dialog-centered" role="document">
         <div class="modal-content">
             <div class="modal-body">
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> <i
                         class="pe-7s-close"></i></button>
                 <div class="row">
                     <div class="col-lg-6 col-sm-12 col-xs-12 mb-lm-30px mb-md-30px mb-sm-30px">
                         <!-- Swiper -->
                         <div class="swiper-container gallery-top">
                             <div class="swiper-wrapper">
                                 <div class="swiper-slide">
                                     <img class="img-responsive m-auto" src="{{ Storage::url($produit->photo) }}"
                                         alt="">
                                 </div>
                                 @foreach (json_decode($produit->photos) ?? [] as $photo)
                                     <div class="swiper-slide">
                                         <img class="img-responsive m-auto" src="{{ Storage::url($photo) }}"
                                             alt="">
                                     </div>
                                 @endforeach
                             </div>
                         </div>
                         <div class="swiper-container gallery-thumbs mt-20px slider-nav-style-1 small-nav">
                             <div class="swiper-wrapper">
                                 <div class="swiper-slide">
                                     <img class="img-responsive m-auto" src="{{ Storage::url($produit->photo) }}"
                                         alt="">
                                 </div>
                                 @foreach (json_decode($produit->photos) ?? [] as $photo)
                                     <div class="swiper-slide">
                                         <a href="{{ route('produit2', ['id'=>$produit->id]) }}">
                                            <img class="img-responsive m-auto" src="{{ Storage::url($photo) }}"
                                             alt="">
                                         </a>
                                     </div>
                                 @endforeach
                             </div>
                             <!-- Add Arrows -->
                             <div class="swiper-buttons">
                                 <div class="swiper-button-next"></div>
                                 <div class="swiper-button-prev"></div>
                             </div>
                         </div>
                     </div>
                     <div class="col-lg-6 col-sm-12 col-xs-12" data-aos="fade-up" data-aos-delay="200">
                         <div class="product-details-content quickview-content">
                             <h2>
                                 {{ $produit->nom }}
                             </h2>
                             <div class="pricing-meta">
                                 <ul class="d-flex">
                                     <li class="new-price">
                                         {{ $produit->prix }} <x-devise></x-devise>
                                     </li>
                                 </ul>
                             </div>
                             <div class="pro-details-rating-wrap">
                                 <div class="rating-product">
                                     <i class="fa fa-star"></i>
                                     <i class="fa fa-star"></i>
                                     <i class="fa fa-star"></i>
                                     <i class="fa fa-star"></i>
                                     <i class="fa fa-star"></i>
                                 </div>
                                 <span class="read-review">
                                     <a class="reviews" href="#"></a>
                                 </span>
                             </div>
                             <p class="mt-30px">
                                 {{ strlen($produit->description) > 300 ? substr($produit->description, 0, 300) . '...' : $produit->description }}

                             </p>
                             @livewire('BtnAddPanier', ['produit' => $produit])
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>
 <!-- Modal end -->
