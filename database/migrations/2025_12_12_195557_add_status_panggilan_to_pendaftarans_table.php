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
        // Status khusus untuk pemanggilan
        $table->enum('status_panggilan', ['menunggu', 'dipanggil', 'dialihkan'])->default('menunggu')->after('status');
        // Menghitung berapa kali dipanggil
        $table->integer('jumlah_panggilan')->default(0)->after('status_panggilan');
    });
}

public function down()
{
    Schema::table('pendaftarans', function (Blueprint $table) {
        $table->dropColumn(['status_panggilan', 'jumlah_panggilan']);
    });
}
};
