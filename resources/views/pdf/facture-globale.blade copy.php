<!DOCTYPE html>
<html lang="fr">
@php
$config = DB::table('configs')->select('icon', 'logo', 'telephone', 'email', 'addresse')->first();

// Détermination du logo
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

// 1. Consultation de base
$tarifConsultation = $tarifConsultation ?? ($consultation->tarif_brut ?? 5000);

// 2. Examens prescrits
$tarifExamens = $tarifExamens ?? ($consultation->demandesExamens ? $consultation->demandesExamens->sum('tarif_brut') : 0);
$qteExamens = $consultation->demandesExamens ? $consultation->demandesExamens->count() : 0;

// 3. Produits & Médicaments
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

// 4. Dernière hospitalisation et ventilation détaillée
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

// Calcul du nombre de sous-lignes d'hospitalisation actives pour le rowspan de la colonne Code
$nbLignesHosp = 0;
if ($fraisSejour > 0) $nbLignesHosp++;
if ($fraisSoinsChambre > 0) $nbLignesHosp++;
if ($totalVisite > 0) $nbLignesHosp++;
if ($totalAmi > 0) $nbLignesHosp++;

$tarifHospitalisation = $fraisSejour + $fraisSoinsChambre + $totalVisite + $totalAmi;

// TOTAL BRUT GLOBAL
$totalBrut = $tarifConsultation + $tarifExamens + $tarifProduits + $tarifHospitalisation;
$tarifBrut = $totalBrut;

// CALCUL DE LA PART ASSURANCE ET NET PATIENT
$tauxAssurance = $tauxAssurance ?? ($consultation->patient?->assurance?->taux ?? 0);

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
@endphp

<head>
    <meta charset="UTF-8">
    <title>Facture Globale - {{ $consultation->code_consultation }}</title>
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
            color: #28a745;
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
            font-size: 12px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 6px;
            color: #28a745;
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

        .table-items th,
        .table-items td {
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
            margin-top: 15px;
            float: right;
            width: 45%;
        }

        .totals table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals td {
            padding: 6px;
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-success {
            color: #198754;
        }

        .clear {
            clear: both;
        }

        .footer {
            margin-top: 30px;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
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
                <div class="invoice-title">Facture Globale</div>
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
                <strong>Téléphone :</strong> {{ $consultation->patient?->telephone ?? 'N/A' }}
            </td>
            <td width="50%" style="text-align: right;">
                <strong>Assurance :</strong> {{ $consultation->patient?->assurance?->nom ?? 'Aucune' }}<br>
                <strong>Téléphone Assur. :</strong> {{ $consultation->patient?->assurance?->telephone ?? 'N/A' }}<br>
                <strong>Assuré Principal :</strong> {{ $consultation->patient?->nom_assure ?? 'Patient lui-même' }}<br>
                <strong>Taux Couverture :</strong> {{ $tauxAssurance }}%
            </td>
        </tr>
    </table>

    <!-- TABLEAU 1 : INFORMATIONS SÉJOUR & HOSPITALISATION -->

    <table class="table-items">
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

    <!-- TABLEAU 2 : DÉTAILS DES PRESTATIONS -->


    <table class="table-items">
        <thead>
            <tr>
                <th style="width: 12%; text-align: center;">Code</th>
                <th style="width: 22%;">Désignation</th>
                <th style="width: 24%;">Cotation / Détails</th>
                <th style="width: 11%; text-align: right;">P.U</th>
                <th style="width: 5%; text-align: center;">Qté</th>
                <th style="width: 13%; text-align: right;">Mt.Ticket.Mod</th>
                <th style="width: 13%; text-align: right;">Mt.T.Payeur</th>
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
                <td style="text-align: right;">{{ number_format($partTicketConsult, 0, ',', ' ') }}</td>
                <td style="text-align: right;">{{ number_format($partPayeurConsult, 0, ',', ' ') }}</td>
            </tr>

            {{-- 2. Section Hospitalisation (Jumelée sous le code hospitalisation si active) --}}
            @if(isset($nbLignesHosp) && $nbLignesHosp > 0)
            @php $isFirstHospLine = true; @endphp

            @if($fraisSejour > 0)
            @php
            $partPayeurSejour = ($fraisSejour * $tauxAssurance) / 100;
            $partTicketSejour = $fraisSejour - $partPayeurSejour;
            $labelSejour = ($derniereHospitalisation?->type_prise_en_charge === 'observation') ? 'Forfait observation' : "Séjour en chambre";
            @endphp
            <tr>
                @if($isFirstHospLine)
                <td rowspan="{{ $nbLignesHosp }}" style="text-align: center; vertical-align: middle; font-weight: bold;">
                    {{ $derniereHospitalisation?->code_hospitalisation ?? 'N/A' }}
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
                <td style="text-align: right;">{{ number_format($partTicketSejour, 0, ',', ' ') }}</td>
                <td style="text-align: right;">{{ number_format($partPayeurSejour, 0, ',', ' ') }}</td>
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
                    {{ $derniereHospitalisation?->code_hospitalisation ?? 'N/A' }}
                </td>
                @php $isFirstHospLine = false; @endphp
                @endif
                <td>Soins et Chambre</td>
                <td>Chambre {{ $derniereHospitalisation?->chambre_number ?? 'N/A' }}</td>
                <td style="text-align: right;">{{ number_format($fraisSoinsChambre, 0, ',', ' ') }}</td>
                <td style="text-align: center;">1</td>
                <td style="text-align: right;">{{ number_format($partTicketChambre, 0, ',', ' ') }}</td>
                <td style="text-align: right;">{{ number_format($partPayeurChambre, 0, ',', ' ') }}</td>
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
                    {{ $derniereHospitalisation?->code_hospitalisation ?? 'N/A' }}
                </td>
                @php $isFirstHospLine = false; @endphp
                @endif
                <td>Visites Urgentistes</td>
                <td>Visites médicales d'urgence</td>
                <td style="text-align: right;">{{ number_format($puVisite, 0, ',', ' ') }}</td>
                <td style="text-align: center;">{{ $qteVisite }}</td>
                <td style="text-align: right;">{{ number_format($partTicketVisite, 0, ',', ' ') }}</td>
                <td style="text-align: right;">{{ number_format($partPayeurVisite, 0, ',', ' ') }}</td>
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
                    {{ $derniereHospitalisation?->code_hospitalisation ?? 'N/A' }}
                </td>
                @php $isFirstHospLine = false; @endphp
                @endif
                <td>Soins Infirmiers (AMI)</td>
                <td>Actes infirmiers médicaux</td>
                <td style="text-align: right;">{{ number_format($puAmi, 0, ',', ' ') }}</td>
                <td style="text-align: center;">{{ $qteAmi }}</td>
                <td style="text-align: right;">{{ number_format($partTicketAmi, 0, ',', ' ') }}</td>
                <td style="text-align: right;">{{ number_format($partPayeurAmi, 0, ',', ' ') }}</td>
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
                <td style="text-align: right;">{{ number_format($partTicketExam, 0, ',', ' ') }}</td>
                <td style="text-align: right;">{{ number_format($partPayeurExam, 0, ',', ' ') }}</td>
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
                    {{ isset($consultation->produits) ? $consultation->produits->count() : 0 }} médicament(s) prescrit(s)<br>
                    <small style="color: #6c757d; font-style: italic;">(voir détails ci-joint)</small>
                </td>
                <td style="text-align: right;">{{ number_format($puMoyenProd, 0, ',', ' ') }}</td>
                <td style="text-align: center;">{{ $qteProduits }}</td>
                <td style="text-align: right;">{{ number_format($partTicketProd, 0, ',', ' ') }}</td>
                <td style="text-align: right;">{{ number_format($partPayeurProd, 0, ',', ' ') }}</td>
            </tr>
        </tbody>

        <!-- LIGNE TOTAL DU TABLEAU -->
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold; border-top: 2px solid #333;">
                <td colspan="3" style="text-align: right; text-transform: uppercase;">TOTAL GENERAL :</td>
                <td colspan="2" style="text-align: right;">{{ number_format($totalBrut, 0, ',', ' ') }} FCFA</td>
                <td style="text-align: right; color: #0d6efd;">{{ number_format($partPatient, 0, ',', ' ') }}</td>
                <td style="text-align: right; color: #198754;">{{ number_format($partAssuranceFinal, 0, ',', ' ') }}</td>
            </tr>
        </tfoot>
    </table>



    <!-- TOTALS ET RÉCAPITULATIF FINANCIER -->
    <div class="totals">
        <table>
            <tr>
                <td>Total Général Brut :</td>
                <td class="text-bold">{{ number_format($totalBrut, 0, ',', ' ') }} FCFA</td>
            </tr>
            @if($partAssuranceFinal > 0)
            <tr>
                <td class="text-success">Prise en charge Assurance ({{ $tauxAssurance }}%) :</td>
                <td class="text-bold text-success">- {{ number_format($partAssuranceFinal, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endif
            <tr style="border-top: 2px solid #212529;">
                <td class="text-bold" style="font-size: 13px;">Net à Payer Patient :</td>
                <td class="text-bold" style="font-size: 13px; color: #28a745;">{{ number_format($partPatient, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>




    <div class="clear"></div>
    @php
    // Fonction native de secours si l'extension intl n'est pas active
    function spellNumberFr($n) {
    if (class_exists('NumberFormatter')) {
    $formatter = new \NumberFormatter('fr', \NumberFormatter::SPELLOUT);
    return ucfirst($formatter->format($n));
    }
    // Fallback si intl absent (affiche le nombre en toutes lettres de base ou formaté)
    return number_format($n, 0, ',', ' ');
    }

    $partAssuranceEnLettres = spellNumberFr(round($partAssuranceFinal));
    @endphp

    <!-- MENTION DU MONTANT EN LETTRES -->
    <div style="margin-top: 20px; padding: 10px; background-color: #f8f9fa; border-left: 4px solid #28a745; font-size: 11px;">
        <strong>Arrêtée la présente facture à la somme de :</strong>
        <span style="font-style: italic; text-transform: uppercase; font-weight: bold;">
            {{ $partAssuranceEnLettres }} FCFA
        </span>
    </div>
    <div class="clear"></div>
    <!-- BLOC SIGNATURES -->
   <table style="width: 100%; margin-top: 30px; border-collapse: collapse;">
    <tr>
        <!-- Case Signature Patient -->
        <td style="width: 40%; vertical-align: top; border: 1px solid #1f2020; padding: 10px; height: 6px;">
            <!-- Espace réservé pour la signature/cachet patient -->
        </td>
        
        <!-- Colonne vide du milieu (espacement) -->
        <td style="width: 20%; border: none;"></td>

        <!-- Case Comptabilité avec Total Assurance -->
        <td style="width: 40%; vertical-align: top; border: 1px solid #1f2020; padding: 10px; height: 6px;">
            <div style="font-size: 11px; font-weight: bold; text-align: right;">
                TOTAL ASSURANCE : {{ number_format($partAssuranceFinal, 0, ',', ' ') }} FCFA
            </div>
        </td>
    </tr>
    <tr>
        <!-- Titre Signature Patient -->
        <td style="width: 40%; text-align: center; vertical-align: top; padding-top: 6px;">
            <p style="font-weight: bold; margin: 0;">Signature du Patient</p>
        </td>
        
        <!-- Espace vide sous la colonne du milieu -->
        <td style="width: 20%;"></td>

        <!-- Titre Comptabilité -->
        <td style="width: 40%; text-align: center; vertical-align: top; padding-top: 6px;">
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