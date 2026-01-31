<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        // AMBIL SEMUA SETTING & JADIKAN ARRAY
        // Hasilnya: ['business_name' => 'My Bizniz', 'tax_rate' => '11', ...]
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Ambil semua data input KECUALI token CSRF
        $data = $request->except('_token');

        // Loop otomatis untuk menyimpan semua input
        foreach ($data as $key => $value) {
            // Jika input kosong, simpan null. Jika ada isi, simpan isinya.
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Konfigurasi berhasil disimpan!');
    }

    // Fitur Backup Database (Sesuai request sebelumnya)
    public function downloadBackup()
    {
        $dbName = env('DB_DATABASE');
        $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
        $return = "";

        // 1. Loop semua tabel di database
        foreach ($tables as $table) {
            $tableName = $table->{'Tables_in_' . $dbName};

            // Ambil struktur tabel (CREATE TABLE ...)
            $createTable = \Illuminate\Support\Facades\DB::select("SHOW CREATE TABLE `$tableName`");
            $return .= "\n\n" . $createTable[0]->{'Create Table'} . ";\n\n";

            // Ambil isi data (INSERT INTO ...)
            $rows = \Illuminate\Support\Facades\DB::table($tableName)->get();

            foreach ($rows as $row) {
                $return .= "INSERT INTO `$tableName` VALUES(";
                $values = [];

                foreach ($row as $key => $val) {
                    if (is_null($val)) {
                        $values[] = "NULL";
                    } elseif (is_numeric($val)) {
                        $values[] = $val;
                    } else {
                        $values[] = "'" . addslashes($val) . "'";
                    }
                }

                $return .= implode(',', $values);
                $return .= ");\n";
            }
        }

        // 2. Simpan ke file
        $fileName = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        $path = storage_path("app/" . $fileName);

        // Pastikan folder ada
        if(!file_exists(storage_path("app"))) {
            mkdir(storage_path("app"), 0777, true);
        }

        file_put_contents($path, $return);

        // 3. Download dan Hapus file dari server setelah dikirim
        return response()->download($path)->deleteFileAfterSend(true);
    }
}
