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
    Schema::table('pemeriksaans', function (Blueprint $table) {
        $table->text('subjektif')->nullable()->change();
        $table->text('objektif')->nullable()->change();
    });
}

public function down(): void // Untuk rollback
{
    Schema::table('pemeriksaans', function (Blueprint $table) {
        $table->text('subjektif')->nullable(false)->change();
        $table->text('objektif')->nullable(false)->change();
    });
}

};
