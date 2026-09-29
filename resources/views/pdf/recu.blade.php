<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de Caisse - Consultation #{{ $consultation->code_consultation }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #0d6efd; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #0d6efd; }
        .details, .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .details td { padding: 5px 0; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f8f9fa; }
        .text-end { text-align: right; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #777; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #0d6efd; color: white; border: none; border-radius: 5px; cursor: pointer;">Imprimer / PDF</button>
    </div>

    <div class="header">
        <h2>CENTRE MÉDICAL LA GLOIRE</h2>
        <p>Reçu de Caisse / Facture Médicale</p>
    </div>

    <table class="details">
        <tr>
            <td><strong>Code Consultation :</strong> {{ $consultation->code_consultation }}</td>
            <td><strong>Date :</strong> {{ $consultation->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Patient :</strong> {{ $consultation->patient->nom ?? 'N/A' }} {{ $consultation->patient->prenom ?? '' }}</td>
            <td><strong>Médecin :</strong> Dr. {{ $consultation->medecin->nom ?? 'Non assigné' }}</td>
        </tr>
        <tr>
            <td><strong>Statut Paiement :</strong> <span style="text-transform: uppercase; font-weight: bold;">{{ $consultation->statut_paiement }}</span></td>
            <td></td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Prestation / Désignation</th>
                <th class="text-end">Montant (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Consultation (Type : {{ $consultation->type }})</td>
                <td class="text-end">{{ number_format($consultation->tarif_brut, 0, ',', ' ') }}</td>
            </tr>
            @if($consultation->demandesExamens)
                @foreach($consultation->demandesExamens as $demande)
                <tr>
                    <td>Examen : {{ $demande->examen->nom ?? 'Examen médical' }}</td>
                    <td class="text-end">{{ number_format($demande->tarif_brut ?? 0, 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
        <tfoot>
            @php
            $total = $consultation->tarif_brut + ($consultation->demandesExamens ? $consultation->demandesExamens->sum('tarif_brut') : 0);
            @endphp
            <tr>
                <td><strong>Total Général</strong></td>
                <td class="text-end"><strong>{{ number_format($total, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
            <tr>
                <td>Montant Déjà Réglé</td>
                <td class="text-end" style="color: green;">{{ number_format($consultation->montant_paye, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td><strong>Reste à Payer</strong></td>
                <td class="text-end" style="color: red;"><strong>{{ number_format(max(0, $total - $consultation->montant_paye), 0, ',', ' ') }} FCFA</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Merci pour votre confiance. Bon rétablissement !</p>
        <p>Centre Médical La Gloire</p>
    </div>

</body>
</html>