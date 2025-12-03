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
        // Tambahkan kolom user_id setelah 'id', hubungkan ke tabel users
        $table->foreignId('user_id')->after('id')->nullable()->constrained('users')->onDelete('cascade');

        // Ubah kolom nama_dokter agar boleh kosong (opsional, bisa dihapus nanti)
        $table->string('nama_dokter')->nullable()->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void // Untuk rollback jika perlu
{
    Schema::table('jadwal_prakteks', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
        $table->string('nama_dokter')->nullable(false)->change(); // Kembalikan seperti semula
    });
}
};
