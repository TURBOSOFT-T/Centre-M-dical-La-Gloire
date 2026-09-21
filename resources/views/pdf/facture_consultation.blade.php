<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu / Facture - {{ $consultation->code_consultation }}</title>
    <style>
        @page {
            margin: 12mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #2a2a2a;
            font-size: 10pt;
            line-height: 1.3;
        }

        /* EN-TÊTE */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #198754;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .clinic-name {
            font-size: 14pt;
            font-weight: bold;
            color: #198754;
            text-transform: uppercase;
        }
        .clinic-info {
            font-size: 8pt;
            color: #555;
        }
        .invoice-title {
            text-align: right;
            font-size: 14pt;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
        }

        /* INFOS PATIENT & REÇU */
        .box-table {
            width: 100%;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 8px;
        }
        .box-table td {
            font-size: 9pt;
            padding: 2px 4px;
        }

        /* TABLEAU DES PRESTATIONS */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table th {
            background-color: #f1f3f5;
            border-bottom: 2px solid #dee2e6;
            text-align: left;
            padding: 6px 8px;
            font-size: 9pt;
            text-transform: uppercase;
        }
        .details-table td {
            border-bottom: 1px solid #e9ecef;
            padding: 8px;
            font-size: 10pt;
        }

        /* DÉCOMPTE FINANCIER */
        .totals-table {
            width: 60%;
            float: right;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .totals-table td {
            padding: 4px 8px;
            text-align: right;
            font-size: 9.5pt;
        }
        .totals-table .total-row {
            font-weight: bold;
            background-color: #e8f5e9;
            color: #1b5e20;
            font-size: 11pt;
        }

        /* PIED DE PAGE & CAISSE */
        .footer-section {
            clear: both;
            margin-top: 30px;
            width: 100%;
        }
        .stamp-box {
            width: 40%;
            float: right;
            text-align: center;
            border: 1px dashed #adb5bd;
            padding: 10px;
            height: 60px;
            font-size: 8pt;
            color: #6c757d;
        }
        .footer-note {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7.5pt;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <!-- EN-TÊTE -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="clinic-name">Centre Médical La Gloire</div>
                <div class="clinic-info">Soins Médicaux & Maternité - Douala, Cameroun</div>
                <div class="clinic-info">Tél: +237 6xx xx xx xx | Email: caisse@lagloire-medical.cm</div>
            </td>
            <td style="width: 45%; text-align: right;">
                <div class="invoice-title">Reçu de Caisse</div>
                <div style="font-size: 9pt; color: #666;">N° Facture : <strong>FAC-{{ $consultation->code_consultation }}</strong></div>
                <div style="font-size: 9pt; color: #666;">Date : {{ date('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <!-- PATIENT & ASSURANCE -->
    <table class="box-table">
        <tr>
            <td style="width: 50%;"><strong>Patient :</strong> {{ strtoupper($consultation->patient->nom_complet) }}</td>
            <td style="width: 50%;"><strong>Code Patient :</strong> {{ $consultation->patient->code_patient }}</td>
        </tr>
        <tr>
            <td><strong>Téléphone :</strong> {{ $consultation->patient->telephone }}</td>
            <td><strong>Médecin :</strong> {{ $consultation->medecin?->name ?? 'Service Général' }}</td>
        </tr>
        @if($consultation->patient->est_assure && $consultation->patient->assurance)
            <tr>
                <td colspan="2" style="color: #0d6efd; font-weight: bold;">
                    Organisme d'Assurance : {{ $consultation->patient->assurance->nom }} (Matricule: {{ $consultation->patient->matricule_assurance ?? 'N/A' }})
                </td>
            </tr>
        @endif
    </table>

    <!-- TABLEAU DÉSIGNATION -->
    <table class="details-table">
        <thead>
            <tr>
                <th>Désignation de l'acte</th>
                <th>Type</th>
                <th style="text-align: right;">Montant Brut</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Consultation Médicale</strong><br>
                    <small style="color: #666;">Réf: {{ $consultation->code_consultation }}</small>
                </td>
                <td>{{ ucfirst(str_replace('_', ' ', $consultation->type)) }}</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($tarifBrut, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tbody>
    </table>

    <!-- CALCUL DE FACTURATION / TIERS-PAYANT -->
    <table class="totals-table">
        <tr>
            <td>Montant Total Brut :</td>
            <td style="width: 40%;"><strong>{{ number_format($tarifBrut, 0, ',', ' ') }} FCFA</strong></td>
        </tr>
        @if($tauxAssurance > 0)
            <tr style="color: #0d6efd;">
                <td>Prise en Charge Assurance ({{ $tauxAssurance }}%) :</td>
                <td><strong>- {{ number_format($partAssurance, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
        @endif
        <tr class="total-row">
            <td>NET À PAYER (PATIENT) :</td>
            <td><strong>{{ number_format($partPatient, 0, ',', ' ') }} FCFA</strong></td>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 8pt; color: #666; padding-top: 5px;">
                Statut Règlement : 
                @if($consultation->est_paye)
                    <strong style="color: #198754;">PAYÉ (Règlement comptant)</strong>
                @else
                    <strong style="color: #dc3545;">EN ATTENTE DE RÈGLEMENT</strong>
                @endif
            </td>
        </tr>
    </table>

    <div class="footer-section">
        <div class="stamp-box">
            Cachet Caisse / Signature
        </div>
    </div>

    <div class="footer-note">
        Centre Médical La Gloire - Document généré le {{ date('d/m/Y H:i') }} - Merci pour votre confiance.
    </div>

</body>
</html>