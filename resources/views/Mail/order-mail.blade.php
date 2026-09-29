<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        {{ \App\Helpers\TranslationHelper::TranslateText('Confirmation de la commande') }}
    </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        th {
            text-align: left;
        }

        .total {
            font-weight: bold;
        }

        .produit {
            height: 70px !important;
            height: 70px !important;
            border-radius: 10px;
            overflow: hidden;
        }

        .produit img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div class="container">
       {{--  <img src="https://agrihub.online/icons/logo%20png.png" alt="logo" height="50" srcset=""> --}}
        <h2>
            {{ \App\Helpers\TranslationHelper::TranslateText('Confirmation de la commande') }}[ #{{ $order->id}} ] </h2>
        <p>Salut, {{ $order->prenom }} {{ $order->nom }},</p>
        <p>
            {{ \App\Helpers\TranslationHelper::TranslateText('Nous avons réçu votre commande. Voici les détails:') }}:
        </p>
        <table>
            <thead>
                <tr>
                    <th></th>
                 
                    <th> {{ \App\Helpers\TranslationHelper::TranslateText('Produit') }}</th>
                    <th> {{ \App\Helpers\TranslationHelper::TranslateText('Coût') }}</th>
                    <th> {{ \App\Helpers\TranslationHelper::TranslateText('Qté') }}</th>
                    <th>Sous Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                  //  $frais_livraison = $config->frais_livraison ?? 0;
                
                  $config = DB::table('configs')->first();
                 $frais_livraison = $config->frais ?? 0;
                    $total = $frais_livraison;
                   // dd($frais_livraison);
                @endphp

                @foreach ($order->contenus as $item)
                    <tr>
                        <td>
                            <div class="produit">
                                <img src="{{ url(Storage::url($item->produit->photo)) }}"
                                    alt="{{ $item->produit->nom }}" srcset="">
                            </div>
                        </td>
                        <td>{{ $item->produit->nom }}</td>
                        <td>{{ $item->prix_unitaire }}  <x-devise></x-devise></td>
                        <td>{{ $item->quantite }}</td>
                        <td>{{ $item->prix_unitaire * $item->quantite }}  <x-devise></x-devise></td>
                    </tr>
                    @php
                          $total += $item->prix_unitaire * $item->quantite - $order->coupon;
                    @endphp
                @endforeach

                <tr>
                    <td></td>
                    <td> {{ \App\Helpers\TranslationHelper::TranslateText('Frais de livraison') }}</td>
                    <td> {{ $frais_livraison }} </td>
                    <td>01</td>
                    <th> {{ $frais_livraison }} </th>
                </tr>

           
                @if($order->coupon ?? 0)
                <tr>
                    <td></td>
                    <td>
                        <b> {{ \App\Helpers\TranslationHelper::TranslateText('Couponde réduction') }} </b>
                    </td>
                    
                    <td> {{ $order->coupon ?? 0 }}  <x-devise></x-devise></td>
                    <td>1</td>
                    <td> -{{ $order->coupon ?? 0 }}  <x-devise></x-devise> </td>
                </tr>
                    
                @endif
                <tr class="total">
                    <td colspan="4"><strong>Total</strong></td>
                    <td><strong>{{ $total }}  <x-devise></x-devise></strong></td>
                </tr>
            </tbody>
        </table>
        <p>
          
            {{ \App\Helpers\TranslationHelper::TranslateText('  Votre commande sera bientôt traitée') }}. <br>
            {{ \App\Helpers\TranslationHelper::TranslateText(' Merci d\'avoir magasiné avec nous!') }}
            
           
        </p>
    </div>
</body>

</html>
