@extends('front.fixe')
@section('titre',"  {{ \App\Helpers\TranslationHelper::TranslateText('Mot de passe oublié') }}")
@section('body')
    <main>

        <!-- error area start -->
        <div class="tp-error-area pt-150 pb-150">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6 mx-auto">
                        <div class="tp-error-content-box text-center">
                            @livewire('Front.ForgetPassword')
                        </div>
       
             
             
             
                    </div>
                </div>
            </div>
        </div>
        <!-- error area end -->
    </main>


@endsection
