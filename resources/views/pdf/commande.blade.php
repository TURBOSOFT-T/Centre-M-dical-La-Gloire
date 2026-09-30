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
        /* Optimisation exclusive pour petits reçus / imprimante thermique */
        body {
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 0;
            color: #000;
            background-color: #fff;
            font-size: 14px; /* Taille demandée */
            line-height: 1.3;
        }

        .container {
            width: 72mm; /* Largeur standard rouleau 80mm avec marges de sécurité */
            margin: 0 auto;
            padding: 4px;
            box-sizing: border-box;
        }

        /* Statut en gros format thermique */
        .status-box {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            border: 2px dashed #000;
            padding: 6px;
            margin: 8px 0;
            letter-spacing: 1px;
        }

        .invoice-header {
            width: 100%;
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            padding-bottom: 6px;
            text-align: center;
        }

        .logo {
            max-width: 75px;
            height: auto;
            display: block;
            margin: 0 auto 4px auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 8px;
        }

        th, td {
            border: none;
            border-bottom: 1px dotted #ccc;
            padding: 5px 2px;
            font-size: 13px;
            text-align: left;
        }

        th {
            border-bottom: 1px solid #000;
            font-weight: bold;
            font-size: 13px;
        }

        .tr-montant {
            border-top: 2px solid #000 !important;
        }

        .tr-montant td {
            border: none;
            font-size: 15px;
            font-weight: bold;
        }

        h5, h3, h4 {
            margin: 8px 0 3px 0;
            font-size: 14px;
        }

        p {
            margin: 4px 0;
            font-size: 14px;
        }

        hr {
            border: 0;
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        @media print {
            @page {
                margin: 0;
                size: 80mm auto; /* Forçage du format rouleau thermique */
            }
            body {
                width: 80mm;
                margin: 0;
                padding: 0;
            }
            .container {
                width: 100%;
                padding: 2px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- En-tête -->
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

        <!-- Statut du reçu -->
        <div class="status-box">
            {{ strtoupper(TranslationHelper::TranslateText($commande->statut ?? 'EN ATTENTE')) }}
        </div>

        <!-- Infos commande -->
        <p><strong>{{ TranslationHelper::TranslateText('Reference') }} :</strong> {{ $commande->reference }}</p>
        <p><strong>{{ TranslationHelper::TranslateText('Date') }} :</strong> {{ $commande->created_at }}</p>

        <!-- Tableau des produits -->
        <h3 style="border-bottom: 1px solid #000; padding-bottom: 2px;">{{ TranslationHelper::TranslateText('Produits') }}</h3>
        <table>
            <colgroup>
                <col style="width: 35%;">
                <col style="width: 15%;">
                <col style="width: 25%;">
                <col style="width: 25%;">
            </colgroup>
            <thead>
                <tr>
                    <th>{{ TranslationHelper::TranslateText('Article') }}</th>
                    <th class="text-center">Qté</th>
                    <th class="text-right">P.U</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach ($commande->contenus as $item)
                <tr>
                    <td>{{ $item->produit->nom ?? 'Produit' }}</td>
                    <td class="text-center">{{ $item->quantite }}</td>
                    <td class="text-right">{{ number_format($item->prix_unitaire, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($item->prix_unitaire * $item->quantite, 0, ',', ' ') }}</td>
                </tr>
                @php $total += ($item->prix_unitaire * $item->quantite); @endphp
                @endforeach

                @if(isset($commande->frais) && $commande->frais > 0)
                <tr>
                    <td><b>{{ TranslationHelper::TranslateText('Livraison') }}</b></td>
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

        <!-- Infos client -->
        <h4 style="border-bottom: 1px solid #000; padding-bottom: 2px;">{{ TranslationHelper::TranslateText('Client') }}</h4>
        <p><strong>Nom :</strong> {{ $commande->prenom ?? '' }} {{ $commande->nom ?? '' }}</p>
        <p><strong>Tél :</strong> {{ $commande->phone ?? ($commande->telephone ?? 'N/A') }}</p>
        @if(!empty($commande->adresse))
        <p><strong>Adresse :</strong> {{ $commande->adresse }}</p>
        @endif
        @if(!empty($commande->gouvernorat))
        <p><strong>Ville :</strong> {{ $commande->gouvernorat }}</p>
        @endif

        <hr>

        <p class="text-center" style="font-size: 13px;">
            {{ TranslationHelper::TranslateText('Merci de votre confiance') }} !
            <br>
            {{ TranslationHelper::TranslateText('À bientôt') }}
        </p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>