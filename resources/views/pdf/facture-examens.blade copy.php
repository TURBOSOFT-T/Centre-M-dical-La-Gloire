<!DOCTYPE html>
<html lang="fr">

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

// Calculs financiers de base

// 1. Consultation de base
$tarifConsultation = $tarifConsultation ?? ($consultation->tarif_brut ?? 5000);

// 2. Examens prescrits
$tarifExamens = $tarifExamens ?? ($consultation->demandesExamens ? $consultation->demandesExamens->sum('tarif_brut') : 0);

// 3. Produits & Médicaments
$tarifProduits = 0;
if (isset($consultation->produits)) {
foreach ($consultation->produits as $prod) {
$qte = $prod->pivot->quantite ?? 1;
$pu = $prod->pivot->prix_unitaire ?? $prod->pivot->prix ?? $prod->prix ?? 0;
$tarifProduits += ($qte * $pu);
}
} else {
$tarifProduits = $consultation->tarif_produits ?? 0;
}

// 4. Dernière hospitalisation
$derniereHospitalisation = $derniereHospitalisation ?? $consultation->patient?->hospitalisations()->latest('created_at')->first();
$tarifHospitalisation = $derniereHospitalisation ? floatval($derniereHospitalisation->montant_total) : 0;

// 5. TOTAL BRUT GLOBAL (5 000 + 3 000 + 8 000 + 5 000 = 21 000 FCFA)
$totalBrut = $tarifConsultation + $tarifExamens + $tarifProduits + $tarifHospitalisation;

// =========================================================================
// 6. CALCUL DE LA PART ASSURANCE ET DU NET À PAYER PATIENT
// =========================================================================

// Récupération du taux de couverture (%)
$tauxAssurance = $tauxAssurance ?? ($consultation->patient?->assurance?->taux ?? 0);

if ($tauxAssurance > 0) {
// Application du taux d'assurance sur le Total Brut complet (21 000 * 10% = 2 100 FCFA)
$partAssuranceFinal = ($totalBrut * $tauxAssurance) / 100;
} elseif (isset($partAssurance) && floatval($partAssurance) > 0) {
// Si un montant fixe de part assurance est passé explicitement
$partAssuranceFinal = floatval($partAssurance);
} elseif ($derniereHospitalisation && floatval($derniereHospitalisation->part_assurance) > 0) {
// Si seule l'hospitalisation comporte un montant fixe d'assurance
$partAssuranceFinal = floatval($derniereHospitalisation->part_assurance);
} else {
$partAssuranceFinal = 0;
}

// Sécurisation : ne peut pas dépasser le total brut
$partAssuranceFinal = min($totalBrut, $partAssuranceFinal);

// Net à payer par le patient (21 000 - 2 100 = 18 900 FCFA)
$partPatient = max(0, $totalBrut - $partAssuranceFinal);
@endphp

<head>

 <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 15px;
        }

        /* En-tête */
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
            font-size: 20px;
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

        /* Encart Infos Patient & Medecin */
        .info-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
        }

        .info-box td {
            padding: 10px;
            width: 50%;
            vertical-align: top;
        }

        .box-header {
            font-weight: bold;
            color: #0d6efd;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 4px;
            margin-bottom: 6px;
            font-size: 11px;
            text-transform: uppercase;
        }

        /* Table des détails des prestations */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .details-table th {
            background-color: #0d6efd;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            text-transform: uppercase;
        }

        .details-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
        }

        .details-table tr:nth-child(even) td {
            background-color: #f8f9fa;
        }

        /* Total / Récapitulatif */
        .totals-table {
            width: 50%;
            margin-left: auto;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .totals-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #e9ecef;
        }

        .totals-table .total-row td {
            border-top: 2px solid #212529;
            border-bottom: 2px solid #212529;
            font-weight: bold;
            font-size: 13px;
        }

        /* Badge Statut */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .badge-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #664d03;
        }

        .badge-info {
            background-color: #cff4fc;
            color: #055160;
        }

        .badge-purple {
            background-color: #e2d9f3;
            color: #432874;
        }

        /* Pied de page */
        .footer {
            margin-top: 30px;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #6c757d;
        }

        .signatures {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            height: 60px;
        }
    </style>
    <meta charset="UTF-8">
    <title>Facture Examens - {{ $consultation->code_consultation }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #17a2b8; padding-bottom: 10px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .table-items { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table-items th, .table-items td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table-items th { background-color: #f8f9fa; }
        .totals { margin-top: 20px; float: right; width: 40%; }
        .totals table { width: 100%; border-collapse: collapse; }
        .totals td { padding: 6px; text-align: right; }
        .text-bold { font-weight: bold; }
        .text-success { color: #198754; }
        .clear { clear: both; }
    </style>
</head>
<body>
   <table class="header-table">
        <tr>
            <td>
                <div class="clinic-title">Centre Médical La Gloire</div>
                <p class="mb-0 text-muted small"> Tél : {{ $config->telephone ?? 'N/A' }}</p>
                <p class="mb-0 text-muted small"> E-mail : {{ $config->email ?? 'N/A' }}</p>
                <p class="mb-0 text-muted small"> Adresse : {{ $config->addresse ?? 'N/A' }}</p>
            </td>
            <td class="logo-cell" style="text-align: center;">
                @if(!empty($logoBase64))
                <img src="{{ $logoBase64 }}" alt="logo" width="80" height="80" class="logo">
                @endif
            </td>
            <td>
                <div class="invoice-title">Facture / Reçu</div>
                <div class="invoice-code">N° {{ $consultation->code_consultation }}</div>
                <div class="clinic-sub" style="text-align: right;">Date : {{ $consultation->date_heure_rdv ? \Carbon\Carbon::parse($consultation->date_heure_rdv)->format('d/m/Y à H:i') : date('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>
   
    <table class="info-table">
        <tr>
            <td width="50%">
                <strong>Patient :</strong> {{ $consultation->patient?->nom_complet ?? $consultation->patient?->nom . ' ' . $consultation->patient?->prenom }}<br>
                <strong>Code Patient :</strong> {{ $consultation->patient?->code_patient }}
            </td>
            <td width="50%" style="text-align: right;">
                <strong>Assurance :</strong> {{ $consultation->patient?->assurance?->nom ?? 'Aucune' }}<br>
                <strong>Taux Couverture :</strong> {{ $consultation->patient?->taux_couverture ?? 0 }}%
            </td>
        </tr>
    </table>

    <table class="table-items">
        <thead>
            <tr>
                <th>Examen demandé</th>
                <th>Type / Catégorie</th>
                <th style="text-align: right;">Montant (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalBrut = 0; @endphp
            @forelse($consultation->demandesExamens as $examen)
                @php
                    $montant = $examen->tarif_brut ?? $examen->prix ?? 0;
                    $totalBrut += $montant;
                @endphp
                <tr>
                    <td>{{ $examen->nom_examen ?? $examen->libelle }}</td>
                    <td>{{ ucfirst($examen->type ?? 'Laboratoire') }}</td>
                    <td style="text-align: right;">{{ number_format($montant, 0, ',', ' ') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">Aucun examen demandé pour cette consultation.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @php
        $taux = $consultation->patient?->taux_couverture ?? 0;
        $partAssurance = ($totalBrut * $taux) / 100;
        $netPatient = max(0, $totalBrut - $partAssurance);
    @endphp

    <div class="totals">
        <table>
            <tr>
                <td>Montant Brut :</td>
                <td class="text-bold">{{ number_format($totalBrut, 0, ',', ' ') }} FCFA</td>
            </tr>
            @if($taux > 0)
            <tr>
                <td class="text-success">Prise en charge Assurance ({{ $taux }}%) :</td>
                <td class="text-bold text-success">- {{ number_format($partAssurance, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endif
            <tr style="border-top: 1px solid #000;">
                <td class="text-bold" style="font-size: 14px;">Net à Payer Patient :</td>
                <td class="text-bold" style="font-size: 14px; color: #17a2b8;">{{ number_format($netPatient, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>

</body>
</html>