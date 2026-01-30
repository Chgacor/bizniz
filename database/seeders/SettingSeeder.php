<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'store_name', 'value' => 'Bizniz Store', 'group' => 'general'],
            ['key' => 'store_phone', 'value' => '0812-3456-7890', 'group' => 'general'],
            ['key' => 'store_address', 'value' => 'Jl. Merdeka No. 45, Jakarta', 'group' => 'general'],
            ['key' => 'tax_rate', 'value' => '11', 'group' => 'finance'], // PPN 11%
            ['key' => 'receipt_footer', 'value' => 'Terima kasih telah berbelanja!', 'group' => 'pos'],
        ];

        foreach ($settings as $setting) {
            // updateOrCreate: Jika KEY sudah ada, update isinya. Jika belum, buat baru.
            // Ini mencegah error "Duplicate Entry"
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'group' => $setting['group']
                ]
            );
        }
    }
}
