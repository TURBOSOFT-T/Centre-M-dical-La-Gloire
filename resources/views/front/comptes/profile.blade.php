@extends('front.fixe')
@section('titre', 'Paramètres ')
@section('body')





<main>

  
       
    <div class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"> {{ \App\Helpers\TranslationHelper::TranslateText('Accueil') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page"> {{ \App\Helpers\TranslationHelper::TranslateText('Compte') }}</li>
                    <li class="breadcrumb-item active" aria-current="page"> {{ \App\Helpers\TranslationHelper::TranslateText('Paramètres') }}</li>
                </ol>
            </nav>
        </div>
    </div>
        <div class="container-xl px-4 mt-4">
        <div class="row">

            <div class="col-xl-7">
                
                <div class=" mb-4">
                    <div class="card-header"> {{ \App\Helpers\TranslationHelper::TranslateText('Profile') }}</div>
                    <br>
                    <div class="">
                        @livewire('Profiles.Information')
                    </div>
                </div>
            </div>


            <div class="col">

                <div class="col-xl-24">
                   
                    <div class=" mb-4">
                        <div class="card-header primary">  {{ \App\Helpers\TranslationHelper::TranslateText('Sécurité') }}</div>
                        <br>
                        <div class="">
                            @livewire('Profiles.UpdateProfile')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            body {
          
            }

         
            .card {
                box-shadow: 0 0.15rem 1.75rem 0 rgb(33 40 50 / 15%);
            }

            .card .card-header {
                font-weight: 500;
            }

            .card-header:first-child {
                border-radius: 0.35rem 0.35rem 0 0;
            }

            .card-header {
                padding: 1rem 1.35rem;
                margin-bottom: 0;
              
                 background-color: #b2e21522;
               /*  background-color: rgba(239, 134, 14, 0.03); */
                border-bottom: 1px solid rgba(221, 119, 23, 0.125);
            }
            
            .form-control,
            .dataTable-input {
                display: block;
                width: 100%;
                padding: 0.875rem 1.125rem;
                font-size: 0.875rem;
                font-weight: 400;
                line-height: 1;
                color: #e4e9ee;
                background-color: #fff;
                background-clip: padding-box;
                border: 1px solid #c5ccd6;
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                border-radius: 0.35rem;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }

      

            .nav-borders .nav-link {
                color: #0b0b0b;
                border-bottom-width: 0.125rem;
                border-bottom-style: solid;
                border-bottom-color: transparent;
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
                padding-left: 0;
                padding-right: 0;
                margin-left: 1rem;
                margin-right: 1rem;
            }
        </style>
       
    </div> 
    <br>
</main>


@endsection

