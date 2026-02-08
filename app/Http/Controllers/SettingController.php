<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        // Ambil settings sebagai array [key => value]
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // 1. Handle Checkbox (Karena HTML tidak kirim value kalau unchecked)
        // Kita paksa set jadi 0 jika tidak ada di request
        $checkboxes = ['show_logo_on_receipt'];
        foreach ($checkboxes as $box) {
            if (!$request->has($box)) {
                $request->merge([$box => '0']);
            }
        }

        // 2. Ambil semua data kecuali token & file
        $data = $request->except(['_token', '_method', 'shop_logo']);

        // 3. Simpan Text Settings
        foreach ($data as $key => $value) {
            // Gunakan updateOrCreate agar tidak duplikat key
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // 4. Handle Upload Logo
        if ($request->hasFile('shop_logo')) {
            // Hapus logo lama jika ada (Opsional, biar hemat storage)
            $oldLogo = Setting::where('key', 'shop_logo')->value('value');
            if ($oldLogo) Storage::disk('public')->delete($oldLogo);

            $path = $request->file('shop_logo')->store('logos', 'public');
            Setting::updateOrCreate(['key' => 'shop_logo'], ['value' => $path]);
        }

        return back()->with('success', 'Konfigurasi sistem berhasil diperbarui! 🚀');
    }

    // Fitur Backup Database Manual
    public function downloadBackup()
    {
        $dbName = env('DB_DATABASE');
        $tables = DB::select('SHOW TABLES');
        $return = "-- Backup Database Bizniz.IO\n-- Tanggal: " . date('d-m-Y H:i:s') . "\n\n";

        foreach ($tables as $table) {
            $tableName = $table->{'Tables_in_' . $dbName};

            // Skip tabel migrasi biar gak error pas import ulang
            if ($tableName == 'migrations') continue;

            $return .= "\n\n-- Struktur Table: $tableName --\n";
            $return .= "DROP TABLE IF EXISTS `$tableName`;\n";

            $createTable = DB::select("SHOW CREATE TABLE `$tableName`");
            $return .= $createTable[0]->{'Create Table'} . ";\n\n";

            $return .= "-- Data Table: $tableName --\n";
            $rows = DB::table($tableName)->get();

            foreach ($rows as $row) {
                $return .= "INSERT INTO `$tableName` VALUES(";
                $values = [];
                foreach ($row as $val) {
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

        $fileName = 'backup_bizniz_' . date('Y_m_d_His') . '.sql';

        return response()->streamDownload(function () use ($return) {
            echo $return;
        }, $fileName);
    }
}
