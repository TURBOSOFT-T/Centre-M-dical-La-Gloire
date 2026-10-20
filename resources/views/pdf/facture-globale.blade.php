<!DOCTYPE html>
<html lang="fr">
@php
    use Illuminate\Support\Carbon;

    // 1. Récupération des configurations globales & Logo
    $config = \Illuminate\Support\Facades\DB::table('configs')
        ->select('icon', 'logo', 'telephone', 'email', 'addresse')
        ->first();

    $logoPath = public_path('icons/logo.jpg');

    if ($config && !empty($config->logo)
        && file_exists(storage_path('app/public/' . $config->logo))) {
        $logoPath = storage_path('app/public/' . $config->logo);
    } elseif ($config && !empty($config->icon)
        && file_exists(storage_path('app/public/' . $config->icon))) {
        $logoPath = storage_path('app/public/' . $config->icon);
    }

    $logoBase64 = '';

    if (file_exists($logoPath)) {
        $extension = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };

        $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
    }

    // 2. Calculs financiers de base
    $tarifConsultation = $tarifConsultation ?? ($consultation->tarif_brut ?? 5000);
    $tarifExamens = $tarifExamens ?? ($consultation->demandesExamens ? $consultation->demandesExamens->sum('tarif_brut') : 0);
    $qteExamens = $consultation->demandesExamens ? $consultation->demandesExamens->count() : 0;

    $tarifProduits = 0;
    $qteProduits = 0;
    if (isset($consultation->produits)) {
        foreach ($consultation->produits as $prod) {
            $qte = $prod->pivot->quantite ?? 1;
            $pu = $prod->pivot->prix_unitaire ?? $prod->pivot->prix ?? $prod->prix ?? 0;
            $tarifProduits += ($qte * $pu);
            $qteProduits += $qte;
        }
    } else {
        $tarifProduits = $consultation->tarif_produits ?? 0;
        $qteProduits = 1;
    }

    // Dernière hospitalisation
    $derniereHospitalisation = $derniereHospitalisation ?? $consultation->patient?->hospitalisations()->latest('created_at')->first();

    $fraisSejour = 0;
    $fraisSoinsChambre = 0;
    $totalVisite = 0;
    $totalAmi = 0;
    $qteVisite = 0;
    $puVisite = 0;
    $qteAmi = 0;
    $puAmi = 0;
    $jours = 1;
    $tarifJour = 0;

    if ($derniereHospitalisation) {
        $jours = $derniereHospitalisation->nombre_jours ?? 1;
        $tarifJour = floatval($derniereHospitalisation->tarif_journalier ?? 0);
        $fraisSejour = ($derniereHospitalisation->type_prise_en_charge === 'observation') ? $tarifJour : ($jours * $tarifJour);
        $fraisSoinsChambre = floatval($derniereHospitalisation->frais_soins_chambre ?? 0);

        $qteVisite = intval($derniereHospitalisation->visite_urgentiste_qte ?? 0);
        $puVisite = floatval($derniereHospitalisation->visite_urgentiste_pu ?? 0);
        $totalVisite = $derniereHospitalisation->visite_urgentiste_total ?? ($qteVisite * $puVisite);

        $qteAmi = intval($derniereHospitalisation->soin_ami_qte ?? 0);
        $puAmi = floatval($derniereHospitalisation->soin_ami_pu ?? 0);
        $totalAmi = $derniereHospitalisation->soin_ami_total ?? ($qteAmi * $puAmi);
    }

    $nbLignesHosp = 0;
    if ($fraisSejour > 0) $nbLignesHosp++;
    if ($fraisSoinsChambre > 0) $nbLignesHosp++;
    if ($totalVisite > 0) $nbLignesHosp++;
    if ($totalAmi > 0) $nbLignesHosp++;

    $tarifHospitalisation = $fraisSejour + $fraisSoinsChambre + $totalVisite + $totalAmi;
    $totalBrut = $tarifConsultation + $tarifExamens + $tarifProduits + $tarifHospitalisation;

    // 3. Calcul de la part assurance
    $tauxAssurance = (float) (
        $tauxAssurance
        ?? $consultation->patient?->assurance?->taux
        ?? $consultation->patient?->taux_couverture
        ?? 0
    );
    $tauxAssurance = max(0, min(100, $tauxAssurance));

    if ($tauxAssurance > 0) {
        $partAssuranceFinal = ($totalBrut * $tauxAssurance) / 100;
    } elseif (isset($partAssurance) && floatval($partAssurance) > 0) {
        $partAssuranceFinal = floatval($partAssurance);
    } elseif ($derniereHospitalisation && floatval($derniereHospitalisation->part_assurance) > 0) {
        $partAssuranceFinal = floatval($derniereHospitalisation->part_assurance);
    } else {
        $partAssuranceFinal = 0;
    }

    $partAssuranceFinal = min($totalBrut, $partAssuranceFinal);
    $partPatient = max(0, $totalBrut - $partAssuranceFinal);

    // 4. Montant assurance en toutes lettres
    $montantAssuranceArrondi = (int) round($partAssuranceFinal);
    if (class_exists(\NumberFormatter::class)) {
        $formatter = new \NumberFormatter('fr_FR', \NumberFormatter::SPELLOUT);
        $montantEnLettres = ucfirst($formatter->format($montantAssuranceArrondi));
    } else {
        $montantEnLettres = number_format($montantAssuranceArrondi, 0, ',', ' ');
    }

    $dateFacture = $consultation->date_heure_rdv
        ? Carbon::parse($consultation->date_heure_rdv)->format('d/m/Y à H:i')
        : now()->format('d/m/Y à H:i');

    $patient = $consultation->patient;
    $nomPatient = $patient?->nom_complet
        ?? trim(($patient?->nom ?? '') . ' ' . ($patient?->prenom ?? ''));
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Facture Globale - {{ $consultation->code_consultation }}</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            line-height: 1.45;
            color: #253247;
            margin: 0;
            background: #fff;
        }

        .invoice {
            width: 100%;
        }

        /* En-tête */
        .header {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px solid #1769aa;
            margin-bottom: 16px;
        }

        .header td {
            vertical-align: middle;
            padding: 8px 5px 12px;
        }

        .clinic-name {
            font-size: 17px;
            font-weight: 700;
            text-transform: uppercase;
            color: #14558b;
            margin-bottom: 5px;
        }

        .clinic-details {
            font-size: 9px;
            color: #5b6573;
            line-height: 1.7;
        }

        .logo {
            max-width: 76px;
            max-height: 76px;
            object-fit: contain;
        }

        .invoice-title {
            text-align: right;
            font-size: 19px;
            font-weight: 700;
            text-transform: uppercase;
            color: #17324d;
        }

        .invoice-number {
            text-align: right;
            font-size: 11px;
            font-weight: 700;
            color: #1769aa;
            margin-top: 6px;
        }

        .invoice-date {
            text-align: right;
            color: #667085;
            margin-top: 4px;
        }

        /* Sections & Encarts Patient */
        .section-title {
            margin: 15px 0 7px;
            padding: 7px 9px;
            background: #edf5fc;
            border-left: 4px solid #1769aa;
            color: #174e78;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .patient-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .patient-table td {
            width: 50%;
            padding: 9px;
            border: 1px solid #e0e6ed;
            vertical-align: top;
        }

        .patient-label {
            color: #697586;
            font-size: 9px;
            margin-bottom: 3px;
        }

        /* Tableau des prestations */
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 7px;
            font-size: 9px;
        }

        .items th {
            background: #56697c;
            color: #fff;
            font-size: 8px;
            text-transform: uppercase;
            font-weight: 700;
            padding: 8px 5px;
            border: 1px solid #17324d;
        }

        .items td {
            border: 1px solid #dce2e8;
            padding: 7px 5px;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .items tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
            white-space: nowrap;
        }

        .total-row {
            font-weight: 700;
            background: #edf5fc !important;
        }

        .ticket {
            color: #1769aa;
        }

        .insurance {
            color: #198754;
        }

        /* Récapitulatif financier */
        .summary-wrapper {
            width: 100%;
            margin-top: 14px;
        }

        .summary {
            width: 58%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .summary td {
            padding: 7px 8px;
            border-bottom: 1px solid #e0e6ed;
        }

        .summary td:last-child {
            text-align: right;
            font-weight: 700;
            white-space: nowrap;
        }

        .summary .net-row td {
            background: #eaf6ee;
            border-top: 2px solid #198754;
            color: #17663a;
            font-size: 11px;
            font-weight: 700;
        }

        /* Arrêté de facture */
        .amount-words {
            margin-top: 15px;
            padding: 10px;
            border: 1px solid #dce2e8;
            border-left: 4px solid #198754;
            background: #f8fafc;
        }

        /* Signatures à 3 colonnes */
        .signatures {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .signatures td {
            width: 45%;
            height: 60px;
            border: 1px solid #1f2020;
            padding: 10px;
            vertical-align: top;
        }

        .signatures .space {
            width: 10%;
            border: none;
        }

        .signature-label {
            text-align: center;
            font-weight: 700;
            padding-top: 6px;
        }

        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #dce2e8;
            color: #667085;
            text-align: center;
            font-size: 8px;
        }

        @media print {
            body {
                padding: 0;
            }

            .items thead {
                display: table-header-group;
            }

            .items tr,
            .summary tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
<div class="invoice">

    {{-- EN-TÊTE --}}
    <table class="header">
        <tr>
            <td style="width: 42%;">
                <div class="clinic-name">Centre Médical La Gloire</div>
                <div class="clinic-details">
                    <div>Téléphone : {{ $config->telephone ?? 'N/A' }}</div>
                    <div>E-mail : {{ $config->email ?? 'N/A' }}</div>
                    <div>Adresse : {{ $config->addresse ?? 'N/A' }}</div>
                </div>
            </td>

            <td style="width: 16%; text-align: center;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" class="logo">
                @endif
            </td>

            <td style="width: 42%;">
                <div class="invoice-title">Facture Globale</div>
                <div class="invoice-number">N° {{ $consultation->code_consultation }}</div>
                <div class="invoice-date">Date : {{ $dateFacture }}</div>
            </td>
        </tr>
    </table>

    {{-- INFOS PATIENT & ASSURANCE --}}
    <table class="patient-table">
        <tr>
            <td>
                <div class="patient-label">Nom du patient : <strong>{{ $nomPatient ?: 'Non renseigné' }}</strong></div>
                <div class="patient-label">Code patient : <strong>{{ $patient?->code_patient ?? '------' }}</strong></div>
                <div class="patient-label">Téléphone : <strong>{{ $patient?->telephone ?? '------' }}</strong></div>
                           <div class="patient-label">Sexe : <strong>{{ $patient?->genre ?? '------' }}</strong></div>
                                           <div class="patient-label">Date de naissance : <strong>{{ $patient?->date_naissance ?? '------' }}</strong></div>
           
                           <div class="patient-label">Residence : <strong>{{ $patient?->ville ?? '------' }}</strong></div>
           
                           <div class="patient-label">Adresse domicile : <strong>{{ $patient?->adresse ?? '------' }}</strong></div>
                            <div class="patient-label">Profession : <strong>{{ $patient?->profession ?? '------' }}</strong></div>
            
                        </td>

            <td>
                <div class="patient-label">Organisme d'assurance : <strong>{{ $patient?->assurance?->nom ?? 'Aucune' }}</strong></div>
                <div class="patient-label">Taux de couverture : <strong>{{ $tauxAssurance }} %</strong></div>
                <div class="patient-label">Tel : <strong>{{ $patient?->assurance?->telephone ?? '------' }}</strong></div>
                <div class="patient-label">BP: <strong>{{ $patient?->assurance?->bp ?? '------' }}</strong></div>
                
                <div class="patient-label">NIU : <strong>{{ $patient?->assurance?->niu ?? '------' }} </strong></div>
                <div class="patient-label">Registre de Commerce : <strong>{{ $patient?->assurance?->numero_rc ?? '------' }} </strong></div>
                

                <div class="patient-label">Assuré principal : <strong>{{ $patient?->nom_assure ?? 'Patient lui-même' }}</strong></div>

                <div class="patient-label">Société : <strong>{{ $patient?->societe ?? '------' }}</strong></div>
                 <div class="patient-label">Matricule assuré principal : <strong>{{ $patient?->matricule_assure_principal ?? '------' }}</strong></div>
            </td>
        </tr>
    </table>
 <table class="items">
        <thead>
            <tr>
                <th>Code Hospitalisation</th>
                <th>Numéro Bon Prise en Charge</th>
                <th>Date / Heure Entrée</th>
                <th>Date / Heure Sortie</th>
            </tr>
        </thead>
        <tbody>
            @php
            $hosp = $derniereHospitalisation ?? $consultation->hospitalisation ?? $consultation->patient?->hospitalisations()->latest('created_at')->first();
            @endphp
            <tr>
                <td>{{ $hosp?->code_hospitalisation ?? $consultation->code_hospitalisation ?? 'N/A' }}</td>
                <td>{{ $consultation->patient?->numero_bon ?? $hosp?->numero_bon ?? $consultation->numero_bon ?? $consultation->patient?->numero_assure ?? 'N/A' }}</td>
                <td>
                    @if(!empty($hosp?->date_entree))
                    {{ \Carbon\Carbon::parse($hosp->date_entree)->format('d/m/Y H:i') }}
                    @elseif(!empty($consultation->date_entree))
                    {{ \Carbon\Carbon::parse($consultation->date_entree)->format('d/m/Y H:i') }}
                    @else
                    N/A
                    @endif
                </td>
                <td>
                    @if(!empty($hosp?->date_sortie_effective))
                    {{ \Carbon\Carbon::parse($hosp->date_sortie_effective)->format('d/m/Y H:i') }}
                    @elseif(!empty($consultation->date_sortie_effective))
                    {{ \Carbon\Carbon::parse($consultation->date_sortie_effective)->format('d/m/Y H:i') }}
                    @else
                    En cours
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    {{-- DÉTAILS DES PRESTATIONS --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width: 12%; text-align: center;">Code</th>
                <th style="width: 22%;">Désignation</th>
                <th style="width: 24%;">Cotation / Détails</th>
                <th style="width: 11%; text-align: right;">P.U</th>
                <th style="width: 5%; text-align: center;">Qté</th>
                <th style="width: 13%; text-align: right;">Ticket mod.</th>
                <th style="width: 13%; text-align: right;">Tiers-payeur</th>
            </tr>
        </thead>
        <tbody>
            {{-- 1. Consultation --}}
            @php
                $partPayeurConsult = ($tarifConsultation * $tauxAssurance) / 100;
                $partTicketConsult = $tarifConsultation - $partPayeurConsult;
            @endphp
            <tr>
                <td style="text-align: center;">{{ $consultation->code_consultation ?? 'N/A' }}</td>
                <td>Acte de Consultation</td>
                <td>Consultation {{ ucfirst(str_replace('_', ' ', $consultation->type ?? 'Générale')) }}</td>
                <td style="text-align: right;">{{ number_format($tarifConsultation, 0, ',', ' ') }}</td>
                <td style="text-align: center;">1</td>
                <td style="text-align: right; color: #1769aa;">{{ number_format($partTicketConsult, 0, ',', ' ') }}</td>
                <td style="text-align: right; color: #198754;">{{ number_format($partPayeurConsult, 0, ',', ' ') }}</td>
            </tr>

            {{-- 2. Section Hospitalisation --}}
            @if(isset($nbLignesHosp) && $nbLignesHosp > 0)
                @php $isFirstHospLine = true; @endphp

                @if($fraisSejour > 0)
                    @php
                        $partPayeurSejour = ($fraisSejour * $tauxAssurance) / 100;
                        $partTicketSejour = $fraisSejour - $partPayeurSejour;
                        $labelSejour = ($derniereHospitalisation?->type_prise_en_charge === 'observation') ? 'Forfait observation' : 'Séjour en chambre';
                    @endphp
                    <tr>
                        @if($isFirstHospLine)
                            <td rowspan="{{ $nbLignesHosp }}" style="text-align: center; vertical-align: middle; font-weight: bold;">
                                {{ $derniereHospitalisation?->code_hospitalisation ?? 'HOSP' }}
                            </td>
                            @php $isFirstHospLine = false; @endphp
                        @endif
                        <td>Frais de Séjour / Nuitées</td>
                        <td>
                            {{ $labelSejour }}<br>
                            <small style="color: #6c757d;">Salle : {{ $derniereHospitalisation?->chambre_number ?? 'N/A' }}</small> &nbsp;
                            <small style="color: #6c757d;">Lit : {{ $derniereHospitalisation?->lit_number ?? 'N/A' }}</small>
                        </td>
                        <td style="text-align: right;">{{ number_format($tarifJour, 0, ',', ' ') }}</td>
                        <td style="text-align: center;">{{ $jours }}</td>
                        <td style="text-align: right; color: #1769aa;">{{ number_format($partTicketSejour, 0, ',', ' ') }}</td>
                        <td style="text-align: right; color: #198754;">{{ number_format($partPayeurSejour, 0, ',', ' ') }}</td>
                    </tr>
                @endif

                @if($fraisSoinsChambre > 0)
                    @php
                        $partPayeurChambre = ($fraisSoinsChambre * $tauxAssurance) / 100;
                        $partTicketChambre = $fraisSoinsChambre - $partPayeurChambre;
                    @endphp
                    <tr>
                        @if($isFirstHospLine)
                            <td rowspan="{{ $nbLignesHosp }}" style="text-align: center; vertical-align: middle; font-weight: bold;">
                                {{ $derniereHospitalisation?->code_hospitalisation ?? 'HOSP' }}
                            </td>
                            @php $isFirstHospLine = false; @endphp
                        @endif
                        <td>Soins et Chambre</td>
                        <td>Chambre {{ $derniereHospitalisation?->chambre_number ?? 'N/A' }}</td>
                        <td style="text-align: right;">{{ number_format($fraisSoinsChambre, 0, ',', ' ') }}</td>
                        <td style="text-align: center;">1</td>
                        <td style="text-align: right; color: #1769aa;">{{ number_format($partTicketChambre, 0, ',', ' ') }}</td>
                        <td style="text-align: right; color: #198754;">{{ number_format($partPayeurChambre, 0, ',', ' ') }}</td>
                    </tr>
                @endif

                @if($totalVisite > 0)
                    @php
                        $partPayeurVisite = ($totalVisite * $tauxAssurance) / 100;
                        $partTicketVisite = $totalVisite - $partPayeurVisite;
                    @endphp
                    <tr>
                        @if($isFirstHospLine)
                            <td rowspan="{{ $nbLignesHosp }}" style="text-align: center; vertical-align: middle; font-weight: bold;">
                                {{ $derniereHospitalisation?->code_hospitalisation ?? 'HOSP' }}
                            </td>
                            @php $isFirstHospLine = false; @endphp
                        @endif
                        <td>Visites Urgentistes</td>
                        <td>Visites médicales d'urgence</td>
                        <td style="text-align: right;">{{ number_format($puVisite, 0, ',', ' ') }}</td>
                        <td style="text-align: center;">{{ $qteVisite }}</td>
                        <td style="text-align: right; color: #1769aa;">{{ number_format($partTicketVisite, 0, ',', ' ') }}</td>
                        <td style="text-align: right; color: #198754;">{{ number_format($partPayeurVisite, 0, ',', ' ') }}</td>
                    </tr>
                @endif

                @if($totalAmi > 0)
                    @php
                        $partPayeurAmi = ($totalAmi * $tauxAssurance) / 100;
                        $partTicketAmi = $totalAmi - $partPayeurAmi;
                    @endphp
                    <tr>
                        @if($isFirstHospLine)
                            <td rowspan="{{ $nbLignesHosp }}" style="text-align: center; vertical-align: middle; font-weight: bold;">
                                {{ $derniereHospitalisation?->code_hospitalisation ?? 'HOSP' }}
                            </td>
                            @php $isFirstHospLine = false; @endphp
                        @endif
                        <td>Soins Infirmiers (AMI)</td>
                        <td>Actes infirmiers médicaux</td>
                        <td style="text-align: right;">{{ number_format($puAmi, 0, ',', ' ') }}</td>
                        <td style="text-align: center;">{{ $qteAmi }}</td>
                        <td style="text-align: right; color: #1769aa;">{{ number_format($partTicketAmi, 0, ',', ' ') }}</td>
                        <td style="text-align: right; color: #198754;">{{ number_format($partPayeurAmi, 0, ',', ' ') }}</td>
                    </tr>
                @endif
            @endif

            {{-- 3. Examens Médicaux --}}
            @php
                $partPayeurExam = ($tarifExamens * $tauxAssurance) / 100;
                $partTicketExam = $tarifExamens - $partPayeurExam;
                $puMoyenExam = $qteExamens > 0 ? ($tarifExamens / $qteExamens) : 0;
            @endphp
            <tr>
                <td style="text-align: center;">EXAM</td>
                <td>Examens Médicaux</td>
                <td>
                    {{ $qteExamens }} examen(s) effectué(s)<br>
                    <small style="color: #6c757d; font-style: italic;">(voir détails ci-joint)</small>
                </td>
                <td style="text-align: right;">{{ number_format($tarifExamens, 0, ',', ' ') }}</td>
                <td style="text-align: center;">{{ $qteExamens }}</td>
                <td style="text-align: right; color: #1769aa;">{{ number_format($partTicketExam, 0, ',', ' ') }}</td>
                <td style="text-align: right; color: #198754;">{{ number_format($partPayeurExam, 0, ',', ' ') }}</td>
            </tr>

            {{-- 4. Pharmacie / Médicaments --}}
            @php
                $partPayeurProd = ($tarifProduits * $tauxAssurance) / 100;
                $partTicketProd = $tarifProduits - $partPayeurProd;
                $puMoyenProd = $qteProduits > 0 ? ($tarifProduits / $qteProduits) : 0;
            @endphp
            <tr>
                <td style="text-align: center;">PHARM</td>
                <td>Pharmacie / Médicaments</td>
                <td>
                    {{ $qteProduits }} médicament(s) / produit(s)<br>
                    <small style="color: #6c757d; font-style: italic;">(voir détails ci-joint)</small>
                </td>
                <td style="text-align: right;">{{ number_format($puMoyenProd, 0, ',', ' ') }}</td>
                <td style="text-align: center;">{{ $qteProduits }}</td>
                <td style="text-align: right; color: #1769aa;">{{ number_format($partTicketProd, 0, ',', ' ') }}</td>
                <td style="text-align: right; color: #198754;">{{ number_format($partPayeurProd, 0, ',', ' ') }}</td>
            </tr>
        </tbody>

        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL GÉNÉRAL :</td>
                <td class="text-right ticket">{{ number_format($partPatient, 0, ',', ' ') }} FCFA</td>
                <td class="text-right insurance">{{ number_format($partAssuranceFinal, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tfoot>
    </table>

    {{-- RÉCAPITULATIF FINANCIER --}}
    <div class="section-title">Récapitulatif financier</div>

    <div class="summary-wrapper">
        <table class="summary">
            <tr>
                <td>Montant brut total</td>
                <td>{{ number_format($totalBrut, 0, ',', ' ') }} FCFA</td>
            </tr>
            @if($partAssuranceFinal > 0)
            <tr>
                <td>Couverture assurance ({{ $tauxAssurance }} %)</td>
                <td class="insurance">{{ number_format($partAssuranceFinal, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endif
            <tr>
                <td>Ticket modérateur à la charge du patient</td>
                <td class="ticket">{{ number_format($partPatient, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr class="net-row">
                <td>NET À PAYER PAR LE PATIENT</td>
                <td>{{ number_format($partPatient, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    {{-- MENTION DU MONTANT EN LETTRES --}}
    <div class="amount-words">
        <strong>Arrêtée la présente facture à la somme de : {{ $montantEnLettres }} FCFA</strong>
        
    </div>

    {{-- BLOC SIGNATURES À 3 COLONNES --}}
   

      <table style="width: 100%; margin-top: 25px; border-collapse: collapse;">
        <tr>
            <!-- Case Signature Patient -->
            <td style="width: 45%; vertical-align: top; border: 1px solid #1f2020; padding: 10px; height: 6px;">
                <!-- Espace réservé pour la signature/cachet patient -->
            </td>

            <!-- Colonne vide d'espacement au milieu -->
            <td style="width: 10%; border: none;"></td>

            <!-- Case Comptabilité avec Total Assurance -->
            <td style="width: 45%; vertical-align: top; border: 1px solid #1f2020; padding: 10px; height: 6px;">
                <div style="font-size: 11px; font-weight: bold; text-align: right;">
                  TOTAL ASSURANCE : {{ number_format($partAssuranceFinal, 0, ',', ' ') }} FCFA
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

    {{-- PIED DE PAGE --}}
    <div class="footer">
        Facture émise le {{ now()->format('d/m/Y à H:i') }} — Centre Médical La Gloire — Merci de votre confiance.
    </div>

</div>
</body>
</html>