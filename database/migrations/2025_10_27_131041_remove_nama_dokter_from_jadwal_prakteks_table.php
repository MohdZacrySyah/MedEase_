<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void {
    Schema::table('jadwal_prakteks', function (Blueprint $table) {
        $table->dropColumn('nama_dokter');
    });
}
public function down(): void { // Untuk rollback
    Schema::table('jadwal_prakteks', function (Blueprint $table) {
        $table->string('nama_dokter')->nullable()->after('user_id');
    });
}
};
