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
    <meta charset="UTF-8">
     <title>Facture N° {{ $consultation->code_consultation }}</title>
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
</head>

<body>

    <!-- EN-TÊTE DE LA FACTURE -->
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

    <!-- INFORMATIONS PATIENT ET MÉDECIN -->
    <table class="info-box">
        <tr>
            <td>
                <div class="box-header">Informations Patient</div>
                <strong>Nom & Prénom :</strong> {{ $consultation->patient->nom ?? '' }} {{ $consultation->patient->prenom ?? '' }}<br>
                <strong>Code Patient :</strong> {{ $consultation->patient->code_patient ?? 'N/A' }}<br>
                <strong>Téléphone :</strong> {{ $consultation->patient->telephone ?? '-' }}<br>
                @if($consultation->patient?->est_assure && $consultation->patient?->assurance)
                <strong>Assurance :</strong> {{ $consultation->patient->assurance->code ?? 'Oui' }} (Couverture : {{ $tauxAssurance }}%)
                @else
                <strong>Couverture :</strong> Patient non assuré (100% à charge)
                @endif
            </td>
            <td>
                <div class="box-header">Prise en charge Médicale</div>
                <strong>Médecin traitant :</strong> Dr. {{ $consultation->medecin?->nom ?? 'Non assigné' }} {{ $consultation->medecin?->prenom ?? '' }}<br>
                <strong>Acte :</strong> Consultation {{ ucfirst(str_replace('_', ' ', $consultation->type ?? 'générale')) }}<br>
                <strong>Statut Caisse :</strong>
                @if($consultation->est_paye)
                <span class="badge badge-success">Réglé (Payé)</span>
                @else
                <span class="badge badge-warning">En attente de paiement</span>
                @endif
            </td>
        </tr>
    </table>

    <!-- TABLEAU DÉTAILLÉ DES ACTES, EXAMENS, HOSPITALISATION ET PRODUITS -->
    <table class="details-table">
        <thead>
            <tr>
                <th style="width: 15%;">Type</th>
                <th style="width: 45%;">Désignation des Prestations & Soins</th>
                <th style="width: 12%; text-align: center;">PU / Qté</th>
                <th style="width: 8%; text-align: center;">Qté</th>
                <th style="width: 20%; text-align: right;">Montant Total</th>
            </tr>
        </thead>
        <tbody>
            {{-- Acte de Consultation --}}
            <tr>
                <td><span class="badge badge-success">Consultation</span></td>
                <td><strong>Consultation Médicale</strong></td>
                <td style="text-align: center;">{{ number_format($tarifConsultation, 0, ',', ' ') }}</td>
                <td style="text-align: center;">1</td>
                <td style="text-align: right;">{{ number_format($tarifConsultation, 0, ',', ' ') }} FCFA</td>
            </tr>


            {{-- Hospitalisation / Observation associée (Ligne unique avec détails complets) --}}
            @if(isset($derniereHospitalisation) && $derniereHospitalisation->montant_total > 0)
            @php
            $typeLabel = ucfirst($derniereHospitalisation->type_prise_en_charge);
            $jours = $derniereHospitalisation->nombre_jours ?? 1;
            $tarifJour = floatval($derniereHospitalisation->tarif_journalier ?? 0);
            $fraisSejour = ($derniereHospitalisation->type_prise_en_charge === 'observation') ? $tarifJour : ($jours * $tarifJour);

            $fraisSoinsChambre = floatval($derniereHospitalisation->frais_soins_chambre ?? 0);

            $qteVisite = intval($derniereHospitalisation->visite_urgentiste_qte ?? 0);
            $puVisite = floatval($derniereHospitalisation->visite_urgentiste_pu ?? 0);
            $totalVisite = $derniereHospitalisation->visite_urgentiste_total ?? 0;

            $qteAmi = intval($derniereHospitalisation->soin_ami_qte ?? 0);
            $puAmi = floatval($derniereHospitalisation->soin_ami_pu ?? 0);
            $totalAmi = $derniereHospitalisation->soin_ami_total ?? 0;
            @endphp

            <tr>
                <td><span class="badge badge-purple">Hospitalisation</span></td>
                <td>
                    <strong>Prise en charge {{ $typeLabel }} (Ch. {{ $derniereHospitalisation->chambre_number ?? 'N/A' }})</strong>
                    <br>
                    <ul style="margin: 3px 0 0 15px; padding: 0; color: #555; font-size: 11px;">
                        @if($derniereHospitalisation->type_prise_en_charge === 'observation')
                        <li>Forfait observation : {{ number_format($fraisSejour, 0, ',', ' ') }} FCFA</li>
                        @else
                        <li>Séjour : {{ $jours }} jour(s) × {{ number_format($tarifJour, 0, ',', ' ') }} = {{ number_format($fraisSejour, 0, ',', ' ') }} FCFA</li>
                        @endif

                        @if($fraisSoinsChambre > 0)
                        <li>Soins et chambre : {{ number_format($fraisSoinsChambre, 0, ',', ' ') }} FCFA</li>
                        @endif

                        @if($qteVisite > 0)
                        <li>Visites urgentistes : {{ $qteVisite }} × {{ number_format($puVisite, 0, ',', ' ') }} = {{ number_format($totalVisite, 0, ',', ' ') }} FCFA</li>
                        @endif

                        @if($qteAmi > 0)
                        <li>Soins infirmiers AMI : {{ $qteAmi }} × {{ number_format($puAmi, 0, ',', ' ') }} = {{ number_format($totalAmi, 0, ',', ' ') }} FCFA</li>
                        @endif
                    </ul>
                </td>
                <td style="text-align: center;">Global</td>
                <td style="text-align: center;">1</td>
                <td style="text-align: right; vertical-align: middle;">
                    <strong>{{ number_format($derniereHospitalisation->montant_total, 0, ',', ' ') }} FCFA</strong>
                </td>
            </tr>
            @endif

            {{-- Examens prescrits --}}
            @foreach($consultation->demandesExamens as $demande)
            <tr>
                <td><span class="badge badge-warning">Examen</span></td>
                <td>
                    <strong>{{ $demande->examen->nom ?? 'Examen biologique / imagerie' }}</strong>

                    @if(is_array($demande->analyses_demandees) && count($demande->analyses_demandees) > 0)
                    <br>
                    <small style="color: #495057;">
                        <em>Détails :</em>
                        {{ implode(', ', array_column($demande->analyses_demandees, 'nom')) }}
                    </small>
                    @endif
                </td>
                <td style="text-align: center;">{{ number_format($demande->tarif_brut, 0, ',', ' ') }} FCFA</td>
                <td style="text-align: center;">1</td>
                <td style="text-align: right;">{{ number_format($demande->tarif_brut, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach



            {{-- Produits et Médicaments prescrits --}}
            @if($consultation->relationLoaded('produits') ? $consultation->produits : $consultation->produits()->exists())
            @foreach($consultation->produits as $prod)
            @php
            $qte = $prod->pivot->quantite ?? 1;
            $pu = $prod->pivot->prix_unitaire ?? $prod->pivot->prix ?? $prod->prix ?? 0;
            $montantLigne = $qte * $pu;
            @endphp
            <tr>
                <td><span class="badge badge-info">Médicament</span></td>
                <td>
                    <strong>{{ $prod->nom ?? 'Article / Médicament' }}</strong>
                    @if(!empty($prod->pivot->posologie))
                    <br><small style="color: #6c757d;">Posologie : {{ $prod->pivot->posologie }}</small>
                    @endif
                </td>
                <td style="text-align: center;">{{ number_format($pu, 0, ',', ' ') }}</td>
                <td style="text-align: center;">{{ $qte }}</td>
                <td style="text-align: right;">{{ number_format($montantLigne, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>

    <!-- RÉCAPITULATIF FINANCIER -->
    <!-- RÉCAPITULATIF FINANCIER -->
    <table class="totals-table">
        <tr>
            <td>Consultation & Actes :</td>
            <td style="text-align: right;">{{ number_format($tarifConsultation, 0, ',', ' ') }} FCFA</td>
        </tr>
        @if($tarifExamens > 0)
        <tr>
            <td>Examens prescrits :</td>
            <td style="text-align: right;">{{ number_format($tarifExamens, 0, ',', ' ') }} FCFA</td>
        </tr>
        @endif
        @if($tarifHospitalisation > 0)
        <tr>
            <td>Frais de Séjour / Hosp. :</td>
            <td style="text-align: right;">{{ number_format($tarifHospitalisation, 0, ',', ' ') }} FCFA</td>
        </tr>
        @endif
        @if($tarifProduits > 0)
        <tr>
            <td>Produits & Médicaments :</td>
            <td style="text-align: right;">{{ number_format($tarifProduits, 0, ',', ' ') }} FCFA</td>
        </tr>
        @endif

        <tr style="border-top: 1px solid #333;">
            <td><strong>TOTAL BRUT :</strong></td>
            <td style="text-align: right;"><strong>{{ number_format($totalBrut, 0, ',', ' ') }} FCFA</strong></td>
        </tr>

        @if($partAssuranceFinal > 0)
        <tr style="color: #198754;">
            <td>
                Prise en charge Assurance
                @if($tauxAssurance > 0) ({{ $tauxAssurance }}%) @endif :
            </td>
            <td style="text-align: right; font-weight: bold;">
                - {{ number_format($partAssuranceFinal, 0, ',', ' ') }} FCFA
            </td>
        </tr>
        @endif

        <tr class="total-row" style="border-top: 2px solid #000; border-bottom: 2px solid #000;">
            <td><strong>NET À PAYER (PATIENT) :</strong></td>
            <td style="text-align: right; color: #0d6efd; font-size: 14px;">
                <strong>{{ number_format($partPatient, 0, ',', ' ') }} FCFA</strong>
            </td>
        </tr>
    </table>

    <!-- SIGNATURES -->
    <table class="signatures">
        <tr>
            <td>
                <strong>Le Patient / Représentant</strong><br>
                <small style="color: #6c757d;">(Signature)</small>
            </td>
            <td>
                <strong>La Caisse / La Perception</strong><br>
                <small style="color: #6c757d;">(Cachet & Signature)</small>
            </td>
        </tr>
    </table>

    <!-- PIED DE PAGE -->
    <div class="footer">
        Facture émise le {{ date('d/m/Y à H:i') }} — Centre Médical La Gloire — Merci de votre confiance.
    </div>

</body>

</html>