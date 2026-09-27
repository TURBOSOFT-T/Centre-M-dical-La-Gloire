<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
class ExamenExportController extends Controller
{
    public function export()
    {
        $tableName = 'examens';
        $rows = DB::table($tableName)->get();

        $sql = "-- Exportation de la table {$tableName}\n";
        $sql .= "-- Date : " . date('Y-m-d H:i:s') . "\n\n";

        $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
        
        $createStmt = DB::select("SHOW CREATE TABLE `{$tableName}`");
        if (!empty($createStmt)) {
            $createKey = 'Create Table';$sql .= $createStmt[0]->$createKey . ";\n\n";
        }

        if ($rows->isNotEmpty()) {$columns = array_keys((array) $rows->first());$sql .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES\n";

            $records = [];
            foreach ($rows as$row) {
                $values = array_map(function ($value) {
                    if (is_null($value)) return 'NULL';
                    if (is_numeric($value)) return$value;
                    
                    return "'" . addslashes($value) . "'";
                }, (array) $row);

                $records[] = "(" . implode(', ', $values) . ")";
            }
            $sql .= implode(",\n", $records) . ";\n";
        }

        return response()->streamDownload(function () use ($sql) {
            echo $sql;
        }, 'export_examens.sql', [
            'Content-Type' => 'text/x-sql',
        ]);
    }


    public function import(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|mimes:sql,txt|max:5120', // Max 5 Mo
        ]);

        try {
            $file = $request->file('sql_file');
            $sqlContent = File::get($file->getRealPath());

            // Désactivation temporaire des contraintes de clés étrangères si nécessaire
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Exécution du script SQL brut
            // DB::unprepared exécute toutes les requêtes SQL contenues dans le fichier (DROP, CREATE, INSERT)
            DB::unprepared($sqlContent);

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->back()->with('success', 'La table des examens a été importée et restaurée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'importation du fichier SQL : ' . $e->getMessage());
        }
    }
}