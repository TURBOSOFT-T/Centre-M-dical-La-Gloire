  <!-- End Slider Area -->
  <!-- Start Categorie Area  -->
  <!-- Start Categorie Area  -->
  <div class="row">
      <div class="col-xl-12">
          <div class="tp-testimonial-title-box text-center mb-60">

              <span class="tp-section-subtitle">
                  🗂️
                  {{ \App\Helpers\TranslationHelper::TranslateText('Nos catégories') }}
              </span>

              <h4 class="tp-section-title">

                  {{ \App\Helpers\TranslationHelper::TranslateText('Explorez nos produits par univers') }}
              </h4>

          </div>
      </div>
  </div>
  <div class="axil-categorie-area bg-color-white axil-section-gapcommon">
      <div class="container">
          <!--  <div class="section-title-wrapper">
                    <h4> <span class="axil-breadcrumb-item1 active" aria-current="page"> <i class="far fa-tags"></i> {{ \App\Helpers\TranslationHelper::TranslateText('Categories') }}</span> </h4>
                    <h2 class="title">
                        {{ \App\Helpers\TranslationHelper::TranslateText('Parcourrir par categories') }}
                    </h2>
                </div> -->
          

              <div class="categrie-product-activation slick-layout-wrapper--15 axil-slick-arrow  arrow-top-slide">

                  @foreach ($categories as $category)
                  <div class="slick-single-layout">
                      <div class="categrie-product" data-sal="zoom-out" data-sal-delay="200" data-sal-duration="500">
                          <a href="/shop?id_categorie={{ $category->id }}"
                              class="{{ isset($current_category) && $current_category->id === $category->id ? 'selected' : '' }}">

                              <img
                                  src="{{ Storage::url($category->photo) }}"
                                  class="category-img rounded shadow"
                                  alt="{{ $category->nom }}" loading="lazy"style="object-fit: cover; aspect-ratio: 1/1;">

                              <h6 class="cat-title">
                                  {{ \App\Helpers\TranslationHelper::TranslateText($category->nom ?? '') }}
                              </h6>
                          </a>
                      </div>
                  </div>
                  @endforeach


              </div>
         
      </div>
  </div>