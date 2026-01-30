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
        $filename = "backup-" . date('Y-m-d-H-i-s') . ".sql";
        $handle = fopen(storage_path($filename), 'w+');

        // Logic backup sederhana (hanya struktur dasar untuk demo)
        // Untuk production, sarankan pakai package 'spatie/laravel-backup'
        fwrite($handle, "-- Backup Database Bizniz \n");
        fclose($handle);

        return response()->download(storage_path($filename))->deleteFileAfterSend(true);
    }
}
