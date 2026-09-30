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
            font-family: 'Courier New', Courier, monospace; /* Police plus nette sur thermique */
            margin: 0;
            padding: 0;
            color: #000;
            background-color: #fff;
            font-size: 12px; /* Réduit pour les petits reçus */
            line-height: 1.2;
        }

        /* Format rouleau thermique 80mm */
        .container {
            width: 72mm; /* Laisse une petite marge pour les bords */
            margin: 0 auto;
            padding: 5px;
            box-sizing: border-box;
            position: relative;
        }

        .watermark {
            font-size: 40px; /* Plus petit pour tenir sur le ticket */
            top: 40%;
            left: 5%;
            width: 90%;
        }

        .invoice-header {
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000; /* Ligne pointillée classique pour les tickets */
            padding-bottom: 5px;
        }

        .logo {
            max-width: 80px; /* Logo plus petit */
            height: auto;
            display: block;
            margin: 0 auto 5px auto; /* Centrer le logo sur le ticket */
        }

        .logo-cell {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        th, td {
            border: none; /* Pas de bordures de grille sur un ticket thermique */
            border-bottom: 1px dotted #ccc; /* Séparateur léger entre les articles */
            padding: 4px 2px;
            font-size: 11px;
        }

        th {
            background-color: transparent;
            border-bottom: 1px solid #000;
            font-weight: bold;
        }

        .tr-montant {
            color: #000 !important;
            background-color: transparent !important;
            border-top: 2px solid #000 !important;
        }

        .tr-montant td {
            border: none;
            font-size: 13px;
        }

        hr {
            border: 0;
            border-top: 1px dashed #000;
            margin: 10px 0;
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
            <!-- Définition des largeurs : Colonne Produit réduite à 35%, Qté à 15%, P.U et Total à 25% chacun -->
            <colgroup>
                <col style="width: 30%;">
                <col style="width: 15%;">
                <col style="width: 25%;">
                <col style="width: 20%;">
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