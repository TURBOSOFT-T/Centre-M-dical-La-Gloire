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
            @page {
                size: auto;   
                margin: 5mm;  
            }

            body {
                padding: 0;
                margin: 0;
                width: 100%;
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact;
            }

            .no-print { 
                display: none !important; 
            }

            table, tr, td, th {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body onload="window.print()">

    @php
        // 1. Tarif Consultation de base
        $tarifConsultation = $consultation->tarif_brut ?? 5000;
        
        // 2. Tarif Examens
        $tarifExamens = 0;
        if ($consultation->relationLoaded('demandesExamens') && $consultation->demandesExamens) {
            $tarifExamens = $consultation->demandesExamens->sum('tarif_brut');
        }
        
        // 3. Produits prescrits directement (via relation pivot)
        $tarifProduitsPrescrits = 0;
        $produitsPrescrits = $consultation->relationLoaded('produits') ? $consultation->produits : collect();
        if ($produitsPrescrits) {
            foreach ($produitsPrescrits as $prod) {
                $qte = $prod->pivot->quantite ?? 1;
                $pu = $prod->pivot->prix_unitaire ?? $prod->pivot->prix ?? $prod->prix ?? 0;
                $tarifProduitsPrescrits += ($qte * $pu);
            }
        }

        // 4. Produits issus d'une commande liée (ex: $consultation->commande ou $consultation->commandes)
        $tarifProduitsCommande = 0;
        $lignesCommande = collect();
        
        // On vérifie si une relation "commande" ou "commandes" existe sur la consultation
        $commande = null;
        if ($consultation->relationLoaded('commande')) {
            $commande = $consultation->commande;
        } elseif (method_exists($consultation, 'commande') && $consultation->commande) {
            $commande = $consultation->commande;
        }

        if ($commande && $commande->relationLoaded('lignes') && $commande->lignes) {
            $lignesCommande = $commande->lignes;
        } elseif ($commande && method_exists($commande, 'lignes') && $commande->lignes) {
            $lignesCommande = $commande->lignes;
        }

        foreach ($lignesCommande as $ligne) {
            $qteCmd = $ligne->quantite ?? 1;
            $puCmd = $ligne->prix_unitaire ?? $ligne->prix ?? 0;
            $tarifProduitsCommande += ($qteCmd * $puCmd);
        }

        // Total Brut Global
        $totalBrut = $tarifConsultation + $tarifExamens + $tarifProduitsPrescrits + $tarifProduitsCommande;
        $totalFacture = $totalBrut;

        // Gestion Assurance
        if ($consultation->patient && $consultation->patient->est_assure && $consultation->patient->assurance) {
            $taux = (float) $consultation->patient->taux_couverture;
            $partAssurance = round(($totalBrut * $taux) / 100);
            $totalFacture = $totalBrut - $partAssurance;
        }

        $montantPaye = $consultation->montant_paye ?? 0;
        $resteAPayer = max(0, $totalFacture - $montantPaye);
    @endphp

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
            <td class="text-end"><strong>Paiement :</strong> 
                <span style="text-transform: uppercase;">
                    @if($consultation->est_paye || $resteAPayer == 0) Solde (100%) @elseif($montantPaye > 0) Partiel @else Impayé @endif
                </span>
            </td>
        </tr>
        @if($consultation->patient && $consultation->patient->est_assure && $consultation->patient->assurance)
        <tr>
            <td colspan="2"><strong>Assurance :</strong> {{ $consultation->patient->assurance->code }} (Couverture : {{ $consultation->patient->taux_couverture }}%)</td>
        </tr>
        @endif
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Prestation / Désignation</th>
                <th class="text-end">Montant (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            {{-- 1. Consultation --}}
            <tr>
                <td>Consultation ({{ ucfirst(str_replace('_', ' ', $consultation->type ?? 'standard')) }})</td>
                <td class="text-end">{{ number_format($tarifConsultation, 0, ',', ' ') }}</td>
            </tr>

            {{-- 2. Examens --}}
            @if($consultation->relationLoaded('demandesExamens') && $consultation->demandesExamens)
                @foreach($consultation->demandesExamens as $demande)
                <tr>
                    <td>Examen : {{ $demande->examen->nom ?? 'Examen médical' }}</td>
                    <td class="text-end">{{ number_format($demande->tarif_brut ?? 0, 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            @endif

            {{-- 3. Produits prescrits directement --}}
            @if($produitsPrescrits && $produitsPrescrits->count() > 0)
                @foreach($produitsPrescrits as $prod)
                @php
                    $qte = $prod->pivot->quantite ?? 1;
                    $pu =  $prod->pivot->prix ?? $prod->prix ?? 0;
                    $sousTotalProd = $qte * $pu;
                @endphp
                <tr>
                    <td>Médicament / Produit : {{ $prod->nom ?? 'Produit' }} (Qte: {{ $qte }})</td>
                    <td class="text-end">{{ number_format($sousTotalProd, 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            @endif

            {{-- 4. Produits issus d'une Commande liée --}}
            @if($lignesCommande && $lignesCommande->count() > 0)
                @foreach($lignesCommande as $ligne)
                @php
                    $qteCmd = $ligne->quantite ?? 1;
                    $puCmd = $ligne->prix_unitaire ?? $ligne->prix ?? 0;
                    $sousTotalCmd = $qteCmd * $puCmd;
                    $nomProduit = $ligne->produit->nom ?? $ligne->nom ?? 'Article de commande';
                @endphp
                <tr>
                    <td>Commande Produit : {{ $nomProduit }} (Qte: {{ $qteCmd }})</td>
                    <td class="text-end">{{ number_format($sousTotalCmd, 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
        <tfoot>
            {{-- Si une assurance s'applique --}}
            @if($consultation->patient && $consultation->patient->est_assure && $consultation->patient->assurance)
                <tr>
                    <td><strong>Total Brut Prestations</strong></td>
                    <td class="text-end"><strong>{{ number_format($totalBrut, 0, ',', ' ') }}</strong></td>
                </tr>
                <tr>
                    <td>Part Prise en charge Assurance ({{ $consultation->patient->taux_couverture }}%)</td>
                    <td class="text-end" style="color: #0d6efd;">- {{ number_format(($totalBrut * $consultation->patient->taux_couverture) / 100, 0, ',', ' ') }}</td>
                </tr>
            @endif

            <tr>
                <td><strong>Total Net Facture</strong></td>
                <td class="text-end"><strong>{{ number_format($totalFacture, 0, ',', ' ') }}</strong></td>
            </tr>
            <tr>
                <td>Montant Réglé (Caisse)</td>
                <td class="text-end" style="color: green;">{{ number_format($montantPaye, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td><strong>Reste à Payer</strong></td>
                <td class="text-end" style="color: red;"><strong>{{ number_format($resteAPayer, 0, ',', ' ') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Merci pour votre confiance. Bon rétablissement!</p>
        <p><strong>Centre Médical La Gloire</strong></p>
    </div>

</body>
</html>