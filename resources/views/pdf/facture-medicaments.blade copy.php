<!DOCTYPE html>
<html lang="fr">

@php
    $config = DB::table('configs')->select('icon', 'logo', 'telephone', 'email', 'addresse')->first();

    // Détermination de l'image du logo
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

    // Récupération des produits / médicaments prescrits
    $produits = $consultation->produits ?? collect();
    $nbProduits = $produits->count();

    // Taux d'assurance
    $tauxAssurance = $tauxAssurance ?? ($consultation->patient?->assurance?->taux ?? $consultation->patient?->taux_couverture ?? 0);

    // Calculs totaux
    $totalBrut = 0;
    $totalTicketMod = 0;
    $totalTiersPayeur = 0;

    foreach ($produits as $prod) {
        $qte = $prod->pivot->quantite ?? 1;
        $pu = $prod->pivot->prix_unitaire ?? $prod->prix ?? 0;
        $m = $qte * $pu;
        $tp = ($m * $tauxAssurance) / 100;
        $tm = $m - $tp;

        $totalBrut += $m;
        $totalTiersPayeur += $tp;
        $totalTicketMod += $tm;
    }

    // Conversion du montant assurance en toutes lettres
    $partAssuranceArrondie = round($totalTiersPayeur);
    if (class_exists('NumberFormatter')) {
        $formatter = new \NumberFormatter('fr', \NumberFormatter::SPELLOUT);
        $partAssuranceEnLettres = ucfirst($formatter->format($partAssuranceArrondie));
    } else {
        $partAssuranceEnLettres = number_format($partAssuranceArrondie, 0, ',', ' ');
    }
@endphp

<head>
    <meta charset="UTF-8">
    <title>Facture Médicaments - {{ $consultation->code_consultation }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 15px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: top;
        }

        .clinic-title {
            font-size: 18px;
            font-weight: bold;
            color: #0d6efd;
            text-transform: uppercase;
        }

        .clinic-sub {
            font-size: 10px;
            color: #6c757d;
            margin-top: 2px;
        }

        .invoice-title {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #212529;
            text-transform: uppercase;
        }

        .invoice-code {
            text-align: right;
            font-size: 12px;
            font-weight: bold;
            color: #0d6efd;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 10px;
            border-radius: 4px;
        }

        .info-table td {
            padding: 4px;
            vertical-align: top;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 6px;
            color: #0d6efd;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }

        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 15px;
        }

        .table-items th, .table-items td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        .table-items th {
            background-color: #f8f9fa;
            font-size: 10px;
            text-transform: uppercase;
        }

        .totals {
            margin-top: 10px;
            float: right;
            width: 45%;
        }

        .totals table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals td {
            padding: 5px;
            text-align: right;
        }

        .text-bold { font-weight: bold; }
        .text-success { color: #198754; }
        .clear { clear: both; }

        .footer {
            margin-top: 25px;
            border-top: 1px solid #dee2e6;
            padding-top: 8px;
            text-align: center;
            font-size: 9px;
            color: #6c757d;
        }
    </style>
</head>
<body>

    <!-- EN-TÊTE DE LA FACTURE -->
    <table class="header-table">
        <tr>
            <td width="40%">
                <div class="clinic-title">Centre Médical La Gloire</div>
                <p class="mb-0 text-muted small">Tél : {{ $config->telephone ?? 'N/A' }}</p>
                <p class="mb-0 text-muted small">E-mail : {{ $config->email ?? 'N/A' }}</p>
                <p class="mb-0 text-muted small">Adresse : {{ $config->addresse ?? 'N/A' }}</p>
            </td>
            <td width="20%" style="text-align: center;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="logo" width="75" height="75">
                @endif
            </td>
            <td width="40%">
                <div class="invoice-title">Facture Médicaments</div>
                <div class="invoice-code">N° {{ $consultation->code_consultation }}</div>
                <div class="clinic-sub" style="text-align: right;">
                    Date : {{ $consultation->date_heure_rdv ? \Carbon\Carbon::parse($consultation->date_heure_rdv)->format('d/m/Y à H:i') : date('d/m/Y H:i') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- INFORMATIONS PATIENT & ASSURANCE -->
    <table class="info-table">
        <tr>
            <td width="50%">
                <strong>Patient :</strong> {{ $consultation->patient?->nom_complet ?? trim(($consultation->patient?->nom ?? '') . ' ' . ($consultation->patient?->prenom ?? '')) }}<br>
                <strong>Code Patient :</strong> {{ $consultation->patient?->code_patient ?? 'N/A' }}<br>
                <strong>N° Tél :</strong> {{ $consultation->patient?->telephone ?? 'N/A' }}
            </td>
            <td width="50%" style="text-align: right;">
                <strong>Médecin prescripteur :</strong> {{ $consultation->medecin?->nom ?? 'N/A' }}<br>
                <strong>Assurance :</strong> {{ $consultation->patient?->assurance?->nom ?? 'Aucune' }}<br>
                <strong>Taux Couverture :</strong> {{ $tauxAssurance }}%
            </td>
        </tr>
    </table>

    <!-- TABLEAU : DÉTAILS DES PRODUITS & MÉDICAMENTS -->
    <div class="section-title">Détails des Produits & Médicaments Prescrits</div>
    <table class="table-items">
        <thead>
            <tr>
                <th style="width: 12%; text-align: center;">Code</th>
                <th style="width: 40%;">Médicament / Produit</th>
                <th style="width: 8%; text-align: center;">Qté</th>
                <th style="width: 13%; text-align: right;">P.U (FCFA)</th>
                <th style="width: 13.5%; text-align: right;">Mt.Ticket.Mod</th>
                <th style="width: 13.5%; text-align: right;">Mt.T.Payeur</th>
            </tr>
        </thead>
        <tbody>
            @if($nbProduits > 0)
                @php $isFirstProdLine = true; @endphp
                @foreach($produits as $produit)
                    @php
                        $qte = $produit->pivot->quantite ?? 1;
                        $pu = $produit->pivot->prix_unitaire ?? $produit->prix ?? 0;
                        $m = $qte * $pu;
                        $partPayeurProd = ($m * $tauxAssurance) / 100;
                        $partTicketProd = $m - $partPayeurProd;
                    @endphp
                    <tr>
                        @if($isFirstProdLine)
                            <td rowspan="{{ $nbProduits }}" style="text-align: center; vertical-align: middle; font-weight: bold;">
                                PHARM
                            </td>
                            @php $isFirstProdLine = false; @endphp
                        @endif
                        <td>{{ $produit->nom }}</td>
                        <td style="text-align: center;">{{ $qte }}</td>
                        <td style="text-align: right;">{{ number_format($pu, 0, ',', ' ') }}</td>
                        <td style="text-align: right;">{{ number_format($partTicketProd, 0, ',', ' ') }}</td>
                        <td style="text-align: right;">{{ number_format($partPayeurProd, 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td style="text-align: center; font-weight: bold;">PHARM</td>
                    <td style="color: #6c757d; font-style: italic;">Aucun médicament prescrit pour cette consultation.</td>
                    <td style="text-align: center;">0</td>
                    <td style="text-align: right;">0</td>
                    <td style="text-align: right;">0</td>
                    <td style="text-align: right;">0</td>
                </tr>
            @endif
        </tbody>

        <!-- LIGNE TOTAL DU TABLEAU -->
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold; border-top: 2px solid #333;">
                <td colspan="2" style="text-align: right; text-transform: uppercase;">TOTAL GENERAL :</td>
                <td style="text-align: center;">{{ $produits->sum(fn($p) => $p->pivot->quantite ?? 1) }}</td>
                <td style="text-align: right;">{{ number_format($totalBrut, 0, ',', ' ') }} FCFA</td>
                <td style="text-align: right; color: #0d6efd;">{{ number_format($totalTicketMod, 0, ',', ' ') }}</td>
                <td style="text-align: right; color: #198754;">{{ number_format($totalTiersPayeur, 0, ',', ' ') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- TOTALS ET RÉCAPITULATIF FINANCIER -->
    <div class="totals">
        <table>
            <tr>
                <td>Montant Brut Total :</td>
                <td class="text-bold">{{ number_format($totalBrut, 0, ',', ' ') }} FCFA</td>
            </tr>
            @if($totalTiersPayeur > 0)
            <tr>
                <td class="text-success">Prise en charge Assurance ({{ $tauxAssurance }}%) :</td>
                <td class="text-bold text-success">- {{ number_format($totalTiersPayeur, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endif
            <tr style="border-top: 2px solid #212529;">
                <td class="text-bold" style="font-size: 13px;">Net à Payer Patient :</td>
                <td class="text-bold" style="font-size: 13px; color: #28a745;">{{ number_format($totalTicketMod, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>

    <!-- MENTION DE L'ARRÊTÉ DE FACTURE EN LETTRES -->
    <div style="margin-top: 20px; padding: 10px; background-color: #f8f9fa; border-left: 4px solid #28a745; font-size: 11px;">
        <strong>Arrêtée la présente facture à la somme de :</strong> 
        <span style="font-style: italic; text-transform: uppercase; font-weight: bold;">
            {{ $partAssuranceEnLettres }} FCFA
        </span>
    </div>

    <!-- TABLEAU DE SIGNATURES À 3 COLONNES -->
    <table style="width: 100%; margin-top: 25px; border-collapse: collapse;">
        <tr>
            <!-- Case Signature Patient -->
            <td style="width: 45%; vertical-align: top; border: 1px solid #1f2020; padding: 10px; height: 60px;">
                <!-- Espace réservé pour la signature/cachet patient -->
            </td>

            <!-- Colonne vide d'espacement au milieu -->
            <td style="width: 10%; border: none;"></td>

            <!-- Case Comptabilité avec Total Assurance -->
            <td style="width: 45%; vertical-align: top; border: 1px solid #1f2020; padding: 10px; height: 60px;">
                <div style="font-size: 11px; font-weight: bold; text-align: right;">
                    TOTAL ASSURANCE : {{ number_format($totalTiersPayeur, 0, ',', ' ') }} FCFA
                </div>
            </td>
        </tr>
        <tr>
            <!-- Libellé Signature Patient -->
            <td style="width: 45%; text-align: center; vertical-align: top; padding-top: 6px;">
                <p style="font-weight: bold; margin: 0;">Signature du Patient</p>
            </td>

            <!-- Espace vide sous la colonne centrale -->
            <td style="width: 10%;"></td>

            <!-- Libellé Comptabilité -->
            <td style="width: 45%; text-align: center; vertical-align: top; padding-top: 6px;">
                <p style="font-weight: bold; margin: 0;">La Comptabilité</p>
            </td>
        </tr>
    </table>

    <!-- PIED DE PAGE -->
    <div class="footer">
        Facture émise le {{ date('d/m/Y à H:i') }} — Centre Médical La Gloire — Merci de votre confiance.
    </div>

</body>
</html>