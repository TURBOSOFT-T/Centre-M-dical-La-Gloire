@extends('front.fixe')
@section('titre', "Félicitation pour votre commande")
@section('body')

<main> 
        <br><br><br><br>
        <div class="container">
            <div class="row">
                <div class="col-ms-6 mx-auto">
                    <div class="mt-5 mb-5">
                        <div class="card-body card-bodyx text-center ">
                            <h5 class="text-success">
                                <div>
                                  {{--   
                                    <img width="96" height="96" src="https://img.icons8.com/pulsar-line/96/578b07/order-completed.png" alt="order-completed"/>
                              --}} 
                              <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="150" height="150" viewBox="0 0 48 48">
                                <linearGradient id="5zzMGVQnN_QyRYWGmJUsQa_A8xKzsTKHhzn_gr1" x1="9.858" x2="38.142" y1="9.858" y2="38.142" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#21ad64"></stop><stop offset="1" stop-color="#088242"></stop></linearGradient><path fill="url(#5zzMGVQnN_QyRYWGmJUsQa_A8xKzsTKHhzn_gr1)" d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z"></path><path d="M32.172,16.172L22,26.344l-5.172-5.172c-0.781-0.781-2.047-0.781-2.828,0l-1.414,1.414	c-0.781,0.781-0.781,2.047,0,2.828l8,8c0.781,0.781,2.047,0.781,2.828,0l13-13c0.781-0.781,0.781-2.047,0-2.828L35,16.172	C34.219,15.391,32.953,15.391,32.172,16.172z" opacity=".05"></path><path d="M20.939,33.061l-8-8c-0.586-0.586-0.586-1.536,0-2.121l1.414-1.414c0.586-0.586,1.536-0.586,2.121,0	L22,27.051l10.525-10.525c0.586-0.586,1.536-0.586,2.121,0l1.414,1.414c0.586,0.586,0.586,1.536,0,2.121l-13,13	C22.475,33.646,21.525,33.646,20.939,33.061z" opacity=".07"></path><path fill="#fff" d="M21.293,32.707l-8-8c-0.391-0.391-0.391-1.024,0-1.414l1.414-1.414c0.391-0.391,1.024-0.391,1.414,0	L22,27.758l10.879-10.879c0.391-0.391,1.024-0.391,1.414,0l1.414,1.414c0.391,0.391,0.391,1.024,0,1.414l-13,13	C22.317,33.098,21.683,33.098,21.293,32.707z"></path>
                                </svg>
              
                            
                            </div>
                                <b>
                                 <h3>  
                                    {{ \App\Helpers\TranslationHelper::TranslateText('Félicitation pour votre commande') }} !
                                    </h3>  
                                </b>
                            </h5>
                            
                            <p>
                                <h3> 
                                    {{ \App\Helpers\TranslationHelper::TranslateText('Votre commande a été enregistrée avec succès') }}
                                </h3>
                            </p>
                           
                        {{--     <div class="order-id">
                                <a href="/shop" class="btn btn"  style=" background-color: #b2e21522;">
                                    Continuer les achats
                                </a>
                            </div> --}}
                            <style>
                                .btn-small-width {
                                    width: 300px; /* Ajustez la largeur selon vos besoins */
                                }
                            </style>
                            
                            <div class="form-group mb--0">
                                <a href="/shop" class="axil-btn btn-bg-primary2 btn-small-width">
                                    <span> 
                                        {{ \App\Helpers\TranslationHelper::TranslateText('Continuer les achats') }}
                                    </span>
                                </a>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br><br><br><br>

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
            background-color: #EFB121; /* Couleur de fond, bleu dans cet exemple */
            color: #ffffff; /* Couleur du texte, blanc dans cet exemple */
            border: none;
            padding: 10px 20px; /* Optionnel, ajuste la taille */
            border-radius: 5px; /* Optionnel, arrondit les coins */
            text-decoration: none; /* Supprime le soulignement */
        }
        </style>
    </main>

    <style>
        .card-bodyx{
            margin-bottom: 20px;
            margin-top: 20px;
        }
    </style>
@endsection
