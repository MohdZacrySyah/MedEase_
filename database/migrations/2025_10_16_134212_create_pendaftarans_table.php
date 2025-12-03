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
    Schema::create('pendaftarans', function (Blueprint $table) {
        $table->id(); // Kolom nomor urut otomatis (ID)
        $table->foreignId('user_id')->constrained('users'); // Menghubungkan ke user yang mendaftar
        $table->string('nama_layanan'); // Nama layanan yang dipilih (cth: "Dokter Umum")
        $table->string('nama_lengkap');
        $table->date('tanggal_lahir');
        $table->text('alamat');
        $table->string('no_telepon');
        $table->text('keluhan');
        $table->string('lama_keluhan');
        $table->date('jadwal_dipilih');
        $table->timestamps(); // Kolom created_at & updated_at otomatis
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
    }
};
