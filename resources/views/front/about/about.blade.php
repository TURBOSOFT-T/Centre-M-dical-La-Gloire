@extends('front.fixe')
@section('titre', "A propos de nous")
@section('body')
<main>
    @php

    $config = DB::table('configs')->first();
    @endphp

    <div class="axil-breadcrumb-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-8">
                    <div class="inner">
                        <ul class="axil-breadcrumb">
                            <li class="axil-breadcrumb-item"><a href="{{ route('home') }}">
                                    {{ \App\Helpers\TranslationHelper::TranslateText('Accueil') }}
                                </a></li>
                            <li class="separator"></li>
                            <li class="axil-breadcrumb-item1 active" aria-current="page"> {{ \App\Helpers\TranslationHelper::TranslateText('A propos de nous') }}</li>
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
                            </style>
                        </ul>
                        <h1 class="title"> {{ \App\Helpers\TranslationHelper::TranslateText('A propos de notre boutique') }}</h1>
                    </div>
                </div>
                <div class="col-lg-6 col-md-4">
                
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb Area  -->

    <!-- Start About Area  -->
    <div class="axil-about-area about-style-1 axil-section-gap ">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-4 col-lg-6">
                    <div class="about-thumbnail">
                        <div class="thumbnail">
                            <img src="{{ Storage::url($config->image_apropos) }}" alt="About Us">
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-6">
                    <div class="about-content content-right">
                        <h4> <span class="axil-breadcrumb-item1 active" aria-current="page"> <i class="far fa-shopping-basket"></i> {{ \App\Helpers\TranslationHelper::TranslateText('A propos de nous') }}</span> </h4>
                     
                        <h3 class="title">
                            {{ \App\Helpers\TranslationHelper::TranslateText($config->titre_appros ?? '') }}
                        </h3>

                        <div class="row">
                            <div class="col-xl-12">
                               <p style="font-size: 18px; line-height: 2; text-align: justify;">
                                        {!! \App\Helpers\TranslationHelper::TranslateText($config->des_apropos ?? ' ') !!}
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End About Area  -->


    <!-- Start About Area  -->
    <div class="axil-about-area about-style-2">
        <div class="container">

            <div class="row align-items-center">
                <div class="col-lg-5 order-lg-2">
                    <div class="about-thumbnail">
                        <img src="{{ Storage::url($config->image_apropos1) }}" alt="about">
                    </div>
                </div>
                <div class="col-lg-7 order-lg-1">
                    <div class="about-content content-left">

                        <h4 class="title">
                            {{ \App\Helpers\TranslationHelper::TranslateText($config->titre_apropos2) }}
                        </h4>
                          <p style="font-size: 18px; line-height: 2; text-align: justify;">
                              
                            {!! $config->des_apropos2 !!}</p>
                        <a href="{{ route('shop') }}" class="axil-btn btn-bg-primary2"> {{ \App\Helpers\TranslationHelper::TranslateText('Voir boutique') }}</a>
                    </div>
                </div>
            </div>
            <div class="row align-items-center mb--80 mb_sm--60">
                <div class="col-lg-5">
                    <div class="about-thumbnail">
                        <img src="{{ Storage::url($config->image_apropos2) }}" alt="about">
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="about-content content-right">

                        <h4 class="title">{{ $config->titre_apropos1 }}</h4>
                       <p style="font-size: 18px; line-height: 2; text-align: justify;">
                              
                            {!! \App\Helpers\TranslationHelper::TranslateText($config->des_apropos1 ?? ' ') !!}
                        </p>
                        <a href="{{ route('shop') }}" class="axil-btn  btn-bg-primary2 "> {{ \App\Helpers\TranslationHelper::TranslateText('Voir boutique') }}</a>
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
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-bg-secondary2 {
            background-color: #EFB121;
            /* Couleur de fond, bleu dans cet exemple */
            color: #ffffff;
            /* Couleur du texte, blanc dans cet exemple */
            border: none;
            padding: 10px 20px;
            /* Optionnel, ajuste la taille */
            border-radius: 5px;
            /* Optionnel, arrondit les coins */
            text-decoration: none;
            /* Supprime le soulignement */
        }
    </style>
    <!-- End About Area  -->


    <!-- End Axil Newsletter Area  -->




</main>
@endsection