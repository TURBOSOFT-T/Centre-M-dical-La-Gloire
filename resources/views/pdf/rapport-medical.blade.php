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

    $consultationActuelle = $derniereConsultation ?? $consultation ?? null;
@endphp

<head>
    <meta charset="UTF-8">
    <title>Bilan Médical - {{ $patient->nom_complet ?? 'Patient' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            margin: 15mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #2b2b2b;
            font-size: 11pt;
            line-height: 1.4;
            padding: 15px;
            background: #fff;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .clinic-contact {
            font-size: 8pt;
            color: #666;
            text-align: right;
        }

        .card {
            border-radius: 6px;
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
</head>

<body>

    {{-- Actions / Impression --}}
    <div class="no-print mb-4 d-flex justify-content-between align-items-center">
        <a href="javascript:history.back()" class="btn btn-secondary btn-sm">&larr; Retour au dossier</a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">Imprimer le Rapport Médical</button>
    </div>

    {{-- En-tête Médical --}}
    <table class="header-table">
        <tr>
            <td style="width: 45%;">
                <h3 class="fw-bold text-primary mb-1" style="font-size: 16px;">CENTRE MÉDICAL LA GLOIRE</h3>
                <p class="mb-0 text-muted small">Tél : {{ $config->telephone ?? 'N/A' }}</p>
                <p class="mb-0 text-muted small">E-mail : {{ $config->email ?? 'N/A' }}</p>
                <p class="mb-0 text-muted small">Adresse : {{ $config->addresse ?? 'N/A' }}</p>
            </td>
            <td style="width: 15%; text-align: center;">
                @if(!empty($logoBase64))
                <img src="{{ $logoBase64 }}" alt="logo" width="80" height="80" style="object-fit: contain;">
                @endif
            </td>
            <td class="clinic-contact" style="width: 40%;">
                <strong>Douala, Cameroun</strong><br>
                Code du dossier: {{ $dossier->code_dossier ?? 'N/A' }}<br>
                Date : {{ date('d/m/Y à H:i') }}<br>
                <strong class="text-primary">RAPPORT & BILAN MÉDICAL</strong>
            </td>
        </tr>
    </table>

    {{-- Informations Patient & Dossier --}}
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="border rounded p-3 bg-light h-100">
                <h6 class="fw-bold text-primary mb-2 border-bottom pb-1" style="font-size: 12px;">IDENTITÉ DU PATIENT</h6>
                <p class="mb-1"><strong>Nom & Prénom :</strong> {{ $patient->nom_complet ?? 'N/A' }}</p>
                <p class="mb-1"><strong>Code Patient :</strong> {{ $patient->code_patient ?? 'N/A' }}</p>
                <p class="mb-1"><strong>Âge / Sexe :</strong> {{ $patient->age ? $patient->age . ' ans' : '-' }} / {{ $patient->genre === 'M' ? 'Masculin' : 'Féminin' }}</p>
                <p class="mb-0"><strong>Téléphone :</strong> {{ $patient->telephone ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="col-6">
            <div class="border rounded p-3 bg-light h-100">
                <h6 class="fw-bold text-primary mb-2 border-bottom pb-1" style="font-size: 12px;">PROFIL MÉDICAL & COUVERTURE</h6>
                <p class="mb-1"><strong>Code Dossier :</strong> {{ $dossier->code_dossier ?? 'N/A' }}</p>
                <p class="mb-1"><strong>Groupe Sanguin :</strong> <span class="badge bg-danger">{{ $dossier->groupe_sanguin ?: 'Non spécifié' }}</span></p>
                <p class="mb-1"><strong>Couverture :</strong> {{ optional($patient->assurance)->nom ?? 'Privé' }} ({{ $patient->taux_couverture ?? 0 }}%)</p>
                <p class="mb-0"><strong>Allergies Connues :</strong> {{ $dossier->allergies ?: 'Aucune' }}</p>
            </div>
        </div>
    </div>

    {{-- 1. Motif --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-2" style="font-size: 12px;">1. MOTIF DE LA CONSULTATION</h6>
    <div class="card mb-3 border-primary shadow-sm">
        <div class="card-body bg-light py-2">
            {{ $consultationActuelle->motif ?? 'Aucun motif spécifique.' }}
        </div>
    </div>

    {{-- 2. Histoire de la maladie --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-2" style="font-size: 12px;">2. HISTOIRE DE LA MALADIE</h6>
    <div class="card mb-3 border-primary shadow-sm">
        <div class="card-body bg-light py-2">
            {{ $consultationActuelle->historique_maladie ?? 'RAS.' }}
        </div>
    </div>

    {{-- 3. Antécédents --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-2" style="font-size: 12px;">3. ANTÉCÉDENTS</h6>
    <div class="card mb-3 border-primary shadow-sm">
        <div class="card-body bg-light py-2">
            {{ $consultationActuelle->antecedents_maladie ?? 'Aucun antécédent médical spécifique consigné.' }}
        </div>
    </div>

    {{-- 4. Examen général --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-2" style="font-size: 12px;">4. EXAMEN GÉNÉRAL</h6>
    <div class="card mb-3 border-primary shadow-sm">
        <div class="card-body bg-light py-2">
            {{ $consultationActuelle->examen_general ?? 'Aucun examen général effectué.' }}
        </div>
    </div>

    {{-- 5. Examen physique --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-2" style="font-size: 12px;">5. EXAMEN PHYSIQUE</h6>
    <div class="card mb-3 border-primary shadow-sm">
        <div class="card-body bg-light py-2">
            {{ $consultationActuelle->examen_physique ?? 'Aucun examen physique effectué.' }}
        </div>
    </div>

    {{-- 6. Diagnostic positif --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-2" style="font-size: 12px;">6. DIAGNOSTIC POSITIF</h6>
    <div class="card mb-3 border-primary shadow-sm">
        <div class="card-body bg-light py-2">
            {{ $consultationActuelle->diagnostic ?? 'Aucun diagnostic effectué.' }}
        </div>
    </div>

    {{-- 7. Complication --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-2" style="font-size: 12px;">7. COMPLICATION</h6>
    <div class="card mb-3 border-primary shadow-sm">
        <div class="card-body bg-light py-2">
            {{ $consultationActuelle->complications ?? 'Aucune complication.' }}
        </div>
    </div>

    {{-- 8. Traitement reçu --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-2" style="font-size: 12px;">8. TRAITEMENT REÇU</h6>
    <div class="card mb-3 border-primary shadow-sm">
        <div class="card-body bg-light py-2">
            @if($consultationActuelle?->produits && $consultationActuelle->produits->count() > 0)
                <ul class="list-group list-group-flush mt-1">
                    @foreach($consultationActuelle->produits as $produit)
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-1">
                        <div>
                            <span class="fw-bold">{{ $produit->nom ?? $produit->libelle }}</span>
                            <br><small class="text-muted">Posologie : {{ $produit->pivot->posologie ?? 'N/A' }} | Voie : {{ $produit->pivot->voie_administration ?? 'N/A' }}</small>
                        </div>
                        <span class="badge bg-primary">Qté : {{ $produit->pivot->quantite ?? 1 }}</span>
                    </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted mb-0">Aucun traitement consigné pour cette consultation.</p>
            @endif
        </div>
    </div>

    {{-- 9. Évolution de la maladie --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-2" style="font-size: 12px;">9. ÉVOLUTION DE LA MALADIE</h6>
    <div class="card mb-3 border-primary shadow-sm">
        <div class="card-body bg-light py-2">
            @if($consultationActuelle && !empty($consultationActuelle->evolution_maladie))
                <p class="mb-2"><strong>Bilan évolutif :</strong> {{ $consultationActuelle->evolution_maladie }}</p>
            @endif

            @php
                $visites = $consultationActuelle->visite_medicale_journaliere ?? [];
                $derniereVisites = collect($visites)->reverse()->take(5);
            @endphp

            @if($derniereVisites->count() > 0)
                <div class="border-start border-primary border-3 ps-3 mt-2">
                    @foreach($derniereVisites as $note)
                    <div class="mb-2 bg-white p-2 border rounded">
                        <div class="small text-muted mb-1">
                            <span>{{ $note['date_heure'] ?? '' }}</span>
                        </div>
                        <p class="mb-0 small text-dark">{{ $note['observation'] ?? '' }}</p>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted small fst-italic mb-0">Aucune note de visite journalière enregistrée.</p>
            @endif
        </div>
    </div>

    {{-- 10. Ordonnance de sortie --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-2" style="font-size: 12px;">10. ORDONNANCE DE SORTIE</h6>
    <div class="card mb-4 border-primary p-3 bg-white shadow-sm">
        @if(!empty($consultationActuelle->ordonnance))
            <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $consultationActuelle->ordonnance }}</p>
        @else
            <p class="text-muted mb-0 fst-italic">Aucune ordonnance de sortie renseignée.</p>
        @endif
    </div>

    {{-- Signatures --}}
    <div class="row mt-5 pt-3">
        <div class="col-6">
            <p class="mb-0">Date du prochain rendez-vous : ........................................</p>
        </div>
        <div class="col-6 text-center">
            <p class="mb-5 fw-bold">Le Médecin traitant</p>
            <p class="text-muted small mt-4">(Nom, Signature & Cachet de la Formation Sanitaire)</p>
        </div>
    </div>

</body>
</html>