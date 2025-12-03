<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up(): void
    {
        Schema::table('jadwal_prakteks', function (Blueprint $table) {
            // Mengubah tipe kolom 'hari' menjadi JSON
            $table->json('hari')->change();
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_prakteks', function (Blueprint $table) {
            // Opsi untuk mengembalikan jika terjadi kesalahan
            $table->string('hari')->change();
        });
    }
};