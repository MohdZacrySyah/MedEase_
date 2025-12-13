<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            // Waktu aktual mulai diperiksa (untuk visibilitas "Sedang Diperiksa")
            $table->timestamp('waktu_mulai_periksa')->nullable()->after('estimasi_dilayani');
            // Waktu aktual selesai diperiksa (KRUSIAL untuk perhitungan ulang)
            $table->timestamp('waktu_selesai_periksa')->nullable()->after('waktu_mulai_periksa');
            // Menambahkan kolom status saat ini
            $table->string('status_antrian')->default('Menunggu')->after('status');
        });
    }

    public function down()
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->dropColumn(['waktu_mulai_periksa', 'waktu_selesai_periksa', 'status_antrian']);
        });
    }
};