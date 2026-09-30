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
        /* Styles optimisés pour imprimante Epson et police 14px */
        body {
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 0;
            color: #000;
            background-color: #fff;
            font-size: 14px; /* Fixé à 14px comme demandé */
            line-height: 1.3;
        }

        /* Format rouleau thermique 80mm */
        .container {
            width: 72mm;
            margin: 0 auto;
            padding: 5px;
            box-sizing: border-box;
            position: relative;
        }

        /* Statut mis en valeur (façon filigrane thermique) */
        .status-box {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            border: 2px dashed #000;
            padding: 6px;
            margin: 10px 0;
            letter-spacing: 1px;
            background-color: #f0f0f0;
        }

        .invoice-header {
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
            text-align: center;
        }

        .logo {
            max-width: 80px;
            height: auto;
            display: block;
            margin: 0 auto 5px auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        th, td {
            border: none;
            border-bottom: 1px dotted #ccc;
            padding: 5px 2px;
            font-size: 13px; /* Proportionnel à 14px pour tenir dans le tableau */
        }

        th {
            background-color: transparent;
            border-bottom: 1px solid #000;
            font-weight: bold;
            font-size: 13px;
        }

        .tr-montant {
            color: #000 !important;
            background-color: transparent !important;
            border-top: 2px solid #000 !important;
        }

        .tr-montant td {
            border: none;
            font-size: 15px;
            font-weight: bold;
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

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 0;
            }
            .container {
                width: 100%;
                padding: 0;
            }
            .status-box {
                background-color: #fff !important;
                border: 2px solid #000;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- En-tête de l'entreprise -->
        <div class="invoice-header">
            @if(!empty($logoBase64))
            <img src="{{ $logoBase64 }}" alt="logo" class="logo">
            @endif
            <div style="font-size: 15px; font-weight: bold;">{{ config('app.name') }}</div>
            @if($config)
                @if(!empty($config->addresse))
                <div style="font-size: 11px;">{{ $config->addresse }}</div>
                @endif
                @if(!empty($config->telephone))
                <div style="font-size: 11px;">Tél : {{ $config->telephone }}</div>
                @endif
            @endif
        </div>

        <!-- Statut mis en valeur -->
        <div class="status-box">
            {{ strtoupper(TranslationHelper::TranslateText($commande->statut ?? 'EN ATTENTE')) }}
        </div>

        <!-- Informations globales de la commande -->
        <p><strong>{{ TranslationHelper::TranslateText('Reference') }} :</strong> {{ $commande->reference }}</p>
        <p><strong>{{ TranslationHelper::TranslateText('Date') }} :</strong> {{ $commande->created_at }}</p>

        <!-- Liste de tous les produits commandés -->
        <h3 style="border-bottom: 1px solid #000; padding-bottom: 2px;">{{ TranslationHelper::TranslateText('Produits commandés') }} :</h3>
        <table>
            <colgroup>
                <col style="width: 30%;">
                <col style="width: 15%;">
                <col style="width: 25%;">
                <col style="width: 30%;">
            </colgroup>
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
                    <td><b>{{ TranslationHelper::TranslateText('Coupon') }}</b></td>
                    <td class="text-center">1</td>
                    <td class="text-right">{{ number_format($commande->coupon, 0, ',', ' ') }}</td>
                    <td class="text-right">-{{ number_format($commande->coupon, 0, ',', ' ') }}</td>
                </tr>
                @php $total -= $commande->coupon; @endphp
                @endif

                <tr class="tr-montant">
                    <td colspan="3" class="text-right">
                        <b>TOTAL ({{ $devise }}) :</b>
                    </td>
                    <td class="text-right">
                        <b>{{ number_format($total, 0, ',', ' ') }}</b>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Informations complètes sur le client et la livraison -->
        <h4 style="border-bottom: 1px solid #000; padding-bottom: 2px;">{{ TranslationHelper::TranslateText('Client / Livraison') }} :</h4>
        
        <p><strong>Nom :</strong> {{ $commande->prenom ?? '' }} {{ $commande->nom ?? '' }}</p>

        @if(!empty($commande->email))
        <p><strong>Email :</strong> {{ $commande->email }}</p>
        @endif

        <p><strong>Tél :</strong> {{ $commande->phone ?? ($commande->telephone ?? 'N/A') }}</p>

        @if(!empty($commande->adresse))
        <p><strong>Adresse :</strong> {{ $commande->adresse }}</p>
        @endif

        @if(!empty($commande->gouvernorat))
        <p><strong>Ville :</strong> {{ $commande->gouvernorat }}</p>
        @endif

        @if(!empty($commande->notes))
        <p><strong>Notes :</strong> {{ $commande->notes }}</p>
        @endif

        <hr>

        <p class="text-center" style="font-size: 13px;">
            {{ TranslationHelper::TranslateText('Merci de votre confiance') }} !
            <br>
            {{ TranslationHelper::TranslateText("À bientôt") }}.
        </p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>