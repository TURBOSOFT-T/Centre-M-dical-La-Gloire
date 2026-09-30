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

$devise = 'FCFA';
@endphp
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ TranslationHelper::TranslateText('Reçu de commande') }} - {{ $commande->reference }}</title>
    <style>
        /* Styles optimisés pour imprimante Epson et police 14 */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
            background-color: #fff;
            font-size: 14px;
            line-height: 1.5;
        }

        .container {
            width: 100%;
            max-width: 750px;
            margin: 0 auto;
            padding: 10px;
            box-sizing: border-box;
        }

        /* En-tête simplifié pour ticket/reçu */
        .invoice-header {
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
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
            font-size: 14px;
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
            max-width: 100px;
            height: auto;
            filter: grayscale(100%) contrast(200%);
        }

        /* Tableaux adaptés pour impression thermique */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #eee !important;
            font-weight: bold;
            color: #000;
        }

        .tr-montant {
            color: #000 !important;
            background-color: #eee !important;
            font-weight: bold;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        h5, h3, h4 {
            margin: 8px 0 4px 0;
            font-size: 14px;
        }

        p {
            margin: 4px 0;
            font-size: 14px;
        }

        hr {
            border: 0;
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        @media print {
            body {
                width: 100%;
                margin: 0;
                padding: 0;
            }
            .container {
                width: 100%;
                max-width: 100%;
                padding: 0;
            }
        }
    </style>
</head>

<body>

    <div class="container">
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

        <!-- Informations globales de la commande -->
        <h5><strong>{{ TranslationHelper::TranslateText('Reference de la commande') }} :</strong> {{ $commande->reference }}</h5>
        <p><strong>{{ TranslationHelper::TranslateText('Date de commande') }} :</strong> {{ $commande->created_at }}</p>
        <p><strong>{{ TranslationHelper::TranslateText('Statut') }} :</strong> {{ $commande->statut ?? 'EN ATTENTE' }}</p>

        <!-- Liste de tous les produits commandés -->
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

                @if(isset($commande->frais) && $commande->frais > 0)
                <tr>
                    <td><b>{{ TranslationHelper::TranslateText('Frais de livraison') }}</b></td>
                    <td class="text-center">1</td>
                    <td class="text-right">{{ number_format($commande->frais, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($commande->frais, 0, ',', ' ') }}</td>
                </tr>
                @php $total += $commande->frais; @endphp
                @endif

                @if(isset($commande->coupon) && $commande->coupon > 0)
                <tr>
                    <td><b>{{ TranslationHelper::TranslateText('Coupon de réduction') }}</b></td>
                    <td class="text-center">1</td>
                    <td class="text-right">{{ number_format($commande->coupon, 0, ',', ' ') }}</td>
                    <td class="text-right">-{{ number_format($commande->coupon, 0, ',', ' ') }}</td>
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

        <!-- Informations complètes sur le client et la livraison -->
        <h4>{{ TranslationHelper::TranslateText('Informations sur la livraison') }} :</h4>
        
        <p><strong>{{ TranslationHelper::TranslateText('Nom complet') }} :</strong> {{ $commande->prenom ?? '' }} {{ $commande->nom ?? '' }}</p>

        @if(!empty($commande->email))
        <p><strong>{{ TranslationHelper::TranslateText('Email') }} :</strong> {{ $commande->email }}</p>
        @endif

        <p><strong>{{ TranslationHelper::TranslateText('Numéro de téléphone') }} :</strong> {{ $commande->phone ?? ($commande->telephone ?? 'N/A') }}</p>

        @if(!empty($commande->adresse))
        <p><strong>{{ TranslationHelper::TranslateText('Adresse') }} :</strong> {{ $commande->adresse }}</p>
        @endif

        @if(!empty($commande->gouvernorat))
        <p><strong>{{ TranslationHelper::TranslateText('Ville / Gouvernorat') }} :</strong> {{ $commande->gouvernorat }}</p>
        @endif

        @if(!empty($commande->notes))
        <p><strong>{{ TranslationHelper::TranslateText('Notes') }} :</strong> {{ $commande->notes }}</p>
        @endif

        <hr>

        <p class="text-center">
            {{ TranslationHelper::TranslateText('Merci de votre confiance') }} !
            <br>
            {{ TranslationHelper::TranslateText("Si vous avez des questions, n'hésitez pas à nous contacter") }}.
        </p>
    </div>
</body>

</html>