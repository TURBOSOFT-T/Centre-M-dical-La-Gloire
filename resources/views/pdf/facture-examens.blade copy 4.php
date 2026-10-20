<!DOCTYPE html>
<html lang="fr">
@php
    use Illuminate\Support\Carbon;

    // 1. Récupération des configurations globales
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

    // 2. Gestion et sécurisation du taux d'assurance (0% à 100%)
    $tauxAssurance = (float) (
        $tauxAssurance
        ?? $consultation->patient?->assurance?->taux
        ?? $consultation->patient?->taux_couverture
        ?? 0
    );
    $tauxAssurance = max(0, min(100, $tauxAssurance));

    // 3. Traitement des demandes d'examens et parsing des analyses/prix
    $examens = $consultation->demandesExamens ?? collect();

    $lignesFacture = [];
    $totalBrut = 0;
    $totalTicketMod = 0;
    $totalTiersPayeur = 0;

    foreach ($examens as $demande) {
        // Extraction et normalisation des caractéristiques de l'examen
        $caracteristiques = $demande->examen?->caracteristiques ?? [];

        if (is_string($caracteristiques)) {
            $caracteristiques = json_decode($caracteristiques, true) ?? [];
        } elseif ($caracteristiques instanceof \Illuminate\Support\Collection) {
            $caracteristiques = $caracteristiques->toArray();
        }

        if (!is_array($caracteristiques)) {
            $caracteristiques = [];
        }

        // Indexation clé => prix des caractéristiques pour recherche rapide
        $prixCaracteristiques = [];
        foreach ($caracteristiques as $cle => $valeur) {
            if (is_array($valeur)) {
                $nom = $valeur['nom'] ?? $valeur['libelle'] ?? null;
                $prix = $valeur['prix'] ?? $valeur['tarif'] ?? null;

                if ($nom !== null && is_numeric($prix)) {
                    $prixCaracteristiques[mb_strtolower(trim((string) $nom))] = (float) $prix;
                }
            } elseif (is_numeric($valeur)) {
                $prixCaracteristiques[mb_strtolower(trim((string) $cle))] = (float) $valeur;
            }
        }

        // Analyses sélectionnées pour cette demande
        $analyses = $demande->analyses_demandees ?? [];

        if (is_string($analyses)) {
            $analyses = json_decode($analyses, true) ?? [];
        } elseif ($analyses instanceof \Illuminate\Support\Collection) {
            $analyses = $analyses->toArray();
        }

        if (!is_array($analyses)) {
            $analyses = [];
        }

        $quantiteDemande = max(1, (int) ($demande->pivot->quantite ?? $demande->quantite ?? 1));

        // Parcourir chaque analyse individuellement pour isoler son prix
        if (count($analyses) > 0) {
            foreach ($analyses as $analyse) {
                $nomAnalyse = is_array($analyse)
                    ? ($analyse['nom'] ?? $analyse['libelle'] ?? $analyse['name'] ?? null)
                    : (is_string($analyse) ? $analyse : null);

                if (!$nomAnalyse) {
                    continue;
                }

                $cleAnalyse = mb_strtolower(trim((string) $nomAnalyse));

                // Recherche du tarif spécifique de l'analyse
                $prixUnitaire = 0;
                if (array_key_exists($cleAnalyse, $prixCaracteristiques)) {
                    $prixUnitaire = $prixCaracteristiques[$cleAnalyse];
                } elseif (is_array($analyse) && isset($analyse['prix']) && is_numeric($analyse['prix'])) {
                    $prixUnitaire = (float) $analyse['prix'];
                } elseif (is_numeric($demande->tarif_brut ?? null) && count($analyses) === 1) {
                    $prixUnitaire = (float) $demande->tarif_brut;
                }

                $prixUnitaire = max(0, $prixUnitaire);
                $montantBrut = $prixUnitaire * $quantiteDemande;
                $partPayeur = ($montantBrut * $tauxAssurance) / 100;
                $partTicket = $montantBrut - $partPayeur;

                $totalBrut += $montantBrut;
                $totalTicketMod += $partTicket;
                $totalTiersPayeur += $partPayeur;

                $lignesFacture[] = [
                    'code' => $demande->code ?? $demande->reference ?? 'EXAM',
                    'categorie' => $demande->examen?->nom ?? $demande->type ?? 'BIOLOGIE',
                    'nom' => $nomAnalyse,
                    'prix_unitaire' => $prixUnitaire,
                    'quantite' => $quantiteDemande,
                    'montant_brut' => $montantBrut,
                    'ticket' => $partTicket,
                    'tiers_payeur' => $partPayeur,
                ];
            }
        } else {
            // Fallback : Examen global si aucune analyse fine n'est isolée
            $prixUnitaire = (float) ($demande->tarif_brut ?? $demande->prix ?? 0);
            $prixUnitaire = max(0, $prixUnitaire);
            $montantBrut = $prixUnitaire * $quantiteDemande;
            $partPayeur = ($montantBrut * $tauxAssurance) / 100;
            $partTicket = $montantBrut - $partPayeur;

            $totalBrut += $montantBrut;
            $totalTicketMod += $partTicket;
            $totalTiersPayeur += $partPayeur;

            $lignesFacture[] = [
                'code' => $demande->code ?? $demande->reference ?? 'EXAM',
                'categorie' => $demande->examen?->nom ?? $demande->type ?? 'BIOLOGIE',
                'nom' => $demande->examen?->nom ?? 'Examen médical',
                'prix_unitaire' => $prixUnitaire,
                'quantite' => $quantiteDemande,
                'montant_brut' => $montantBrut,
                'ticket' => $partTicket,
                'tiers_payeur' => $partPayeur,
            ];
        }
    }

    // 4. Somme en lettres de la part Assurance
    $montantAssuranceArrondi = (int) round($totalTiersPayeur);
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
    <title>Facture examens - {{ $consultation->code_consultation }}</title>

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

        /* Sections & Cadres Patient */
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
        }

        /* Tableau des prestations */
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 7px;
            font-size: 9px;
        }

        .items th {
            background: #17324d;
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

        .category {
            font-weight: 700;
            color: #174e78;
            text-transform: uppercase;
        }

        .empty-row {
            text-align: center;
            padding: 20px !important;
            color: #667085;
            font-style: italic;
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

    {{-- EN-TÊTE DU CLINIQUE --}}
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
                    <img src="{{ $logoBase64 }}" alt="Logo du centre médical" class="logo">
                @endif
            </td>

            <td style="width: 42%;">
                <div class="invoice-title">Facture examens</div>
                <div class="invoice-number">
                    N° {{ $consultation->code_consultation }}
                </div>
                <div class="invoice-date">
                    Date : {{ $dateFacture }}
                </div>
            </td>
        </tr>
    </table>

    {{-- INFOS PATIENT & ASSURANCE --}}
    <table class="patient-table">
        <tr>
            <td>
                <div class="patient-label">Nom du patient : <strong>{{ $nomPatient ?: 'Non renseigné' }}</strong></div>
                <div class="patient-label">Code patient : <strong>{{ $patient?->code_patient ?? 'N/A' }}</strong></div>
                <div class="patient-label">Téléphone : <strong>{{ $patient?->telephone ?? 'N/A' }}</strong></div>
            </td>

            <td>
                <div class="patient-label">Organisme d'assurance : <strong>{{ $patient?->assurance?->nom ?? 'Aucune assurance' }}</strong></div>
                <div class="patient-label">Téléphone assurance : <strong>{{ $patient?->assurance?->telephone ?? 'N/A' }}</strong></div>
                <div class="patient-label">Taux de couverture : <strong>{{ $tauxAssurance }} %</strong></div>
            </td>
        </tr>
    </table>

    {{-- DÉTAIL DES EXAMENS / ANALYSES --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width: 9%;">Code</th>
                <th style="width: 20%;">Catégorie</th>
                <th style="width: 27%;">Analyse demandée</th>
                <th style="width: 11%;" class="text-right">P.U FCFA</th>
                <th style="width: 5%;" class="text-center">Qté</th>
                <th style="width: 12%;" class="text-right">Montant brut</th>
                <th style="width: 13%;" class="text-right">Ticket mod.</th>
                <th style="width: 13%;" class="text-right">Tiers-payeur</th>
            </tr>
        </thead>

        <tbody>
            @forelse($lignesFacture as $ligne)
                <tr>
                    <td class="text-center">
                        {{ $ligne['code'] }}
                    </td>

                    <td class="category">
                        {{ $ligne['categorie'] }}
                    </td>

                    <td>
                        <strong>{{ $ligne['nom'] }}</strong>
                    </td>

                    <td class="text-right">
                        {{ number_format($ligne['prix_unitaire'], 0, ',', ' ') }}
                    </td>

                    <td class="text-center">
                        {{ $ligne['quantite'] }}
                    </td>

                    <td class="text-right">
                        {{ number_format($ligne['montant_brut'], 0, ',', ' ') }}
                    </td>

                    <td class="text-right ticket">
                        {{ number_format($ligne['ticket'], 0, ',', ' ') }}
                    </td>

                    <td class="text-right insurance">
                        {{ number_format($ligne['tiers_payeur'], 0, ',', ' ') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-row">
                        Aucun examen n'a été prescrit pour cette consultation.
                    </td>
                </tr>
            @endforelse
        </tbody>

        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">
                    TOTAL GÉNÉRAL
                </td>

                <td class="text-right">
                    {{ number_format($totalBrut, 0, ',', ' ') }}
                </td>

                <td class="text-right ticket">
                    {{ number_format($totalTicketMod, 0, ',', ' ') }}
                </td>

                <td class="text-right insurance">
                    {{ number_format($totalTiersPayeur, 0, ',', ' ') }}
                </td>
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

            <tr>
                <td>Couverture assurance ({{ $tauxAssurance }} %)</td>
                <td class="insurance">
                    {{ number_format($totalTiersPayeur, 0, ',', ' ') }} FCFA
                </td>
            </tr>

            <tr>
                <td>Ticket modérateur à la charge du patient</td>
                <td class="ticket">
                    {{ number_format($totalTicketMod, 0, ',', ' ') }} FCFA
                </td>
            </tr>

            <tr class="net-row">
                <td>NET À PAYER PAR LE PATIENT</td>
                <td>
                    {{ number_format($totalTicketMod, 0, ',', ' ') }} FCFA
                </td>
            </tr>
        </table>
    </div>

    {{-- ARRÊTÉ DE COMPTE EN LETTRES (ASSURANCE) --}}
    <div class="amount-words">
        <strong>Arrêtée la présente facture à la somme de :</strong>
        <div style="margin-top: 5px; font-weight: 700; text-transform: uppercase;">
            {{ $montantEnLettres }} FCFA
        </div>
    </div>

    {{-- BLOC SIGNATURES À 3 COLONNES --}}
    <table class="signatures">
        <tr>
            <!-- Case Patient -->
            <td>
                <!-- Espace réservé pour signature/cachet patient -->
            </td>

            <!-- Espaceur central -->
            <td class="space"></td>

            <!-- Case Comptabilité -->
            <td>
                <div style="font-size: 11px; font-weight: bold; text-align: right;">
                    TOTAL ASSURANCE : {{ number_format($totalTiersPayeur, 0, ',', ' ') }} FCFA
                </div>
            </td>
        </tr>

        <tr>
            <td class="signature-label">Signature du Patient</td>
            <td class="space"></td>
            <td class="signature-label">La Comptabilité</td>
        </tr>
    </table>

    <div class="footer">
        Facture émise le {{ now()->format('d/m/Y à H:i') }}
        — Centre Médical La Gloire — Merci de votre confiance.
    </div>

</div>
</body>
</html>