<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
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


    public function exportDatabase()
    {
        $databaseName = DB::getDatabaseName();
        // Récupère la liste de toutes les tables de la base de données
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' .$databaseName;

        $sql = "-- ========================================================\n";
        $sql .= "-- EXPORTATION COMPLÈTE DE LA BASE DE DONNÉES : {$databaseName}\n";
        $sql .= "-- Date d'export : " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Centre Médical La Gloire\n";
        $sql .= "-- ========================================================\n\n";

        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableObj) {$tableName = $tableObj->$tableKey;

            $sql .= "-- --------------------------------------------------------\n";
            $sql .= "-- Structure et données de la table : `{$tableName}`\n";
            $sql .= "-- --------------------------------------------------------\n\n";

            // 1. DROP et CREATE TABLE
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            
            $createStmt = DB::select("SHOW CREATE TABLE `{$tableName}`");
            if (!empty($createStmt)) {
                $createKey = 'Create Table';$sql .= $createStmt[0]->$createKey . ";\n\n";
            }

            // 2. Récupération des données
            $rows = DB::table($tableName)->get();

            if ($rows->isNotEmpty()) {$columns = array_keys((array) $rows->first());$sql .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES\n";

                $recordsList = [];
                foreach ($rows as$row) {
                    $values = array_map(function ($value) {
                        if (is_null($value)) {
                            return 'NULL';
                        }
                        if (is_numeric($value)) {
                            return $value;
                        }
                       
                        return "'" . addslashes($value) . "'";
                    }, (array) $row);

                    $recordsList[] = "(" . implode(', ', $values) . ")";
                }

                $sql .= implode(",\n", $recordsList) . ";\n\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $fileName = 'backup_full_database_' . date('Y_m_d_His') . '.sql';

        return response()->streamDownload(function () use ($sql) {
            echo $sql;
        }, $fileName, [
            'Content-Type' => 'text/x-sql',
        ]);
    }

    /**
     * Importe un fichier SQL complet (Restauration de toute la base)
     */
    public function importDatabase(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|mimes:sql,txt|max:20480', // Limite à 20 Mo pour une base entière
        ]);

        try {
            $file =$request->file('sql_file');
            $sqlContent = File::get($file->getRealPath());

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::unprepared($sqlContent);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->back()->with('success', 'La base de données a été restaurée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la restauration : ' . $e->getMessage());
        }
    }



    public function exportAndEmailDatabase(Request $request)
    {
        try {
            $databaseName = DB::getDatabaseName();$tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' .$databaseName;

            $sql = "-- ========================================================\n";
            $sql .= "-- EXPORTATION COMPLÈTE DE LA BASE DE DONNÉES : {$databaseName}\n";
            $sql .= "-- Date d'export : " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Centre Médical La Gloire\n";
            $sql .= "-- ========================================================\n\n";

            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $tableObj) {$tableName = $tableObj->$tableKey;

                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                
                $createStmt = DB::select("SHOW CREATE TABLE `{$tableName}`");
                if (!empty($createStmt)) {
                    $createKey = 'Create Table';$sql .= $createStmt[0]->$createKey . ";\n\n";
                }

                $rows = DB::table($tableName)->get();

                if ($rows->isNotEmpty()) {$columns = array_keys((array) $rows->first());$sql .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES\n";

                    $recordsList = [];
                    foreach ($rows as$row) {
                        $values = array_map(function ($value) {
                            if (is_null($value)) return 'NULL';
                            if (is_numeric($value)) return$value;
                            
                            return "'" . addslashes($value) . "'";
                        }, (array) $row);

                        $recordsList[] = "(" . implode(', ', $values) . ")";
                    }

                    $sql .= implode(",\n", $recordsList) . ";\n\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            $fileName = 'backup_full_database_' . date('Y_m_d_His') . '.sql';
            
            // Stockage temporaire sur le disque pour pouvoir l'attacher à l'e-mail
            $filePath = storage_path('app/' .$fileName);
            File::put($filePath,$sql);

            // Détermination du destinataire (Admin connecté ou email de configuration)
            $destinataire = Auth::user()?->email ?? config('mail.from.address');

            // Envoi de l'e-mail avec la pièce jointe
            Mail::send([], [], function ($message) use ($filePath, $fileName,$destinataire) {
                $message->to($destinataire)
                        ->subject('Sauvegarde automatique - Centre Médical La Gloire')
                        ->html("Bonjour,<br><br>Veuillez trouver ci-joint la sauvegarde complète de la base de données du <strong>Centre Médical La Gloire</strong> générée le " . date('d/m/Y à H:i') . ".")
                        ->attach($filePath, [
                            'as' => $fileName,
                            'mime' => 'text/x-sql',
                        ]);
            });

            // Suppression du fichier temporaire du serveur après l'envoi
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            return redirect()->back()->with('success', 'La sauvegarde a été envoyée avec succès par e-mail à : ' . $destinataire);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
        }
    }
}