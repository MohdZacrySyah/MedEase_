<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            // Anda bisa menambahkan seeder lain di sini jika perlu
            // Contoh: UserSeeder::class,
            
        ]);
        $this->call(ApotekerSeeder::class);
    }
}
