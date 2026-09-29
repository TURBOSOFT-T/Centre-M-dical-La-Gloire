<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats d'Examen - {{ $demande->code_demande }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 13px; color: #333; }
        .header-title { text-transform: uppercase; font-weight: bold; letter-spacing: 1px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body class="p-4 bg-white" onload="window.print()">

    <div class="no-print mb-4 d-flex justify-content-between align-items-center">
        <a href="javascript:history.back()" class="btn btn-secondary btn-sm">&larr; Retour</a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">Imprimer le rapport</button>
    </div>

    {{-- En-tête du Centre Médical --}}
    <div class="row border-bottom pb-3 mb-4 align-items-center">
        <div class="col-8">
            <h3 class="fw-bold text-primary mb-1">CENTRE MÉDICAL LA GLOIRE</h3>
            <p class="mb-0 text-muted small">Laboratoire d'Analyses Médicales & Biologiques</p>
            <p class="mb-0 text-muted small">Douala, Cameroun - Tél : +237 600 00 00 00</p>
        </div>
        <div class="col-4 text-end">
            <h5 class="fw-bold text-dark mb-0">BULLETIN DE RÉSULTATS</h5>
            <small class="text-muted">N° {{ $demande->code_demande }}</small>
        </div>
    </div>

    {{-- Cartouche Patient & Examen --}}
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="border rounded p-3 bg-light">
                <h6 class="fw-bold text-primary mb-2 border-bottom pb-1">INFORMATIONS PATIENT</h6>
                <p class="mb-1"><strong>Nom & Prénom :</strong> {{ $demande->patient?->nom }} {{ $demande->patient?->prenom }}</p>
                <p class="mb-1"><strong>Code Patient :</strong> {{ $demande->patient?->code_patient }}</p>
                <p class="mb-0"><strong>Âge / Sexe :</strong> {{ $demande->patient?->age ? $demande->patient->age . ' ans' : '-' }} / {{ $demande->patient?->genre }}</p>
            </div>
        </div>
        <div class="col-6">
            <div class="border rounded p-3 bg-light">
                <h6 class="fw-bold text-primary mb-2 border-bottom pb-1">DÉTAILS DEMANDE</h6>
                <p class="mb-1"><strong>Examen :</strong> {{ $demande->examen?->nom }}</p>
                <p class="mb-1"><strong>Prescripteur :</strong> Dr. {{ $demande->prescripteur?->nom ?? $demande->prescripteur?->name ?? 'N/A' }}</p>
                <p class="mb-0"><strong>Date de réalisation :</strong> {{ $demande->date_realisation ? \Carbon\Carbon::parse($demande->date_realisation)->format('d/m/Y à H:i') : '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Tableau des Résultats --}}
    <h6 class="fw-bold text-dark mb-2">ANALYSES ET PARAMÈTRES</h6>
    <table class="table table-bordered mb-4">
        <thead class="table-light">
            <tr>
                <th>Paramètre / Sous-analyse</th>
                <th class="text-center" style="width: 40%;">Résultat Obtenu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($demande->analyses_demandees ?? [] as $item)
                <tr>
                    <td class="fw-semibold">{{ $item['nom'] ?? 'Paramètre' }}</td>
                    <td class="text-center fw-bold text-primary fs-6">{{ $item['resultat'] ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center text-muted">Aucun paramètre saisi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Conclusion & Signature --}}
    @if($demande->conclusion)
        <div class="p-3 bg-light border rounded mb-4">
            <strong class="text-primary d-block mb-1">Conclusion du Biologiste :</strong>
            <p class="mb-0">{{ $demande->conclusion }}</p>
        </div>
    @endif

    <div class="row mt-5 pt-3">
        <div class="col-6 offset-6 text-center">
            <p class="mb-5 fw-bold">Le Biologiste / Responsable du Laboratoire</p>
            <p class="text-muted small mt-4">(Signature et Cachet)</p>
        </div>
    </div>

</body>
</html>