@php
use App\Helpers\TranslationHelper;
$devise = 'FCFA';
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture - {{ $hospitalisation->code_hospitalisation }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 72mm;
            margin: 0 auto;
            padding: 3mm;
            color: #000;
            background: #fff;
            font-size: 13px;
            line-height: 1.25;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .bold { font-weight: bold; }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        .logo {
            max-width: 55px;
            height: auto;
            margin-bottom: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }
        th, td {
            padding: 3px 2px;
            font-size: 12px;
            text-align: left;
            border-bottom: 1px dotted #ccc;
        }
        th {
            border-bottom: 1px solid #000;
        }
        .total-row td {
            border-top: 1px solid #000;
            border-bottom: 2px solid #000;
            font-weight: bold;
            font-size: 13px;
        }
        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        @media print {
            body { width: 72mm; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>

    <div class="header">
        @if(!empty($logoBase64))
        <img src="{{ $logoBase64 }}" alt="logo" class="logo"><br>
        @endif
        <span class="bold" style="font-size: 14px;">{{ config('app.name') }}</span><br>
        @if($config && !empty($config->telephone)) Tél : {{ $config->telephone }} <br> @endif
        @if($config && !empty($config->addresse)) {{ $config->addresse }} <br> @endif
    </div>

    <div>
        <strong>Facture N° :</strong> {{ $hospitalisation->code_hospitalisation }}<br>
        <strong>Date :</strong> {{ now()->format('d/m/Y H:i') }}<br>
        <strong>Patient :</strong> {{ $hospitalisation->patient->nom ?? '' }} {{ $hospitalisation->patient->prenom ?? '' }}<br>
        <strong>Type :</strong> {{ strtoupper($hospitalisation->type_prise_en_charge) }}
        @if($hospitalisation->type_prise_en_charge === 'hospitalisation')
            <br><strong>Chambre :</strong> {{ $hospitalisation->chambre_number }} ({{ ucfirst(str_replace('_', ' ', $hospitalisation->standing_type)) }})
        @endif
    </div>

    <hr>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th class="text-center">Qté/Jours</th>
                <th class="text-right">P.U</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @if($hospitalisation->type_prise_en_charge === 'observation')
                <tr>
                    <td>Forfait Observation</td>
                    <td class="text-center">1</td>
                    <td class="text-right">{{ number_format($hospitalisation->tarif_journalier, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($hospitalisation->tarif_journalier, 0, ',', ' ') }}</td>
                </tr>
            @else
                <tr>
                    <td>Séjour ({{ ucfirst(str_replace('_', ' ', $hospitalisation->standing_type)) }})</td>
                    <td class="text-center">{{ $hospitalisation->nombre_jours }} j</td>
                    <td class="text-right">{{ number_format($hospitalisation->tarif_journalier, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($hospitalisation->nombre_jours * $hospitalisation->tarif_journalier, 0, ',', ' ') }}</td>
                </tr>
            @endif

            @if($hospitalisation->frais_soins_chambre > 0)
                <tr>
                    <td>Soins / Prestations</td>
                    <td class="text-center">1</td>
                    <td class="text-right">{{ number_format($hospitalisation->frais_soins_chambre, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($hospitalisation->frais_soins_chambre, 0, ',', ' ') }}</td>
                </tr>
            @endif

            <tr class="total-row">
                <td colspan="3" class="text-right">TOTAL :</td>
                <td class="text-right">{{ number_format($hospitalisation->montant_total, 0, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="font-size: 12px; margin-top: 6px;">
        <strong>Part Assurance :</strong> -{{ number_format($hospitalisation->part_assurance, 0, ',', ' ') }} {{ $devise }}<br>
        <strong>Reste à charge Patient :</strong> {{ number_format($hospitalisation->part_patient, 0, ',', ' ') }} {{ $devise }}<br>
        <strong>Montant Versé (Payé) :</strong> {{ number_format($hospitalisation->montant_paye, 0, ',', ' ') }} {{ $devise }}<br>
        <strong>Reste à payer :</strong> <span class="bold">{{ number_format(max(0, $hospitalisation->part_patient - $hospitalisation->montant_paye), 0, ',', ' ') }} {{ $devise }}</span>
    </div>

    <hr>
    <div class="text-center" style="font-size: 11px;">
        {{ TranslationHelper::TranslateText('Merci de votre confiance !') }}<br>
        Agent : {{ $hospitalisation->agent->name ?? 'Système' }}
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>