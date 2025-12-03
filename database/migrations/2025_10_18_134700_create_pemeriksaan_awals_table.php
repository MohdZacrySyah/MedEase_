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
    Schema::create('pemeriksaan_awals', function (Blueprint $table) {
        $table->id();
        // Kunci asing ke tabel pendaftarans (satu pendaftaran punya satu pemeriksaan awal)
        $table->foreignId('pendaftaran_id')->unique()->constrained('pendaftarans')->onDelete('cascade');
        $table->string('tekanan_darah')->nullable(); // cth: "120/80 mmHg"
        $table->string('berat_badan')->nullable(); // cth: "56 kg"
        $table->string('suhu_tubuh')->nullable(); // cth: "36.5 °C"
        $table->foreignId('dicatat_oleh')->nullable()->constrained('users'); // ID user admin/staf yg mencatat
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_awals');
    }
};
