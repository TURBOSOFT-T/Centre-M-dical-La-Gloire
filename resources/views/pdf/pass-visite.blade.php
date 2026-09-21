<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Pass Visiteur - {{ $visite->code_visite }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; margin: 10px; text-align: center; }
        .header { border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 10px; }
        .title { font-weight: bold; font-size: 14px; text-transform: uppercase; }
        .code { font-size: 16px; font-weight: bold; margin: 8px 0; background: #eee; padding: 4px; }
        .info { text-align: left; margin-bottom: 5px; }
        .footer { border-top: 1px dashed #000; margin-top: 10px; padding-top: 5px; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Centre Médical La Gloire</div>
        <small>PASS VISITEUR FAMILLE</small>
    </div>

    <div class="code">{{ $visite->code_visite }}</div>

    <div class="info">
        <p><strong>Patient :</strong> {{ $visite->patient->nom_complet }}</p>
        <p><strong>Chambre/Lit :</strong> {{ $visite->chambre_lit ?? 'Non spécifié' }}</p>
        <p><strong>Visiteur :</strong> {{ $visite->visiteur->nom_complet }}</p>
        <p><strong>Lien :</strong> {{ $visite->visiteur->lien_parente ?? 'Proche' }}</p>
        <p><strong>Badge N° :</strong> {{ $visite->badge_numero ?? '-' }}</p>
        <p><strong>Entrée :</strong> {{ $visite->date_heure_entree->format('d/m/Y H:i') }}</p>
    </div>

    <div class="footer">
        Prière de restituer le badge à la sortie.<br>
        <em>Bonne visite à votre proche !</em>
    </div>
</body>
</html>