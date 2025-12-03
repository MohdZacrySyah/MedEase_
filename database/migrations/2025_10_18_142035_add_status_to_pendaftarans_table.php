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
    Schema::table('pendaftarans', function (Blueprint $table) {
        // Tambahkan kolom status setelah 'jadwal_dipilih'
        // Defaultnya 'Menunggu' saat pasien baru mendaftar
        $table->string('status')->after('jadwal_dipilih')->default('Menunggu'); 
    });
}

public function down(): void // Untuk rollback
{
    Schema::table('pendaftarans', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}
};
