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
    <title>Résultats d'Analyses - {{ $consultation->code_consultation }}</title>
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
            font-size: 15px;
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

        /* Patient & Prescripteur */
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

        /* Bloc Examen & Résultats */
        .examen-section {
            margin-bottom: 20px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            overflow: hidden;
        }
        .examen-header {
            background-color: #e2e8f0;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 12px;
            color: #1e293b;
            border-bottom: 1px solid #cbd5e1;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
        }
        .results-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
            text-align: left;
            padding: 6px 10px;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e1;
        }
        .results-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .results-table tr:last-child td {
            border-bottom: none;
        }
        .conclusion-box {
            background-color: #f8fafc;
            border-top: 1px solid #cbd5e1;
            padding: 8px 10px;
            font-size: 10px;
            color: #334155;
        }

        /* Signatures */
        .signatures {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            height: 60px;
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
                <div class="clinic-sub">Service de Biologie Médicale & Résultats d'Analyses</div>
                <div class="clinic-sub">Douala, Cameroun | Tél : (+237) 600 00 00 00</div>
            </td>
            
 <td class="logo-cell">
                        @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="logo" width="100" height="100" class="logo">
                        @endif
                    </td>
            <td>
                <div class="document-title">Compte-Rendu d'Analyses</div>
                <div class="consultation-code">Consultation N° {{ $consultation->code_consultation }}</div>
                <div class="clinic-sub" style="text-align: right;">Édité le : {{ date('d/m/Y à H:i') }}</div>
            </td>
        </tr>
    </table>

    <!-- INFORMATIONS PATIENT & MÉDECIN -->
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
                <div class="info-title">Prescription & Réalisation</div>
                <strong>Médecin Prescripteur :</strong> Dr. {{ $consultation->medecin?->name ?? $consultation->medecin?->nom ?? 'Non assigné' }}<br>
                <strong>Date de Consultation :</strong> {{ $consultation->date_heure_rdv ? $consultation->date_heure_rdv->format('d/m/Y à H:i') : '-' }}<br>
                <strong>Biologiste / Technicien :</strong> 
                @if($demandes->first()?->realisateur)
                    {{ $demandes->first()->realisateur->name ?? $demandes->first()->realisateur->nom }}
                @else
                    Laboratoire Central
                @endif
            </td>
        </tr>
    </table>

    <!-- BOUCLE SUR CHAQUE EXAMEN ET SES RÉSULTATS -->
    @foreach($demandes as $demande)
        <div class="examen-section">
            <div class="examen-header">
                {{ $demande->examen->nom ?? 'Examen de Laboratoire' }} 
                <span style="font-weight: normal; font-size: 10px; color: #475569; float: right;">
                    Réf Demande : {{ $demande->code_demande }} | Statut : {{ ucfirst($demande->statut) }}
                </span>
            </div>

            <table class="results-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Paramètre / Sous-analyse</th>
                        <th style="width: 30%; text-align: center;">Valeur Trouvée (Résultat)</th>
                        <th style="width: 30%; text-align: center;">Valeurs de Référence (Normes)</th>
                    </tr>
                </thead>
                <tbody>
                    @if(is_array($demande->analyses_demandees) && count($demande->analyses_demandees) > 0)
                        @foreach($demande->analyses_demandees as $analyse)
                            <tr>
                                <td><strong>{{ $analyse['nom'] ?? '-' }}</strong></td>
                                <td style="text-align: center; font-weight: bold; color: #0d6efd;">
                                    {{ $analyse['resultat'] ?? 'En attente' }}
                                </td>
                                <td style="text-align: center; color: #64748b;">
                                    {{ $analyse['norme'] ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" style="text-align: center; color: #64748b;">Aucun sous-paramètre renseigné.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if(!empty($demande->conclusion))
                <div class="conclusion-box">
                    <strong>Conclusion / Interprétation :</strong> {{ $demande->conclusion }}
                </div>
            @endif
        </div>
    @endforeach

    <!-- SIGNATURES -->
    <table class="signatures">
        <tr>
            <td>
                <strong>Le Technicien / Prélèveur</strong><br>
                <small style="color: #64748b;">(Signature)</small>
            </td>
            <td>
                <strong>Validation du Biologiste</strong><br>
                <small style="color: #64748b;">(Cachet & Signature)</small>
            </td>
        </tr>
    </table>

    <!-- PIED DE PAGE -->
    <div class="footer">
        Centre Médical La Gloire — Résultats validés informatiquement. Document strictement confidentiel.
    </div>

</body>
</html>