<style>
    @keyframes blinkText {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }

    @keyframes blinkColor {
        0% {
            opacity: 1;
            color: red;
        }

        50% {
            opacity: 0;
            color: blue;
        }

        100% {
            opacity: 1;
            color: red;
        }
    }

    .blink-color {
        animation: blinkColor 1s infinite;
    }

    /* Structure et hauteurs du Carousel */
    .carousel-item img {
        height: 600px;
        object-fit: cover;
    }

    .carousel-item {
        background-color: #333333;
        border: 5px solid #ffffff;
        border-radius: 20px;
        overflow: hidden;
    }

    /* Positionnement et alignement du contenu à gauche */
    .carousel-caption {
        bottom: 15%;
        left: 5%;
        right: 5%;
        padding-bottom: 0;
    }

    .main-slider-content {
        text-align: left;
        margin-left: 0;
        padding-left: 20px;
    }

    .main-slider-content .subtitle,
    .main-slider-content p {
        font-size: 3rem;
        color: #ffffff;
        margin-bottom: 0;
        line-height: 1.2;
    }

    .shop-btn {
        text-align: left;
        padding-left: 20px;
    }

    /* Tablettes (max-width: 992px) */
    @media (max-width: 992px) {
        .carousel-item img {
            height: 300px;
        }

        .main-slider-content .subtitle,
        .main-slider-content p {
            font-size: 2rem;
        }
    }

    /* Mobiles (max-width: 768px) - Maintien strict de la forme à gauche */
    @media (max-width: 768px) {
        .carousel-item img {
            height: 250px;
            /* Ajustement pour garder un ratio d'aspect équilibré */
        }

        .carousel-caption {
            bottom: 10%;
            /* Remonte légèrement le bloc pour éviter les débordements */
        }

        .main-slider-content {
            padding-left: 10px;
        }

        .main-slider-content .subtitle,
        .main-slider-content p {
            font-size: 1.2rem !important;
            /* Réduction proportionnelle pour garder la mise en page */
        }

        .shop-btn {
            padding-left: 10px;
        }

        .shop-btn .axil-btn {
            padding: 8px 16px;
            font-size: 0.85rem;
            /* Réduit la taille du bouton pour le mobile */
        }
    }
</style>

<div class="container-fluid px-0 mb-5">
    <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach ($banners as $key => $banner)
            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                <!--    <img class="d-block w-100" src="{{ Storage::url($banner->image) }}" alt="Banner Image" loading="lazy">
 -->
                <picture>

                    <img class="d-block w-100" src="{{ Storage::url($banner->image) }}" alt="Banner Image" loading="lazy"  style="object-fit: cover; aspect-ratio: 1/1;">
                </picture>
                <div class="carousel-caption d-block">
                    <div class="container px-0">
                        <div class="main-slider-content">
                            <span class="subtitle">
                                <i class="fas fa-fire"></i>
                                {{ \App\Helpers\TranslationHelper::TranslateText($banner->titre ?? ' ') }}
                            </span>
                            <p class="mt-1">
                                {{ \App\Helpers\TranslationHelper::TranslateText($banner->sous_titre ?? ' ') }}
                            </p>
                        </div>

                        <div class="shop-btn mt-3 d-flex justify-content-start">
                            <a href="{{ route('shop') }}" class="axil-btn btn-bg-primary2 right-icon">
                                {{ \App\Helpers\TranslationHelper::TranslateText('Voir boutique') }}
                                <i class="fal fa-long-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>