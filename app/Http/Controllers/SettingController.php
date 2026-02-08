<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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
        if (!auth()->user()->hasRole('Owner')) {
            abort(403);
        }

        try {
            // 2. GENERATE: Run the backup command programmatically
            // We use the --only-db flag so we don't backup the whole file system (images), just data.
            Artisan::call('backup:run', ['--only-db' => true, '--disable-notifications' => true]);

            // 3. RETRIEVE: Find the newest backup file
            // Spatie stores backups in 'storage/app/Laravel' (default) or 'storage/app/backups'
            // You verify this path in config/backup.php -> 'name'
            $appName = config('backup.backup.name');
            $files = Storage::disk('local')->files($appName);

            // Sort files to get the latest one
            $latestFile = collect($files)->last();

            if (!$latestFile) {
                return back()->with('error', 'Backup failed to generate.');
            }

            // 4. DOWNLOAD: Send the zip file to the browser
            return Storage::disk('local')->download($latestFile);
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
