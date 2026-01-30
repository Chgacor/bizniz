<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        // Group settings by their 'group' column (e.g., 'general', 'tax') for the UI
        $settings = Setting::all()->groupBy('group');
        return view('settings.index', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        // Get all input except the CSRF token
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            // Update where key matches (e.g., 'tax_rate')
            Setting::where('key', $key)->update(['value' => $value]);
        }

        return back()->with('success', 'System configuration updated successfully.');
    }

    /**
     * Generate and download a Database Backup (.sql).
     */
    public function downloadBackup()
    {
        $filename = "backup-bizniz-" . date('Y-m-d-His') . ".sql";

        $headers = [
            "Content-type" => "text/plain",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Expires" => "0"
        ];

        $callback = function() {
            $handle = fopen('php://output', 'w');

            // 1. Get List of Tables
            $tables = DB::select('SHOW TABLES');
            $dbName = env('DB_DATABASE', 'laravel');
            $colName = "Tables_in_" . $dbName;

            foreach ($tables as $table) {
                // Handle different DB drivers/fetch modes safely
                $tableName = $table->$colName ?? array_values((array)$table)[0];

                // 2. Drop Table Statement
                fwrite($handle, "\n\nDROP TABLE IF EXISTS `$tableName`;\n\n");

                // 3. Create Table Structure
                $createTable = DB::select("SHOW CREATE TABLE `$tableName`");
                $createSql = $createTable[0]->{'Create Table'} ?? array_values((array)$createTable[0])[1];
                fwrite($handle, $createSql . ";\n\n");

                // 4. Insert Data
                // Use cursor to prevent memory overflow on large tables
                foreach (DB::table($tableName)->cursor() as $row) {
                    $values = array_map(function ($value) {
                        if (is_null($value)) return "NULL";
                        // Escape single quotes for SQL safety
                        return "'" . str_replace("'", "\'", $value) . "'";
                    }, (array)$row);

                    $sql = "INSERT INTO `$tableName` VALUES (" . implode(", ", $values) . ");\n";
                    fwrite($handle, $sql);
                }
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
