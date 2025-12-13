<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            // Ubah status jadi string agar bisa menampung 'Hadir', 'Menunggu', dll
            $table->string('status')->change(); 

            // Ubah status_panggilan jadi string agar bisa menampung 'hadir', 'dipanggil'
            $table->string('status_panggilan')->change(); 
        });
    }

    public function down(): void
    {
        // Tidak perlu rollback spesifik untuk kasus ini
    }
};