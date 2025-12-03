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
    Schema::create('reseps', function (Blueprint $table) {
        $table->id();

        // Kunci Asing ke tabel pemeriksaan (yang berisi resepnya)
        $table->foreignId('pemeriksaan_id')
              ->constrained('pemeriksaans')
              ->onDelete('cascade');

        // Kunci Asing ke pasien (agar apoteker tahu ini resep siapa)
        $table->foreignId('pasien_id')
              ->constrained('users') // Asumsi tabel pasien Anda adalah 'users'
              ->onDelete('cascade');
        
        // Kunci Asing ke apoteker (siapa yang memproses resep ini)
        // Dibuat nullable() karena awalnya belum ada yang proses
        $table->foreignId('apoteker_id')
              ->nullable()
              ->constrained('apotekers')
              ->onDelete('set null');

        // Status antrian resep
        $table->string('status')->default('Menunggu'); 
        // Status bisa: 'Menunggu', 'Diproses', 'Selesai', 'Dibatalkan'

        // Catatan dari apoteker (misal: "Obat X diganti Y")
        $table->text('catatan_apoteker')->nullable();
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseps');
    }
};
