@extends('front.fixe')
@section('titre', 'Accueil')
@section('body')


<main class="main-wrapper">


    <style>
        .axil-breadcrumb-item1 {
            font-size: 14px;
            color: #EFB121;

        }

        .axil-breadcrumb-item.active {
            font-weight: bold;
            color: #EFB121;

        }

        .axil-breadcrumb-item:not(.active)::after {
            content: " / ";

            color: #EFB121;
        }
    </style>








 
    <!-- End Expolre Product Area  -->
    @include('front.components.testimonials')
    <br>
    <br>
  

    @livewire('live-chat')
</main>


@endsection