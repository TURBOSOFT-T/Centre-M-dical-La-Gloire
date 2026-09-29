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
            width: 45%;
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
                <div class="clinic-sub">Soins généraux, Laboratoire d'Analyses & Radiologie</div>
                <div class="clinic-sub">Téléphone : (+237) 600 00 00 00 | Douala, Cameroun</div>
            </td>

           <td class="logo-cell">
                        @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="logo" width="100" height="100" class="logo">
                        @endif
                    </td>
            <td>
                <div class="invoice-title">Facture / Reçu</div>
                <div class="invoice-code">N° {{ $consultation->code_consultation }}</div>
                <div class="clinic-sub" style="text-align: right;">Date : {{ $consultation->date_heure_rdv ? $consultation->date_heure_rdv->format('d/m/Y à H:i') : date('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <!-- INFORMATIONS PATIENT ET MÉDECIN -->
    <table class="info-box">
        <tr>
            <td>
                <div class="box-header">Informations Patient</div>
                <strong>Nom & Prénom :</strong> {{ $consultation->patient->nom_complet }}<br>
                <strong>Code Patient :</strong> {{ $consultation->patient->code_patient }}<br>
                <strong>Téléphone :</strong> {{ $consultation->patient->telephone ?? '-' }}<br>
                @if($consultation->patient->est_assure && $consultation->patient->assurance)
                    <strong>Assurance :</strong> {{ $consultation->patient->assurance->code }} (Couverture : {{ $tauxAssurance }}%)
                @else
                    <strong>Couverture :</strong> Patient non assuré (100% à charge)
                @endif
            </td>
            <td>
                <div class="box-header">Prise en charge Médicale</div>
                <strong>Médecin traitant :</strong> Dr. {{ $consultation->medecin?->name ?? $consultation->medecin?->nom ?? 'Non assigné' }}<br>
                <strong>Acte :</strong> Consultation {{ ucfirst(str_replace('_', ' ', $consultation->type)) }}<br>
                <strong>Statut Caisse :</strong> 
                @if($consultation->est_paye)
                    <span class="badge badge-success">Réglé (Payé)</span>
                @else
                    <span class="badge badge-warning">En attente de paiement</span>
                @endif
            </td>
        </tr>
    </table>

    <!-- TABLEAU DÉTAILLÉ DES ACTES ET EXAMENS PRESCRITS -->
    <table class="details-table">
        <thead>
            <tr>
                <th style="width: 10%;">Type</th>
                <th style="width: 55%;">Désignation des Prestations & Sous-analyses</th>
                <th style="width: 10%; text-align: center;">Qté</th>
                <th style="width: 25%; text-align: right;">Montant Brut</th>
            </tr>
        </thead>
        <tbody>
            {{-- Acte de Consultation --}}
            <tr>
                <td><span class="badge badge-success">Acte</span></td>
                <td>
                    <strong>Consultation Médicale ({{ ucfirst(str_replace('_', ' ', $consultation->type)) }})</strong>
                </td>
                <td style="text-align: center;">1</td>
                <td style="text-align: right;">{{ number_format($tarifConsultation, 0, ',', ' ') }} FCFA</td>
            </tr>

            {{-- Examens et sous-analyses prescrits --}}
            @foreach($consultation->demandesExamens as $demande)
                <tr>
                    <td><span class="badge badge-warning">Examen</span></td>
                    <td>
                        <strong>{{ $demande->examen->nom ?? 'Examen biologique / imagerie' }}</strong> 
                        <small style="color: #6c757d;">({{ $demande->code_demande }})</small>
                        @if(is_array($demande->analyses_demandees) && count($demande->analyses_demandees) > 0)
                            <br>
                            <small style="color: #495057;">
                                <em>Détails :</em> 
                                {{ implode(', ', array_column($demande->analyses_demandees, 'nom')) }}
                            </small>
                        @endif
                    </td>
                    <td style="text-align: center;">1</td>
                    <td style="text-align: right;">{{ number_format($demande->tarif_brut, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- RÉCAPITULATIF FINANCIER -->
    <table class="totals-table">
        <tr>
            <td>Actes & Consultations :</td>
            <td style="text-align: right;">{{ number_format($tarifConsultation, 0, ',', ' ') }} FCFA</td>
        </tr>
        @if($tarifExamens > 0)
        <tr>
            <td>Examens prescrits :</td>
            <td style="text-align: right;">{{ number_format($tarifExamens, 0, ',', ' ') }} FCFA</td>
        </tr>
        @endif
        <tr>
            <td><strong>Total Brut :</strong></td>
            <td style="text-align: right;"><strong>{{ number_format($totalBrut, 0, ',', ' ') }} FCFA</strong></td>
        </tr>
        @if($tauxAssurance > 0)
        <tr style="color: #198754;">
            <td>Prise en charge Assurance ({{ $tauxAssurance }}%) :</td>
            <td style="text-align: right;">- {{ number_format($partAssurance, 0, ',', ' ') }} FCFA</td>
        </tr>
        @endif
        <tr class="total-row">
            <td>Net à Payer (Patient) :</td>
            <td style="text-align: right; color: #0d6efd;">{{ number_format($partPatient, 0, ',', ' ') }} FCFA</td>
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
                <strong>La Caisse / Le Perception</strong><br>
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