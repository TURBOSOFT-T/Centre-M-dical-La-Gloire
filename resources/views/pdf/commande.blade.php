@php
$config = DB::table('configs')->select('icon', 'logo', 'telephone', 'email', 'addresse')->first();

// Détermination de l'image à utiliser pour le logo
$logoPath = public_path('/icons/logo.jpg');
if ($config && !empty($config->logo) && file_exists(storage_path('app/public/' . $config->logo))) {
$logoPath = storage_path('app/public/' . $config->logo);
} elseif ($config && !empty($config->icon) && file_exists(storage_path('app/public/' . $config->icon))) {
$logoPath = storage_path('app/public/' . $config->icon);
}

$logoBase64 = '';
if (file_exists($logoPath)) {
$logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($logoPath));
}
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
            font-family: Arial, sans-serif;
            margin: 0;
            color: #333;
            position: relative;
        }

        /* Style du filigrane anti-fraude */
        .watermark {
            position: fixed;
            top: 40%;
            left: 5%;
            width: 90%;
            text-align: center;
            opacity: 0.08;
            font-size: 110px;
            font-weight: bold;
            color: #000000;
            transform: rotate(-35deg);
            transform-origin: 50% 50%;
            z-index: -1000;
            user-select: none;
        }

        .container {
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        /* Styles de l'en-tête de l'entreprise */
        .invoice-header {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .company-details {
            text-align: left;
            /* Aligné à gauche */
            font-size: 13px;
            line-height: 1.5;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .logo-cell {
            text-align: right;
            /* Aligne le logo à droite */
        }

        .logo {
            max-width: 150px;
            height: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: transparent;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .tr-montant {
            color: white !important;
            background-color: #000000;
        }

        .tr-montant td {
            border: 1px solid #000000;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        hr {
            border: 0;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <!-- Filigrane de sécurité -->
    <div class="watermark">
        {{ strtoupper(\App\Helpers\TranslationHelper::TranslateText($commande->statut ?? 'EN ATTENTE')) }}
    </div>

    <div class="container">
        <!-- En-tête de l'entreprise -->
        <div class="invoice-header">
            <table class="header-table">
                <tr>
                    <!-- Informations de l'entreprise à gauche -->
                    <td class="company-details">
                        <div class="company-name">{{ config('app.name') }}, {{ \App\Helpers\TranslationHelper::TranslateText('notre boutique') }}</div>
                        @if($config)

                        @if(!empty($config->telephone))
                        <div><strong>Tél :</strong> {{ $config->telephone }}</div>
                        @endif
                        @if(!empty($config->email))
                        <div><strong>Email :</strong> {{ $config->email }}</div>
                        @endif
                        @if(!empty($config->addresse))
                        <div><strong>Adresse :</strong> {{ $config->addresse }}</div>
                        @endif
                        @endif
                    </td>

                    <!-- Logo à droite -->
                    <td class="logo-cell">
                        @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="logo" class="logo">
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- <h1>
            {{ \App\Helpers\TranslationHelper::TranslateText('Reçu de commande') }}
        </h1> -->
        <h5>{{ \App\Helpers\TranslationHelper::TranslateText('Reference de la commande') }}: {{ $commande->reference }}</h5>
        <p><strong>{{ \App\Helpers\TranslationHelper::TranslateText('Date de commande') }}:</strong> {{ $commande->created_at }}</p>

        <h3>{{ \App\Helpers\TranslationHelper::TranslateText('Produits commandés') }} :</h3>
        <table>
            <thead>
                <tr>
                    <th>{{ \App\Helpers\TranslationHelper::TranslateText('Produit') }}</th>
                    <th>{{ \App\Helpers\TranslationHelper::TranslateText('Quantité') }}</th>
                    <th>{{ \App\Helpers\TranslationHelper::TranslateText('Prix unitaire') }}</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                $total = 0;
                @endphp
                @foreach ($commande->contenus as $item)
                <tr>
                    <td>{{ $item->produit->nom }}</td>
                    <td>{{ $item->quantite }}</td>
                    <td>{{ $item->prix_unitaire }} <x-devise></x-devise></td>
                    <td>{{ $item->prix_unitaire * $item->quantite }} <x-devise></x-devise></td>
                </tr>
                @php
                $total += ($item->prix_unitaire * $item->quantite);
                @endphp
                @endforeach

                @if($commande->frais)
                <tr>
                    <td><b>{{ \App\Helpers\TranslationHelper::TranslateText('Frais de livraison') }}</b></td>
                    <td>1</td>
                    <td>{{ $commande->frais }} <x-devise></x-devise></td>
                    <td>{{ $commande->frais }} <x-devise></x-devise></td>
                </tr>
                @php $total += $commande->frais; @endphp
                @endif

                @if($commande->coupon)
                <tr>
                    <td><b>{{ \App\Helpers\TranslationHelper::TranslateText('Coupon de réduction') }}</b></td>
                    <td>1</td>
                    <td>{{ $commande->coupon }} <x-devise></x-devise></td>
                    <td style="color: #dc3545;">-{{ $commande->coupon }} <x-devise></x-devise></td>
                </tr>
                @php $total -= $commande->coupon; @endphp
                @endif

                <tr class="tr-montant">
                    <td colspan="3" class="text-right">
                        <b>{{ \App\Helpers\TranslationHelper::TranslateText('Total de la commande') }}:</b>
                    </td>
                    <td>
                        <b>{{ $total }} <x-devise></x-devise></b>
                    </td>
                </tr>
            </tbody>
        </table>

        <h4>{{ \App\Helpers\TranslationHelper::TranslateText('Informations sur la livraison') }} :</h4>
        <p><strong>{{ \App\Helpers\TranslationHelper::TranslateText('Nom complet') }}:</strong> {{ $commande->prenom }} {{ $commande->nom }}</p>

        @if($commande->adresse)
        <p><strong>{{ \App\Helpers\TranslationHelper::TranslateText('Adresse') }}:</strong> {{ $commande->adresse }}</p>
        @endif

        <p><strong>{{ \App\Helpers\TranslationHelper::TranslateText('Numéro de téléphone') }}:</strong> {{ $commande->phone ?? 'N/A' }}</p>

        @if($commande->gouvernorat)
        <p><strong>{{ \App\Helpers\TranslationHelper::TranslateText('Ville') }}:</strong> {{ $commande->gouvernorat }}</p>
        @endif

        <hr>

        <p>
            {{ \App\Helpers\TranslationHelper::TranslateText('Merci de votre confiance') }}!
            <br>
            {{ \App\Helpers\TranslationHelper::TranslateText('Si vous avez des questions ou des préoccupations, n\'hésitez pas à nous contacter') }}.
        </p>
    </div>
</body>

</html>