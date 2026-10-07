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
    <title>Bilan Médical - {{ $patient->nom_complet }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 13px;
            color: #212529;
        }

        .bg-header {
            background-color: #0d6efd;
            color: white;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 0;
            }
        }
    </style>

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
            font-size: 8pt;
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
            white-space: pre-line;
            /* Conserve les sauts de ligne */
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

<body class="p-4 bg-white" onload="window.print()">

    {{-- Actions / Impression --}}
    <div class="no-print mb-4 d-flex justify-content-between align-items-center">
        <a href="javascript:history.back()" class="btn btn-secondary btn-sm">&larr; Retour au dossier</a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">Imprimer le Rapport Médical</button>
    </div>

    {{-- En-tête Médical --}}
    <div class="row border-bottom pb-3 mb-4 align-items-center">
        <!--  <div class="col-6">
            <h3 class="fw-bold text-primary mb-1">CENTRE MÉDICAL LA GLOIRE</h3>
            <p class="mb-0 text-muted small">Médecine Générale - Spécialités - Soins & Chirurgie</p>
            <p class="mb-0 text-muted small">Douala, Cameroun - Tél : {{ $config->telephone }}</p>
            <p class="mb-0 text-muted small"> E-mail : {{ $config->email }}</p>

        </div>
        <div class="col-4">
            @if(!empty($logoBase64))
            <img src="{{ $logoBase64 }}" alt="logo" class="logo">
            @endif
        </div>

        <div class="col-2 text-end">
            <h5 class="fw-bold text-dark mb-0">RAPPORT & BILAN MÉDICAL</h5>
            <small class="text-muted">Édité le {{ date('d/m/Y à H:i') }}</small>
        </div> -->
        <table class="header-table">
            <tr>
                <td style="width: 45%;">
                    <h3 class="fw-bold text-primary mb-1">CENTRE MÉDICAL LA GLOIRE</h3>

                    <p class="mb-0 text-muted small"> Tél : {{ $config->telephone }}</p>
                    <p class="mb-0 text-muted small"> E-mail : {{ $config->email }}</p>
                    <p class="mb-0 text-muted small"> Adresse : {{ $config->addresse }}</p>

                </td>
                <td class="logo-cell">
                    @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="logo" width=" 100" height="100" class="logo">
                    @endif
                </td>
                <td class="clinic-contact">
                    <strong>Douala, Cameroun</strong><br>
                    Code du dossier: {{ $dossier->code_dossier }}<br>
                    Date : {{ date('d/m/Y à H:i') }}<br>

                    <strong>RAPPORT & BILAN MÉDICAL</strong>

                </td>
            </tr>
        </table>
    </div>

    {{-- Informations Patient & Dossier --}}
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="border rounded p-3 bg-light">
                <h6 class="fw-bold text-primary mb-2 border-bottom pb-1">IDENTITÉ DU PATIENT</h6>
                <p class="mb-1"><strong>Nom & Prénom :</strong> {{ $patient->nom_complet }}</p>
                <p class="mb-1"><strong>Code Patient :</strong> {{ $patient->code_patient }}</p>
                <p class="mb-1"><strong>Âge / Sexe :</strong> {{ $patient->age ? $patient->age . ' ans' : '-' }} / {{ $patient->genre === 'M' ? 'Masculin' : 'Féminin' }}</p>
                <p class="mb-0"><strong>Téléphone :</strong> {{ $patient->telephone }}</p>
            </div>
        </div>
        <div class="col-6">
            <div class="border rounded p-3 bg-light">
                <h6 class="fw-bold text-primary mb-2 border-bottom pb-1">PROFIL MÉDICAL & COUVERTURE</h6>
                <p class="mb-1"><strong>Code Dossier :</strong> {{ $dossier->code_dossier }}</p>
                <p class="mb-1"><strong>Groupe Sanguin :</strong> <span class="badge bg-danger">{{ $dossier->groupe_sanguin ?: 'Non spécifié' }}</span></p>
                <p class="mb-1"><strong>Couverture :</strong> {{ optional($patient->assurance)->nom ?? 'Privé' }} ({{ $patient->taux_couverture }}%)</p>
                <p class="mb-0"><strong>Allergies Connues :</strong> {{ $dossier->allergies ?: 'Aucune' }}</p>
            </div>
        </div>
    </div>
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">1. ANTECEDENTS </h6>
    <div class="card mb-4 border-primary shadow-sm">
        <div class="card-body bg-light">

           {{ $derniereConsultation->antecedents_maladie ?: 'Aucun antécédent médical spécifique consigné.' }}      
        </div>
    </div>


     <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">2. EXAMEN GENERAL </h6>
    <div class="card mb-4 border-primary shadow-sm">
        <div class="card-body bg-light">

           {{ $derniereConsultation->examen_general ?: 'Aucun examen général effectué.' }}      
        </div>
    </div>


       <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">3. EXAMEN PHYSIQUE </h6>
    <div class="card mb-4 border-primary shadow-sm">
        <div class="card-body bg-light">

           {{ $derniereConsultation->examen_physique ?: 'Aucun examen physique effectué.' }}      
        </div>
    </div>



    
     <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">4. TRAITEMENT RECU </h6>
    <div class="card mb-4 border-primary shadow-sm">
        <div class="card-body bg-light">

               @if($derniereConsultation->produits && $derniereConsultation->produits->count() > 0)
                <div class="col-12 mt-2">
                    <strong class="text-dark">Médicaments / Produits prescrits :</strong>
                    <ul class="list-group list-group-flush mt-1">
                        @foreach($derniereConsultation->produits as $produit)
                        <li class="list-group-item bg-white d-flex justify-content-between align-items-center py-2">
                            <div>
                                <span class="fw-bold">{{ $produit->nom ?? $produit->libelle }}</span>
                                <br><small class="text-muted">Posologie : {{ $produit->pivot->posologie ?? 'N/A' }} | Voie : {{ $produit->pivot->voie_administration ?? 'N/A' }}</small>
                            </div>
                            <span class="badge bg-primary">Qté : {{ $produit->pivot->quantite }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif    
        </div>
    </div>

    {{-- SECTION : DÉTAILS DE LA DERNIÈRE CONSULTATION --}}
    @if(isset($derniereConsultation) && $derniereConsultation)
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">5. SYNTHÈSE DE LA DERNIÈRE CONSULTATION ({{ optional($derniereConsultation->date_heure_rdv ?? $derniereConsultation->created_at)->format('d/m/Y') }})</h6>
    <div class="card mb-4 border-primary shadow-sm">
        <div class="card-body bg-light">
            <div class="row g-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Médecin Traitant :</strong> Dr. {{ optional($derniereConsultation->medecin)->nom ?? optional($derniereConsultation->medecin)->name ?? 'Non spécifié' }}</p>
                    <p class="mb-1"><strong>Motif de consultation :</strong> {{ $derniereConsultation->motif ?: 'N/A' }}</p>
                    <p class="mb-1"><strong>Diagnostic Posé :</strong> <strong class="text-success">{{ $derniereConsultation->diagnostic ?: 'N/A' }}</strong></p>
                    <p class="mb-0"><strong>Hypothèse Diagnostique :</strong> {{ $derniereConsultation->hypothese_diagnostique ?: 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Examen Clinique / Physique :</strong> {{ $derniereConsultation->examen_physique ?: 'N/A' }}</p>
                    @if(!empty($derniereConsultation->constantes) && is_array($derniereConsultation->constantes))
                    <p class="mb-0"><strong>Constantes :</strong>
                        @foreach($derniereConsultation->constantes as $key => $val)
                        <span class="badge bg-secondary me-1">{{ ucfirst($key) }}: {{ $val }}</span>
                        @endforeach
                    </p>
                    @endif
                </div>
            </div>

            <table class="table table-bordered mb-4 align-middle">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>Médecin Praticien</th>
                <th>Motif</th>
                <th>Diagnostic Posé</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dossier->consultations as $c)
            <tr>
                <td>{{ optional($c->date_heure_rdv ?? $c->created_at)->format('d/m/Y') }}</td>
                <td>Dr. {{ optional($c->medecin)->nom ?? optional($c->medecin)->name ?? 'Praticien' }}</td>
                <td>{{ $c->motif ?: '-' }}</td>
                <td><strong class="text-success">{{ $c->diagnostic ?: '-' }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Aucune consultation enregistrée.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
        
        </div>
    </div>
    @endif
  
  

    {{-- Perspectives & Recommandations Thérapeutiques (Issues de la dernière consultation) --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">6. PERSPECTIVES & RECOMMANDATIONS THÉRAPEUTIQUES</h6>
    <div class="card mb-4 border-primary p-3 bg-white">
        {{-- Affichage de l'ordonnance si renseignée --}}
        @if(!empty($derniereConsultation->ordonnance))
        <div class="mb-3 p-2 bg-light border rounded">
           
            <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $derniereConsultation->ordonnance }}</p>
        </div>
        @endif

        
    </div>

    {{-- Signatures --}}
    <div class="row mt-5 pt-3">
        <div class="col-6">
            <p class="text-muted small">Document généré automatiquement pour valoir ce que de droit.</p>
        </div>
        <div class="col-6 text-center">
            <p class="mb-5 fw-bold">Le Médecin Chef / Directeur Médical</p>
            <p class="text-muted small mt-4">(Nom, Signature & Cachet de la Formation Sanitaire)</p>
        </div>
    </div>

</body>

</html>