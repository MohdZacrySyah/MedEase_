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
    Schema::create('pemeriksaans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade'); // Link ke pendaftaran
        $table->foreignId('pasien_id')->constrained('users')->onDelete('cascade'); // Link ke user (pasien)
        $table->foreignId('tenaga_medis_id')->constrained('users')->onDelete('cascade'); // Link ke user (tenaga medis)
        $table->text('subjektif'); // Keluhan dari pasien
        $table->text('objektif'); // Hasil pemeriksaan fisik oleh dokter
        $table->text('assessment'); // Diagnosis atau penilaian dokter
        $table->text('plan'); // Rencana tindakan, resep, edukasi, dll.
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaans');
    }
};
