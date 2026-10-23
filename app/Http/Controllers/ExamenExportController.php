<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class ExamenExportController extends Controller
{
    /**
     * Exporte uniquement la table 'examens' (SQLite)
     */
    public function export()
    {
        $tableName = 'examens';
        $rows = DB::table($tableName)->get();

        $sql = "-- Exportation de la table {$tableName} (SQLite)\n";
        $sql .= "-- Date : " . date('Y-m-d H:i:s') . "\n\n";

        $sql .= "DROP TABLE IF EXISTS \"{$tableName}\";\n";
        $sql .= $this->getTableCreateStatement($tableName) . ";\n\n";

        if ($rows->isNotEmpty()) {$columns = array_keys((array) $rows->first());$sql .= "INSERT INTO \"{$tableName}\" (\"" . implode('", "', $columns) . "\") VALUES\n";

            $records = [];
            foreach ($rows as$row) {
                $values = array_map(function ($value) {
                    if (is_null($value)) return 'NULL';
                    if (is_numeric($value)) return$value;
                    return "'" . str_replace("'", "''", $value) . "'";
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

    /**
     * Importe un fichier SQL ou une base SQLite brute
     */

    public function importDatabase(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|mimes:sql,txt,sqlite,db|max:102400', // Limite à 100 Mo
        ]);

        try {
            $file = $request->file('sql_file');
            $filePath = $file->getRealPath();
            $extension = strtolower($file->getClientOriginalExtension());

            // Récupération du driver de base de données actuel (mysql, mariadb ou sqlite)
            $driver = DB::getDriverName();

            // Si c'est un fichier binaire SQLite brut (.sqlite ou .db)
            if (in_array($extension, ['sqlite', 'db'])) {
                if ($driver !== 'sqlite') {
                    throw new \Exception("Impossible d'importer un fichier SQLite alors que la base de données active utilise le driver " . $driver);
                }

                $dbName = basename(config('database.connections.sqlite.database', 'database.sqlite'));
                $dbPath = database_path($dbName);
                
                DB::purge('sqlite');
                if (!copy($filePath, $dbPath)) {
                    throw new \Exception("Impossible de remplacer le fichier de la base de données SQLite.");
                }
                return redirect()->back()->with('success', 'Base de données SQLite restaurée avec succès.');
            }

            // Si c'est un script de requêtes SQL textuelles
            $sqlContent = File::get($filePath);

            // Nettoyage de base des commentaires
            $sqlContent = preg_replace('/^--.*$/m', '', $sqlContent);
            $sqlContent = preg_replace('/\/\*.*?\*\//s', '', $sqlContent);

            // Gestion dynamique des contraintes de clés étrangères selon le SGBD
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF;');
            } else {
                // MySQL / MariaDB
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }

            DB::unprepared($sqlContent);

            // Réactivation des contraintes
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            return redirect()->back()->with('success', 'Le script SQL a été importé avec succès.');
        } catch (\Exception $e) {
            // S'assure de réactiver les clés étrangères même en cas d'erreur selon le driver
            try {
                $driver = DB::getDriverName();
                if ($driver === 'sqlite') {
                    DB::statement('PRAGMA foreign_keys = ON;');
                } else {
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                }
            } catch (\Throwable $th) {
                // Ignore si la connexion est rompue
            }
            
            return redirect()->back()->with('error', 'Erreur lors de la restauration : ' . $e->getMessage());
        }
    }
    public function importDatabase2(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|mimes:sql,txt,sqlite,db|max:102400', // Limite à 100 Mo
        ]);

        try {
            $file = $request->file('sql_file');
            $filePath = $file->getRealPath();
            $extension = strtolower($file->getClientOriginalExtension());

            // Si c'est un fichier binaire SQLite brut (.sqlite ou .db)
            if (in_array($extension, ['sqlite', 'db'])) {
                $dbName = basename(config('database.connections.sqlite.database', 'database.sqlite'));
                $dbPath = database_path($dbName);
                
                DB::purge('sqlite');
                if (!copy($filePath, $dbPath)) {
                    throw new \Exception("Impossible de remplacer le fichier de la base de données SQLite.");
                }
                return redirect()->back()->with('success', 'Base de données SQLite restaurée avec succès.');
            }

            // Si c'est un script de requêtes SQL textuelles
            $sqlContent = File::get($filePath);

            // Nettoyage de base pour SQLite
            $sqlContent = preg_replace('/^--.*$/m', '', $sqlContent);
            $sqlContent = preg_replace('/\/\*.*?\*\//s', '', $sqlContent);

            // Désactive les contraintes de clés étrangères pour SQLite, exécute le SQL, puis les réactive
            DB::statement('PRAGMA foreign_keys = OFF;');
            DB::unprepared($sqlContent);
            DB::statement('PRAGMA foreign_keys = ON;');

            return redirect()->back()->with('success', 'Le script SQL a été importé avec succès dans SQLite.');
        } catch (\Exception $e) {
            // S'assure de réactiver les clés étrangères même en cas d'erreur
            DB::statement('PRAGMA foreign_keys = ON;');
            
            return redirect()->back()->with('error', 'Erreur lors de la restauration : ' . $e->getMessage());
        }
    }

    /**
     * Alias de importDatabase pour correspondre aux routes pointant vers `import`
     */
    public function import(Request $request)
    {
        return $this->importDatabase($request);
    }

    /**
     * Exporte toute la base de données (SQLite)
     */
    public function exportDatabase()
    {
        $databaseName = DB::getDatabaseName();
        $tables =$this->getAllTableNames();

        $sql = "-- ========================================================\n";
        $sql .= "-- EXPORTATION COMPLÈTE DE LA BASE DE DONNÉES (SQLite)\n";
        $sql .= "-- Date d'export : " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Centre Médical La Gloire\n";
        $sql .= "-- ========================================================\n\n";

        foreach ($tables as $tableName) {$sql .= "-- --------------------------------------------------------\n";
            $sql .= "-- Structure et données de la table : \"{$tableName}\"\n";
            $sql .= "-- --------------------------------------------------------\n\n";

            $sql .= "DROP TABLE IF EXISTS \"{$tableName}\";\n";
            $sql .= $this->getTableCreateStatement($tableName) . ";\n\n";

            $rows = DB::table($tableName)->get();

            if ($rows->isNotEmpty()) {$columns = array_keys((array) $rows->first());$sql .= "INSERT INTO \"{$tableName}\" (\"" . implode('", "', $columns) . "\") VALUES\n";

                $recordsList = [];
                foreach ($rows as$row) {
                    $values = array_map(function ($value) {
                        if (is_null($value)) return 'NULL';
                        if (is_numeric($value)) return$value;
                        return "'" . str_replace("'", "''", $value) . "'";
                    }, (array) $row);

                    $recordsList[] = "(" . implode(', ', $values) . ")";
                }

                $sql .= implode(",\n", $recordsList) . ";\n\n";
            }
        }

        $fileName = 'backup_full_database_' . date('Y_m_d_His') . '.sql';

        return response()->streamDownload(function () use ($sql) {
            echo $sql;
        }, $fileName, [
            'Content-Type' => 'text/x-sql',
        ]);
    }

    /**
     * Exporte toute la base de données au format SQLite (méthode de compatibilité)
     */
    public function exportMysqlForSqlite()
    {
        return $this->exportDatabase();
    }

    /**
     * Exporte et envoie la base complète par e-mail (SQLite)
     */
    public function exportAndEmailDatabase(Request $request)
    {
        try {
            $databaseName = DB::getDatabaseName();
            $tables =$this->getAllTableNames();

            $sql = "-- ========================================================\n";
            $sql .= "-- EXPORTATION COMPLÈTE DE LA BASE DE DONNÉES (SQLite)\n";
            $sql .= "-- Date d'export : " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Centre Médical La Gloire\n";
            $sql .= "-- ========================================================\n\n";

            foreach ($tables as $tableName) {$sql .= "DROP TABLE IF EXISTS \"{$tableName}\";\n";
                $sql .= $this->getTableCreateStatement($tableName) . ";\n\n";

                $rows = DB::table($tableName)->get();

                if ($rows->isNotEmpty()) {$columns = array_keys((array) $rows->first());$sql .= "INSERT INTO \"{$tableName}\" (\"" . implode('", "', $columns) . "\") VALUES\n";

                    $recordsList = [];
                    foreach ($rows as$row) {
                        $values = array_map(function ($value) {
                            if (is_null($value)) return 'NULL';
                            if (is_numeric($value)) return$value;
                            return "'" . str_replace("'", "''", $value) . "'";
                        }, (array) $row);

                        $recordsList[] = "(" . implode(', ', $values) . ")";
                    }

                    $sql .= implode(",\n", $recordsList) . ";\n\n";
                }
            }

            $fileName = 'backup_full_database_' . date('Y_m_d_His') . '.sql';
            $filePath = storage_path('app/' .$fileName);
            File::put($filePath,$sql);

            $destinataire = Auth::user()?->email ?? config('mail.from.address');

            Mail::send([], [], function ($message) use ($filePath, $fileName,$destinataire) {
                $message->to($destinataire)
                        ->subject('Sauvegarde automatique - Centre Médical La Gloire')
                        ->html("Bonjour,<br><br>Veuillez trouver ci-joint la sauvegarde complète de la base de données SQLite du <strong>Centre Médical La Gloire</strong> générée le " . date('d/m/Y à H:i') . ".")
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

    /**
     * Récupère la liste de toutes les tables SQLite (hors system/migrations)
     */
    private function getAllTableNames()
    {
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name NOT LIKE 'migrations'");
        return array_map(fn($t) => $t->name,$tables);
    }

    /**
     * Récupère la commande CREATE TABLE stockée dans SQLite
     */
    private function getTableCreateStatement($tableName)
    {
        $row = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$tableName]);
        return $row ? $row->sql : '';
    }
}