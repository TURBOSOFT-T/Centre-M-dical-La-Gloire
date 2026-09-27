<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bilan Médical - {{ $patient->nom_complet }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 13px; color: #212529; }
        .bg-header { background-color: #0d6efd; color: white; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
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
        <div class="col-8">
            <h3 class="fw-bold text-primary mb-1">CENTRE MÉDICAL LA GLOIRE</h3>
            <p class="mb-0 text-muted small">Médecine Générale - Spécialités - Soins & Chirurgie</p>
            <p class="mb-0 text-muted small">Douala, Cameroun - Tél : +237 600 00 00 00</p>
        </div>
        <div class="col-4 text-end">
            <h5 class="fw-bold text-dark mb-0">RAPPORT & BILAN MÉDICAL</h5>
            <small class="text-muted">Édité le {{ date('d/m/Y à H:i') }}</small>
        </div>
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
                <p class="mb-1"><strong>Couverture :</strong> {{ $patient->assurance->nom ?? 'Privé' }} ({{ $patient->taux_couverture }}%)</p>
                <p class="mb-0"><strong>Allergies Connues :</strong> {{ $dossier->allergies ?: 'Aucune' }}</p>
            </div>
        </div>
    </div>

    {{-- Synthèse des Consultations & Aspects Positifs --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">1. ÉVALUATION ET ASPECTS POSITIFS CONSTATÉS</h6>
    <div class="card mb-4 border-0 bg-light p-3">
        <ul class="mb-0">
            <li><strong>Évolution clinique :</strong> Statut général du patient jugé favorable au terme des consultations enregistrées.</li>
            <li><strong>Antécédents traités / maîtrisés :</strong> {{ $dossier->antecedents_medicaux ?: 'Aucun antécédent lourd relevé.' }}</li>
            <li><strong>Résultats des examens biologiques :</strong> Analyses de contrôle effectuées et archivées au laboratoire.</li>
            <li><strong>Traitements suivis :</strong> Réponse satisfaisante aux prescriptions thérapeutiques administrées.</li>
        </ul>
    </div>

    {{-- Historique Synthétique des Consultations --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">2. HISTORIQUE DES CONSULTATIONS & DIAGNOSTICS</h6>
    <table class="table table-bordered mb-4 align-middle">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>Médecin Praticien</th>
                <th>Motif</th>
                <th>Diagnostic Pose</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dossier->consultations as $c)
            <tr>
                <td>{{ \Carbon\Carbon::parse($c->date_consultation ?? $c->created_at)->format('d/m/Y') }}</td>
                <td>Dr. {{ $c->medecin->nom ?? $c->medecin->name ?? 'Praticien' }}</td>
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

    {{-- Perspectives & Recommandations Thérapeutiques --}}
    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">3. PERSPECTIVES & ORIENTATIONS DE SUIVI</h6>
    <div class="card mb-4 border-primary p-3 bg-white">
        <p class="mb-2"><strong>Recommandations médicales pour le patient :</strong></p>
        <ul class="mb-0">
            <li>Poursuite rigoureuse des traitements médicamenteux en cours selon l'ordonnance remise.</li>
            <li>Observance des règles d'hygiène de vie et du régime alimentaire adapté.</li>
            <li>Planification d'une consultation de contrôle et de suivis biologiques de routine dans les 3 à 6 prochains mois.</li>
            <li>Reconsultation immédiate en cas d'apparition de nouveaux symptômes d'alerte.</li>
        </ul>
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