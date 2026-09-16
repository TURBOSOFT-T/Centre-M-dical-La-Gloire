@php
$config = DB::table('configs')->select('icon', 'logo')->first();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>

        {{ \App\Helpers\TranslationHelper::TranslateText('Reçu de commande') }}
    </title>
    <style>
        body {
            font-family: Arial, sans-serif, 'bangla';
        }

        .table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        table {
            width: 100% !important;
            text-align: left !important
        }

        thead {
            background-color: #416f10 !important;
            color: white !important;
        }
    </style>
</head>

<body>
    @foreach ($ids [] as $id_item)
    @php
    $commande = App\Models\commandes::find($id_item);
    @endphp
    @if ($commande)
    <div style="page-break-after: always;">
        <center>
            <img src="{{-- {{ config('app.app_url') }} --}}{{ asset(Storage::url($config->icon)) }}" alt="logo"
                height="70" srcset="">
        </center>
        <h2> {{ \App\Helpers\TranslationHelper::TranslateText('Numéro de commande') }} #{{ $commande->id }}</h2>

        {{-- ✅ QR Code --}}
        <center style="margin-bottom: 20px;">
            {!! QrCode::size(120)->generate(route('print_commande', $commande->reference)) !!}
            <div style="font-size: 13px; color: #333;">
                {{ \App\Helpers\TranslationHelper::TranslateText('Référence :') }}
                <strong>{{ $commande->reference }}</strong>
                <br>
                <em>{{ \App\Helpers\TranslationHelper::TranslateText('Scannez ce code pour consulter la commande en ligne') }}</em>
            </div>
        </center>
        <div>
            <br>
            <center>
                <h3>
                    {{ \App\Helpers\TranslationHelper::TranslateText('Informations sur le client') }}
                </h3>
            </center>
            <table>
                <tr>
                    <td>
                        <b> {{ \App\Helpers\TranslationHelper::TranslateText('Nom :') }} </b> {{ $commande->nom ?? '/' }} <br>
                        <b> {{ \App\Helpers\TranslationHelper::TranslateText('Prénom :') }}</b> {{ $commande->prenom ?? '/' }} <br>
                        <b> {{ \App\Helpers\TranslationHelper::TranslateText('Téléphone :') }}</b> {{ $commande->phone ?? '/' }} <br>
                        <b>
                    <th> {{ \App\Helpers\TranslationHelper::TranslateText('E-mail :') }}</th>
                    </b> {{ $commande->email ?? '/' }}
                    </td>
                    <td>
                        <b>
                    <th> {{ \App\Helpers\TranslationHelper::TranslateText('Adresse :') }}</th>
                    </b> {{ $commande->adresse ?? '/' }}<br>
                    {{-- <b>Pays :</b> {{ $commande->pays ?? '/' }}<br>
                    <b>Gouvernorat :</b> {{ $commande->gouvernorat ?? '/' }}
                    --}} </td>
                </tr>
            </table>
        </div>
        <br>
        <center>
            <h3>
                <th> {{ \App\Helpers\TranslationHelper::TranslateText('Informations de la commande') }}</th>
            </h3>
        </center>
        <div>
            <b>
                <th> {{ \App\Helpers\TranslationHelper::TranslateText('Date de reception de la commande :') }}</th>
            </b> {{ $commande->created_at ?? '/' }}
        </div>
        <center>
            <h3>

                <th> {{ \App\Helpers\TranslationHelper::TranslateText('Contenu de la commande') }}</th>
            </h3>
        </center>
        <table class="table">
            <thead>
                <tr>
                    <th> {{ \App\Helpers\TranslationHelper::TranslateText('Référence') }}</th>
                    <th> {{ \App\Helpers\TranslationHelper::TranslateText('Article') }}</th>
                    <th> {{ \App\Helpers\TranslationHelper::TranslateText('Prix unitaire') }}</th>
                    <th> {{ \App\Helpers\TranslationHelper::TranslateText('Quantité') }}</th>

                    <th> {{ \App\Helpers\TranslationHelper::TranslateText('Montant') }}</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($commande->contenus as $article)
                <tr>
                    <td>{{ $article->produit->reference }}</td>
                    <td>{{ $article->produit->nom }}</td>
                    <td>{{ $article->prix_unitaire }} <x-devise></x-devise></td>
                    <td>x{{ $article->quantite }}</td>
                    <td>{{ $article->prix_unitaire * $article->quantite }} <x-devise></x-devise></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <div>
            <b>
                <th> {{ \App\Helpers\TranslationHelper::TranslateText('Frais de livraison :') }}</th>
            </b> {{ $commande->frais ?? 0 }} <x-devise></x-devise> <br>
            <b>
                <th> {{ \App\Helpers\TranslationHelper::TranslateText('Montant net à payer :') }}</th>
            </b> {{ $commande->montant() ?? '/'-   $commande->coupon  ??  '/'  }} <x-devise></x-devise>
        </div>
        <br><br><br>
        <i>

            <th> {{ \App\Helpers\TranslationHelper::TranslateText('Merci pour votre commande !') }}</th>
        </i>
    </div>
    @endif
    @endforeach
</body>

</html>