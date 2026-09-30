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
        /* Styles universels de base */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
            background-color: #fff;
            font-size: 14px;
            line-height: 1.4;
        }

        /* Conteneur fluide par défaut (pour écran et A4) */
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 15px;
            box-sizing: border-box;
            position: relative;
        }

        /* Statut mis en valeur */
        .status-box {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            border: 2px dashed #000;
            padding: 8px;
            margin: 12px 0;
            letter-spacing: 1px;
            background-color: #f9f9f9;
        }

        .invoice-header {
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            text-align: center;
        }

        .logo {
            max-width: 100px;
            height: auto;
            display: block;
            margin: 0 auto 8px auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 14px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .tr-montant {
            background-color: #000 !important;
            color: #fff !important;
        }

        .tr-montant td {
            border: 1px solid #000;
            font-size: 15px;
            font-weight: bold;
        }

        h5, h3, h4 {
            margin: 12px 0 6px 0;
            font-size: 15px;
        }

        p {
            margin: 6px 0;
            font-size: 14px;
        }

        hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 15px 0;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        /* ==========================================================
           MAGIE DE L'IMPRESSION UNIVERSELLE (@media print)
           ================================================---------- */
        @media print {
            /* Si l'utilisateur a configuré son imprimante sur du format ticket (80mm) */
            @page {
                margin: 0;
                size: auto; 
            }

            body {
                width: 100%;
                margin: 0;
                padding: 0;
                font-family: 'Courier New', Courier, monospace; /* Rendu thermique net si imprimante de caisse */
            }

            .container {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 5px !important;
            }

            /* Sur thermique, on allège les bordures lourdes des tableaux A4 pour un look ticket épuré */
            th, td {
                border: none !important;
                border-bottom: 1px dotted #ccc !important;
                padding: 5px 2px !important;
            }

            th {
                border-bottom: 1px solid #000 !important;
                background-color: transparent !important;
            }

            .tr-montant {
                background-color: transparent !important;
                color: #000 !important;
                border-top: 2px solid #000 !important;
            }

            .tr-montant td {
                border: none !important;
            }

            .status-box {
                background-color: #fff !important;
                border: 2px solid #000 !important;
            }

            hr {
                border-top: 1px dashed #000 !important;
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
            <div style="font-size: 18px; font-weight: bold;">{{ config('app.name') }}</div>
            @if($config)
                @if(!empty($config->addresse))
                <div style="font-size: 13px;">{{ $config->addresse }}</div>
                @endif
                @if(!empty($config->telephone))
                <div style="font-size: 13px;">Tél : {{ $config->telephone }}</div>
                @endif
                @if(!empty($config->email))
                <div style="font-size: 13px;">Email : {{ $config->email }}</div>
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
        <h3 style="border-bottom: 1px solid #000; padding-bottom: 3px;">{{ TranslationHelper::TranslateText('Produits commandés') }}</h3>
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
        <h4 style="border-bottom: 1px solid #000; padding-bottom: 3px;">{{ TranslationHelper::TranslateText('Client / Livraison') }}</h4>
        
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