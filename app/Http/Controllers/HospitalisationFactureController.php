<?php
namespace App\Http\Controllers;

use App\Models\Hospitalisation;
use Illuminate\Support\Facades\DB;

class HospitalisationFactureController extends Controller
{
    public function print($id)
    {
        $hospitalisation = Hospitalisation::with(['patient', 'dossierMedical', 'medecin', 'agent'])->findOrFail($id);
        $config = DB::table('configs')->select('icon', 'logo', 'telephone', 'email', 'addresse')->first();

        // Gestion du logo en base64 pour l'impression thermique
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

        return view('pdf.facture-print', compact('hospitalisation', 'config', 'logoBase64'));
    }
}