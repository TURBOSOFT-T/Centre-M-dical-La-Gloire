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
            $file =$request->file('sql_file');
            $sqlContent = File::get($file->getRealPath());

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::unprepared($sqlContent);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->back()->with('success', 'La table des examens a été importée et restaurée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'importation du fichier SQL : ' . $e->getMessage());
        }
    }


    public function exportDatabase()
    {
        $databaseName = DB::getDatabaseName();$tables = DB::select('SHOW TABLES');
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

        return response()->streamDownload(function () use ($sql) {
            echo $sql;
        }, $fileName, [
            'Content-Type' => 'text/x-sql',
        ]);
    }

    /**
     * NOUVELLE MÉTHODE : Exporte depuis MySQL mais nettoie et formate le SQL
     * pour qu'il soit directement importable du côté d'une base SQLite.
     */
    public function exportMysqlForSqlite()
    {
        $databaseName = DB::getDatabaseName();$tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' .$databaseName;

        $sql = "-- EXPORT MYSQL COMPATIBLE SQLITE - Centre Médical La Gloire\n";
        $sql .= "-- Date : " . date('Y-m-d H:i:s') . "\n\n";

        foreach ($tables as $tableObj) {$tableName = $tableObj->$tableKey;
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            
            $createStmt = DB::select("SHOW CREATE TABLE `{$tableName}`");
            if (!empty($createStmt)) {
                $createKey = 'Create Table';$createStatement = $createStmt[0]->$createKey;

                // Nettoyage et conversion pour SQLite à la volée
                $createStatement = preg_replace('/`id`\s+(?:bigint\s+unsigned|INTEGER|BIGINT[^\n,]*)(?:\s+NOT\s+NULL)?(?:\s+AUTO_INCREMENT)?/i', '`id` INTEGER PRIMARY KEY AUTOINCREMENT', $createStatement);$createStatement = str_ireplace('AUTO_INCREMENT', '', $createStatement);$createStatement = preg_replace('/,\s*PRIMARY\s+KEY\s*\(`id`\)/i', '', $createStatement);$createStatement = preg_replace('/bigint\s+unsigned/i', 'INTEGER', $createStatement);$createStatement = preg_replace('/int\s+unsigned/i', 'INTEGER', $createStatement);$createStatement = str_ireplace('LONGTEXT', 'TEXT', $createStatement);$createStatement = str_ireplace('DATETIME', 'TEXT', $createStatement);$createStatement = str_ireplace('TIMESTAMP', 'TEXT', $createStatement);$createStatement = preg_replace('/UNIQUE\s+KEY\s+`[^`]+`\s*\(/i', 'UNIQUE (', $createStatement);
                $createStatement = preg_replace('/KEY\s+`[^`]+`\s*\([^)]+\),?/i', '', $createStatement);$createStatement = preg_replace('/CHARACTER\s+SET\s+[^\s,)]+/i', '', $createStatement);$createStatement = preg_replace('/COLLATE\s+[^\s,)]+/i', '', $createStatement);$createStatement = preg_replace('/\)\s*ENGINE\s*=\s*[a-zA-Z0-9_]+\s*(DEFAULT\s+CHARSET\s*=\s*[^\s;]+)?\s*;/i', ');', $createStatement);

                $sql .=$createStatement . "\n\n";
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

        $fileName = 'backup_mysql_for_sqlite_' . date('Y_m_d_His') . '.sql';

        return response()->streamDownload(function () use ($sql) {
            echo $sql;
        }, $fileName, [
            'Content-Type' => 'text/x-sql',
        ]);
    }

    /**
     * Importe un fichier SQL complet (Qu'il soit brut ou issu de MySQL, injecté dans SQLite)
     */
    public function importDatabase(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|mimes:sql,txt,sqlite,db|max:20480', // Limite à 20 Mo
        ]);

        try {
            $file =$request->file('sql_file');
            $filePath =$file->getRealPath();
            $extension = strtolower($file->getClientOriginalExtension());
            $content = File::get($filePath);

            $driver = DB::connection()->getDriverName();

            // Si le driver actif est SQLite et qu'on importe un fichier binaire SQLite brut
            if ($driver === 'sqlite' && in_array($extension, ['sqlite', 'db'])) {$dbName = basename(config('database.connections.sqlite.database', 'database.sqlite'));
                $dbPath = database_path($dbName);
                
                DB::purge('sqlite');
                if (!copy($filePath,$dbPath)) {
                    throw new \Exception("Impossible de copier le fichier SQLite.");
                }
                return redirect()->back()->with('success', 'Base de données SQLite restaurée avec succès.');
            }

            // Si c'est un script SQL (standard ou MySQL)
            $sqlContent = File::get($filePath);

            if ($driver === 'sqlite') {
                // Nettoyage automatique des commentaires et des syntaxes spécifiques MySQL pour SQLite
                $sqlContent = preg_replace('/^--.*$/m', '', $sqlContent);
                $sqlContent = preg_replace('/\/\*.*?\*\//s', '',$sqlContent);
                $sqlContent = preg_replace('/^SET\s+.*;/mi', '',$sqlContent);
                
                // Conversions de sécurité si le dump n'a pas été généré par exportMysqlForSqlite
                $sqlContent = preg_replace('/bigint\s+unsigned/i', 'INTEGER', $sqlContent);$sqlContent = preg_replace('/int\s+unsigned/i', 'INTEGER', $sqlContent);$sqlContent = preg_replace('/`id`\s+(?:bigint\s+unsigned|INTEGER|BIGINT[^\n,]*)(?:\s+NOT\s+NULL)?(?:\s+AUTO_INCREMENT)?/i', '`id` INTEGER PRIMARY KEY AUTOINCREMENT', $sqlContent);$sqlContent = str_ireplace('AUTO_INCREMENT', '', $sqlContent);$sqlContent = preg_replace('/,\s*PRIMARY\s+KEY\s*\(`id`\)/i', '', $sqlContent);$sqlContent = preg_replace('/UNIQUE\s+KEY\s+`[^`]+`\s*\(/i', 'UNIQUE (', $sqlContent);
                $sqlContent = preg_replace('/KEY\s+`[^`]+`\s*\([^)]+\),?/i', '', $sqlContent);$sqlContent = preg_replace('/CHARACTER\s+SET\s+[^\s,)]+/i', '', $sqlContent);$sqlContent = preg_replace('/COLLATE\s+[^\s,)]+/i', '', $sqlContent);$sqlContent = preg_replace('/\)\s*ENGINE\s*=\s*[a-zA-Z0-9_]+\s*(DEFAULT\s+CHARSET\s*=\s*[^\s;]+)?\s*;/i', ');', $sqlContent);
                $sqlContent = str_ireplace('LONGTEXT', 'TEXT',$sqlContent);
                $sqlContent = str_ireplace('DATETIME', 'TEXT',$sqlContent);
                $sqlContent = str_ireplace('TIMESTAMP', 'TEXT',$sqlContent);
            }

            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }

            DB::unprepared($sqlContent);

            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

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
            
            $filePath = storage_path('app/' .$fileName);
            File::put($filePath,$sql);

            $destinataire = Auth::user()?->email ?? config('mail.from.address');

            Mail::send([], [], function ($message) use ($filePath, $fileName,$destinataire) {
                $message->to($destinataire)
                        ->subject('Sauvegarde automatique - Centre Médical La Gloire')
                        ->html("Bonjour,<br><br>Veuillez trouver ci-joint la sauvegarde complète de la base de données du <strong>Centre Médical La Gloire</strong> générée le " . date('d/m/Y à H:i') . ".")
                        ->attach($filePath, [
                            'as' => $fileName,
                            'mime' => 'text/x-sql',
                        ]);
            });

            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            return redirect()->back()->with('success', 'La sauvegarde a été envoyée avec succès par e-mail à : ' . $destinataire);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
        }
    }
}