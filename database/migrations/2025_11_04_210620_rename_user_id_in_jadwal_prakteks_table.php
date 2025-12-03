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
            // 1. Hapus foreign key yang lama (yang mengarah ke tabel 'users')
            // Nama constraint bisa berbeda, cek di database Anda jika error.
            // Format umum: nama_tabel_nama_kolom_foreign
            $table->dropForeign('jadwal_prakteks_user_id_foreign');

            // 2. Ganti nama kolom dari 'user_id' menjadi 'tenaga_medis_id'
            $table->renameColumn('user_id', 'tenaga_medis_id');

            // 3. Tambahkan foreign key baru yang mengarah ke tabel 'tenaga_medis'
            $table->foreign('tenaga_medis_id')
                  ->references('id')
                  ->on('tenaga_medis')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_prakteks', function (Blueprint $table) {
            // Logika untuk membatalkan perubahan jika diperlukan
            $table->dropForeign(['tenaga_medis_id']);
            $table->renameColumn('tenaga_medis_id', 'user_id');
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};
