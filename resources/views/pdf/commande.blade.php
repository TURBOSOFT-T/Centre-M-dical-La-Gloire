@php
use App\Helpers\TranslationHelper;

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

$devise = 'FCFA'; // Remplacez par votre devise si besoin
@endphp
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ TranslationHelper::TranslateText('Reçu de commande') }} - {{ $commande->reference }}</title>
    <style>
        /* Styles globaux pour la responsivité et l'affichage écran/PDF */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #fff;
            font-size: 14px;
            line-height: 1.4;
        }

        /* Conteneur principal fluide et centré pour s'adapter à tous les écrans et formats */
        .container {
            width: 100%;
            max-width: 750px;
            margin: 0 auto;
            padding: 15px;
            box-sizing: border-box;
            position: relative;
        }

        /* Filigrane anti-fraude discret */
        .watermark {
            position: absolute;
            top: 35%;
            left: 10%;
            width: 80%;
            text-align: center;
            opacity: 0.06;
            font-size: 70px;
            font-weight: bold;
            color: #000;
            transform: rotate(-30deg);
            z-index: 0;
            user-select: none;
            pointer-events: none;
        }

        /* En-tête de l'entreprise */
        .invoice-header {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .header-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        .company-details {
            text-align: left;
            font-size: 12px;
            line-height: 1.4;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .logo-cell {
            text-align: right;
        }

        .logo {
            max-width: 120px;
            height: auto;
        }

        /* Tableaux fluides */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
            background-color: transparent;
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            font-size: 13px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .tr-montant {
            color: #fff !important;
            background-color: #000 !important;
        }

        .tr-montant td {
            border: 1px solid #000;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        h5, h3, h4 {
            margin: 10px 0 5px 0;
        }

        p {
            margin: 5px 0;
        }

        hr {
            border: 0;
            border-top: 1px solid #ddd;
            margin: 15px 0;
        }

        /* Règle spécifique pour l'impression physique (évite les coupures moches) */
        @media print {
            body {
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .container {
                width: 100%;
                max-width: 100%;
                padding: 5px;
            }
            .watermark {
                opacity: 0.04;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Filigrane de sécurité -->
        <div class="watermark">
            {{ strtoupper(TranslationHelper::TranslateText($commande->statut ?? 'EN ATTENTE')) }}
        </div>

        <!-- En-tête de l'entreprise -->
        <div class="invoice-header">
            <table class="header-table">
                <tr>
                    <td class="company-details">
                        <div class="company-name">{{ config('app.name') }}</div>
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

                    <td class="logo-cell">
                        @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="logo" class="logo">
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <h5>{{ TranslationHelper::TranslateText('Reference de la commande') }} : {{ $commande->reference }}</h5>
        <p><strong>{{ TranslationHelper::TranslateText('Date de commande') }} :</strong> {{ $commande->created_at }}</p>

        <h3>{{ TranslationHelper::TranslateText('Produits commandés') }} :</h3>
        <table>
            <thead>
                <tr>
                    <th>{{ TranslationHelper::TranslateText('Produit') }}</th>
                    <th class="text-center">{{ TranslationHelper::TranslateText('Qté') }}</th>
                    <th class="text-right">{{ TranslationHelper::TranslateText('P.U') }}</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                $total = 0;
                @endphp
                @foreach ($commande->contenus as $item)
                <tr>
                    <td>{{ $item->produit->nom ?? 'Produit' }}</td>
                    <td class="text-center">{{ $item->quantite }}</td>
                    <td class="text-right">{{ number_format($item->prix_unitaire, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($item->prix_unitaire * $item->quantite, 0, ',', ' ') }}</td>
                </tr>
                @php
                $total += ($item->prix_unitaire * $item->quantite);
                @endphp
                @endforeach

                @if($commande->frais)
                <tr>
                    <td><b>{{ TranslationHelper::TranslateText('Frais de livraison') }}</b></td>
                    <td class="text-center">1</td>
                    <td class="text-right">{{ number_format($commande->frais, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($commande->frais, 0, ',', ' ') }}</td>
                </tr>
                @php $total += $commande->frais; @endphp
                @endif

                @if($commande->coupon)
                <tr>
                    <td><b>{{ TranslationHelper::TranslateText('Coupon de réduction') }}</b></td>
                    <td class="text-center">1</td>
                    <td class="text-right">{{ number_format($commande->coupon, 0, ',', ' ') }}</td>
                    <td class="text-right" style="color: #dc3545;">-{{ number_format($commande->coupon, 0, ',', ' ') }}</td>
                </tr>
                @php $total -= $commande->coupon; @endphp
                @endif

                <tr class="tr-montant">
                    <td colspan="3" class="text-right">
                        <b>{{ TranslationHelper::TranslateText('Total') }} ({{ $devise }}) :</b>
                    </td>
                    <td class="text-right">
                        <b>{{ number_format($total, 0, ',', ' ') }}</b>
                    </td>
                </tr>
            </tbody>
        </table>

        <h4>{{ TranslationHelper::TranslateText('Informations sur la livraison') }} :</h4>
        <p><strong>{{ TranslationHelper::TranslateText('Nom complet') }} :</strong> {{ $commande->prenom }} {{ $commande->nom }}</p>

        @if($commande->adresse)
        <p><strong>{{ TranslationHelper::TranslateText('Adresse') }} :</strong> {{ $commande->adresse }}</p>
        @endif

        <p><strong>{{ TranslationHelper::TranslateText('Numéro de téléphone') }} :</strong> {{ $commande->phone ?? 'N/A' }}</p>

        @if($commande->gouvernorat)
        <p><strong>{{ TranslationHelper::TranslateTest ?? TranslationHelper::TranslateText('Ville') }} :</strong> {{ $commande->gouvernorat }}</p>
        @endif

        <hr>

        <p class="text-center" style="font-size: 12px;">
            {{ TranslationHelper::TranslateText('Merci de votre confiance') }} !
            <br>
            {{ TranslationHelper::TranslateText("Si vous avez des questions, n'hésitez pas à nous contacter") }}.
        </p>
    </div>
</body>

</html>