<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// 1. Import model Apoteker
use App\Models\Apoteker;
// 2. Import facade Hash untuk password
use Illuminate\Support\Facades\Hash; 

class ApotekerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Apoteker::updateOrCreate(
            [
                'email' => 'apoteker@klinik.com' // Kunci unik untuk pengecekan
            ],
            [
                'name' => 'Apoteker Klinik',
                'password' => Hash::make('12345678'), // Ganti 'password' dengan yg Anda mau
                'nomor_lisensi' => '123456789',
                'no_telepon' => '081234567890'
            ]
        );

        // Anda bisa tambahkan akun lain di sini jika perlu
        // Apoteker::updateOrCreate(
        //     ['email' => 'apoteker2@klinik.com'],
        //     [
        //         'name' => 'Apoteker Dua',
        //         'password' => Hash::make('password'),
        //     ]
        // );
    }
}