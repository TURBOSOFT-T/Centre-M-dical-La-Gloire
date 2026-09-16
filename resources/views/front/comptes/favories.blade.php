@extends('front.fixe')
@section('titre', 'Mes Favoris')
@section('body')

<main>
    <main class="main-wrapper">

        
        <div class="axil-wishlist-area axil-section-gap">
            <div class="container">
                <div class="product-table-heading">
                    <h4 class="title">
                        {{ \App\Helpers\TranslationHelper::TranslateText('List de mes favoris sur SWOOT BIO') }}
                    </h4>
                </div>
                @livewire('Front.Favoris')
          
            </div>
        </div>
       
    </main>

</main>
@endsection
