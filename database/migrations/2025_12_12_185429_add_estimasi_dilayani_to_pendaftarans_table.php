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
        // Menambah kolom estimasi_dilayani setelah no_antrian
        $table->time('estimasi_dilayani')->nullable()->after('no_antrian');
    });
}

public function down()
{
    Schema::table('pendaftarans', function (Blueprint $table) {
        $table->dropColumn('estimasi_dilayani');
    });
}
};
