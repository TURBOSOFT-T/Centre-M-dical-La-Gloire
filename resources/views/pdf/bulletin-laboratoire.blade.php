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
    <title>Demande d'Examens de Laboratoire - {{ $consultation->code_consultation }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #2b2b2b;
            margin: 0;
            padding: 15px;
        }

        /* En-tête */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .clinic-title {
            font-size: 18px;
            font-weight: bold;
            color: #0d6efd;
            text-transform: uppercase;
        }
        .clinic-sub {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }
        .document-title {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            color: #212529;
            text-transform: uppercase;
        }
        .consultation-code {
            text-align: right;
            font-size: 11px;
            font-weight: bold;
            color: #0d6efd;
        }

        /* Bloc d'Informations (Patient & Prescripteur) */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        .info-table td {
            padding: 8px 12px;
            width: 50%;
            vertical-align: top;
        }
        .info-title {
            font-weight: bold;
            color: #0d6efd;
            font-size: 11px;
            border-bottom: 1px solid #ced4da;
            padding-bottom: 3px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        /* Liste des Examens Prescrits */
        .examens-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .examens-table th {
            background-color: #0d6efd;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            text-transform: uppercase;
        }
        .examens-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
        }
        .examens-table tr:nth-child(even) td {
            background-color: #f8f9fa;
        }

        /* Liste des sous-analyses */
        .sub-list {
            margin: 4px 0 0 0;
            padding-left: 15px;
            color: #333;
        }
        .sub-list li {
            margin-bottom: 2px;
        }

        /* Indication médicale */
        .indication-box {
            background-color: #eef5ff;
            border-left: 3px solid #0d6efd;
            padding: 8px 12px;
            margin-bottom: 20px;
            font-style: italic;
        }

        /* Signatures */
        .signatures {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            height: 70px;
        }

        /* Pied de page */
        .footer {
            position: fixed;
            bottom: 15px;
            left: 15px;
            right: 15px;
            text-align: center;
            font-size: 9px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <!-- EN-TÊTE -->
    <table class="header-table">
        <tr>
            <td>
                <div class="clinic-title">Centre Médical La Gloire</div>
                <div class="clinic-sub">Service de Biologie Médicale & Analyses de Laboratoire</div>
                <div class="clinic-sub">Douala, Cameroun | Tél : (+237) 600 00 00 00</div>
            </td>
             <td class="logo-cell">
                        @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="logo" width="100" height="100" class="logo">
                        @endif
                    </td>
            <td>
                <div class="document-title">Demande d'Examens</div>
                <div class="consultation-code">Consultation N° {{ $consultation->code_consultation }}</div>
                <div class="clinic-sub" style="text-align: right;">Prescrit le : {{ $consultation->date_heure_rdv ? $consultation->date_heure_rdv->format('d/m/Y à H:i') : date('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <!-- PATIENT & PRESCRIPTEUR -->
    <table class="info-table">
        <tr>
            <td>
                <div class="info-title">Identité du Patient</div>
                <strong>Nom & Prénom :</strong> {{ $consultation->patient->nom_complet }}<br>
                <strong>Code Patient :</strong> {{ $consultation->patient->code_patient }}<br>
                <strong>Sexe / Âge :</strong> 
                {{ $consultation->patient->genre === 'M' ? 'Masculin' : 'Féminin' }} 
                ({{ $consultation->patient->date_naissance ? $consultation->patient->date_naissance->age . ' ans' : '-' }})<br>
                <strong>Téléphone :</strong> {{ $consultation->patient->telephone ?? '-' }}
            </td>
            <td>
                <div class="info-title">Médecin Prescripteur</div>
                <strong>Médecin :</strong> Dr. {{ $consultation->medecin?->name ?? $consultation->medecin?->nom ?? 'Non assigné' }}<br>
                <strong>Service :</strong> Consultation {{ ucfirst(str_replace('_', ' ', $consultation->type)) }}<br>
                <strong>Dossier Médical N° :</strong> {{ $consultation->dossier_medical_id ?? '-' }}
            </td>
        </tr>
    </table>

    <!-- TABLEAU DES EXAMENS DEMANDÉS -->
    <table class="examens-table">
        <thead>
            <tr>
                <th style="width: 15%;">Code</th>
                <th style="width: 35%;">Examen Demande</th>
                <th style="width: 50%;">Sous-analyses & Bilan à réaliser</th>
            </tr>
        </thead>
        <tbody>
            @foreach($demandes as $demande)
                <tr>
                    <td><strong>{{ $demande->code_demande }}</strong></td>
                    <td>
                        <strong style="color: #0d6efd;">{{ $demande->examen->nom ?? 'Examen biologique' }}</strong>
                        @if($demande->indication_medicale)
                            <br><small style="color: #6c757d;"><em>Note : {{ $demande->indication_medicale }}</em></small>
                        @endif
                    </td>
                    <td>
                        @if(is_array($demande->analyses_demandees) && count($demande->analyses_demandees) > 0)
                            <ul class="sub-list">
                                @foreach($demande->analyses_demandees as $analyse)
                                    <li><strong>{{ $analyse['nom'] ?? 'Analyse' }}</strong></li>
                                @endforeach
                            </ul>
                        @else
                            <span style="color: #6c757d;">Analyse complète selon protocole</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- SIGNATURES -->
    <table class="signatures">
        <tr>
            <td>
                <strong>Signature & Cachet du Médecin</strong><br>
                <small style="color: #6c757d;">Dr. {{ $consultation->medecin?->name ?? $consultation->medecin?->nom ?? 'Prescripteur' }}</small>
            </td>
            <td>
                <strong>Réception Laboratoire</strong><br>
                <small style="color: #6c757d;">(Date, Heure & Nom du Prélèveur)</small>
            </td>
        </tr>
    </table>

    <!-- PIED DE PAGE -->
    <div class="footer">
        Fiche de prélèvement émise le {{ date('d/m/Y à H:i') }} — Centre Médical La Gloire — Document médical confidentiel.
    </div>

</body>
</html>