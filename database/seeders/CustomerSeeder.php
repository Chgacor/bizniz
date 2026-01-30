<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Budi Santoso', 'Siti Aminah', 'Rudi Hartono', 'Dewi Lestari',
            'Agus Salim', 'Putri Maharani', 'Joko Widodo', 'Megawati',
            'Susilo Bambang', 'Eko Patrio', 'Raffi Ahmad', 'Nagita Slavina'
        ];

        foreach ($names as $index => $name) {
            Customer::create([
                'name' => $name,
                'phone' => '0812' . rand(10000000, 99999999),
                'email' => strtolower(str_replace(' ', '', $name)) . '@gmail.com',
                'notes' => ($index % 3 == 0) ? 'Pelanggan VIP' : null,
            ]);
        }
    }
}
