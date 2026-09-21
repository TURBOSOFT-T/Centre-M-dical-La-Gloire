<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ordonnance Médicale - {{ $consultation->code_consultation }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #2b2b2b;
            font-size: 11pt;
            line-height: 1.4;
        }

        /* EN-TÊTE DU CENTRE MÉDICAL LA GLOIRE */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .clinic-name {
            font-size: 16pt;
            font-weight: bold;
            color: #0d6efd;
            text-transform: uppercase;
            margin: 0;
        }
        .clinic-sub {
            font-size: 9pt;
            color: #555;
            margin-top: 3px;
        }
        .clinic-contact {
            font-size: 8pt;
            color: #666;
            text-align: right;
        }

        /* BLOC PATIENT & CONSULTATION */
        .info-box {
            width: 100%;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 8px 12px;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            padding: 2px 0;
            font-size: 10pt;
        }

        /* TITRE ORDONNANCE */
        .doc-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 20px 0 15px 0;
            color: #111;
            text-decoration: underline;
        }

        /* CORPS DE L'ORDONNANCE */
        .ordonnance-content {
            min-height: 250px;
            font-size: 11pt;
            white-space: pre-line; /* Conserve les sauts de ligne */
            padding: 10px;
        }

        /* PIED DE PAGE & SIGNATURE */
        .footer-table {
            width: 100%;
            margin-top: 30px;
        }
        .signature-box {
            text-align: right;
            padding-right: 20px;
        }
        .signature-title {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 50px;
        }
        .footer-note {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <!-- EN-TÊTE DU CENTRE MÉDICAL -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="clinic-name">Centre Médical La Gloire</div>
                <div class="clinic-sub">Soins Généraux - Maternité - Soins Spécialisés</div>
            </td>
            <td class="clinic-contact">
                <strong>Douala, Cameroun</strong><br>
                Téléphone : +237 6xx xx xx xx<br>
                Email : contact@lagloire-medical.cm
            </td>
        </tr>
    </table>

    <!-- BLOC INFORMATION PATIENT -->
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td style="width: 55%;"><strong>Patient :</strong> {{ strtoupper($consultation->patient->nom_complet) }}</td>
                <td style="width: 45%; text-align: right;"><strong>Date :</strong> {{ $consultation->date_heure_rdv->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td><strong>Code Dossier :</strong> {{ $consultation->patient->code_patient }}</td>
                <td style="text-align: right;">
                    <strong>Âge / Genre :</strong> 
                    {{ $consultation->patient->date_naissance ? $consultation->patient->date_naissance->age . ' ans' : '-' }} / {{ $consultation->patient->genre }}
                </td>
            </tr>
            @if($consultation->patient->est_assure && $consultation->patient->assurance)
                <tr>
                    <td colspan="2">
                        <strong>Couverture Santé :</strong> {{ $consultation->patient->assurance->code }} 
                        @if($consultation->patient->matricule_assurance) 
                            (Matricule: {{ $consultation->patient->matricule_assurance }})
                        @endif
                    </td>
                </tr>
            @endif
        </table>
    </div>

    <!-- TITRE -->
    <div class="doc-title">Ordonnance Médicale</div>

    <!-- PRESCRIPTION -->
    <div class="ordonnance-content">
        {{ $consultation->ordonnance }}
    </div>

    <!-- SIGNATURE MÉDECIN -->
    <table class="footer-table">
        <tr>
            <td style="width: 50%;">
                <small style="color: #666;">Document délivré par le système informatique de santé du Centre Médical La Gloire.</small>
            </td>
            <td class="signature-box" style="width: 50%;">
                <div class="signature-title">
                    Le Médecin Traitant<br>
                    <span style="font-weight: normal; font-size: 9pt;">{{ $consultation->medecin?->name ?? 'Dr. Médecin de garde' }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- BAS DE PAGE -->
    <div class="footer-note">
        Centre Médical La Gloire - Réf: {{ $consultation->code_consultation }} - Imprimé le {{ date('d/m/Y H:i') }}
    </div>

</body>
</html>