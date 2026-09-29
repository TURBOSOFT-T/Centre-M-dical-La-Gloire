<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de Caisse - Consultation #{{ $consultation->code_consultation ?? $consultation->id }}</title>
    <style>
        /* --- Styles Généraux --- */
        body { 
            font-family: Arial, sans-serif; 
            font-size: 13px; 
            color: #333; 
            margin: 0; 
            padding: 10px; 
            background: #fff;
        }
        
        .header { 
            text-align: center; 
            border-bottom: 2px solid #0d6efd; 
            padding-bottom: 8px; 
            margin-bottom: 15px; 
        }
        .header h2 { font-size: 16px; margin: 0 0 5px 0; color: #0d6efd; }
        .header p { font-size: 12px; margin: 0; color: #555; }

        .details { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 12px; }
        .details td { padding: 4px 0; vertical-align: top; }

        .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 12px; }
        .table th, .table td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        .table th { background-color: #f8f9fa; }
        .text-end { text-align: right; }

        .footer { margin-top: 20px; text-align: center; font-size: 11px; color: #777; }

        /* --- Bouton d'impression (Masqué lors de l'impression réelle) --- */
        .no-print { margin-bottom: 15px; text-align: right; }
        .no-print button { 
            padding: 8px 15px; 
            background: #0d6efd; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 14px;
        }

        /* --- CONFIGURATION UNIVERSELLE POUR L'IMPRESSION --- */
        @media print {
            /* Supprime les marges superflues du navigateur pour maximiser la surface imprimable */
            @page {
                size: auto;   /* S'adapte au format par défaut de l'imprimante (A4, A5, ou rouleau continu) */
                margin: 5mm;  /* Petite marge de sécurité pour éviter les coupures sur les bords */
            }

            body {
                padding: 0;
                margin: 0;
                width: 100%;
                -webkit-print-color-adjust: exact; /* Force l'impression des couleurs de fond (table th, etc.) */
                print-color-adjust: exact;
            }

            .no-print { 
                display: none !important; 
            }

            /* Empêche les coupures disgracieuses au milieu des tableaux ou du texte */
            table, tr, td, th {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <button onclick="window.print()">🖨️ Imprimer le Reçu</button>
    </div>

    <div class="header">
        <h2>CENTRE MÉDICAL LA GLOIRE</h2>
        <p>Reçu de Caisse / Facture Médicale</p>
    </div>

    <table class="details">
        <tr>
            <td><strong>N° :</strong> {{ $consultation->code_consultation ?? 'CONS-' . $consultation->id }}</td>
            <td class="text-end"><strong>Date :</strong> {{ $consultation->created_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Patient :</strong> {{ $consultation->patient->nom_complet ?? (($consultation->patient->nom ?? 'N/A') . ' ' . ($consultation->patient->prenom ?? '')) }}</td>
        </tr>
        <tr>
            <td><strong>Médecin :</strong> {{ $consultation->medecin->nom ?? 'Non assigné' }}</td>
            <td class="text-end"><strong>Paiement :</strong> <span style="text-transform: uppercase;">{{ $consultation->statut_paiement ?? 'Non défini' }}</span></td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Prestation / Désignation</th>
                <th class="text-end">Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Consultation ({{ ucfirst(str_replace('_', ' ', $consultation->type)) }})</td>
                <td class="text-end">{{ number_format($consultation->tarif_brut, 0, ',', ' ') }}</td>
            </tr>
            @if($consultation->relationLoaded('demandesExamens') && $consultation->demandesExamens)
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
            $totalExamens = ($consultation->relationLoaded('demandesExamens') && $consultation->demandesExamens) ? $consultation->demandesExamens->sum('tarif_brut') : 0;
            $total = $consultation->tarif_brut + $totalExamens;
            $montantPaye = $consultation->montant_paye ?? 0;
            @endphp
            <tr>
                <td><strong>Total Général</strong></td>
                <td class="text-end"><strong>{{ number_format($total, 0, ',', ' ') }}</strong></td>
            </tr>
            <tr>
                <td>Montant Réglé</td>
                <td class="text-end" style="color: green;">{{ number_format($montantPaye, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td><strong>Reste à Payer</strong></td>
                <td class="text-end" style="color: red;"><strong>{{ number_format(max(0, $total - $montantPaye), 0, ',', ' ') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Merci pour votre confiance. Bon rétablissement !</p>
        <p><strong>Centre Médical La Gloire</strong></p>
    </div>

</body>
</html>