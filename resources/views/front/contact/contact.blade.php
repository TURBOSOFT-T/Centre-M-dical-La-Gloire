@extends('front.fixe')
@section('titre', "Contact")
@section('body')
<main>


    <!-- Start Breadcrumb Area  -->
    <div class="axil-breadcrumb-area">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-lg-6 col-md-8">
                    <div class="inner">
                        <ul class="axil-breadcrumb">
                            <li class="axil-breadcrumb-item"><a href="{{ route('home') }}"> {{ \App\Helpers\TranslationHelper::TranslateText('Accueil') }}</a></li>
                            <li class="separator"></li>
                            <li class="axil-breadcrumb-item1 active" aria-current="page"> {{ \App\Helpers\TranslationHelper::TranslateText('Contact') }}</li>
                        </ul>

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
                        <h1 class="title">
                            {{ \App\Helpers\TranslationHelper::TranslateText('Contactez-nous') }}
                        </h1>
                    </div>
                </div>
                <div class="col-lg-6 col-md-4">
                   
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb Area  -->

    <!-- Start Contact Area  -->
    <div class="axil-contact-page-area axil-section-gap">
        <div class="container">
            <div class="axil-contact-page">
                <div class="row row--30">
                    <div class="col-lg-8">
                        <div class="contact-form">
                            <h3 class="title mb--10">
                                {{ \App\Helpers\TranslationHelper::TranslateText('Partagez votre énergie avec nous !') }}
                            </h3>

                            <p>
                                {{ \App\Helpers\TranslationHelper::TranslateText('Une question sur nos produits bio, une envie de collaborer ou simplement besoin d\'un conseil pour faire le plein d\'énergie pure ? Écrivez-nous, notre équipe est là pour vous.') }}
                            </p>

                            @livewire('Front.ContactForm')
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="contact-location mb--40">
                            <h4 class="title mb--20">
                                {{ \App\Helpers\TranslationHelper::TranslateText('Notre magasin') }}
                            </h4>
                            <span class="address mb--20"> {{ $config->addresse ?? ' ' }}</span>
                            <span class="phone">Télphone: {{ $config->telephone ?? ' ' }}</span>
                            <span class="email">Email: {{ $config->email ?? ' ' }}</span>
                        </div>

                        <div class="opening-hour">
                            <h4 class="title mb--20">
                                {{ \App\Helpers\TranslationHelper::TranslateText('Horaires ouverture') }}:
                            </h4>
                            <p>
                                {{ \App\Helpers\TranslationHelper::TranslateText('Du lundi  au samedi: 9h00-22h') }}

                                <br>
                                {{ \App\Helpers\TranslationHelper::TranslateText('Dimanche : 10h00 - 18h00') }}

                            </p>
                        </div>
                    </div>
                </div>
            </div>
         
        </div>
    </div>
    <!-- End Contact Area  -->


</main>
@endsection