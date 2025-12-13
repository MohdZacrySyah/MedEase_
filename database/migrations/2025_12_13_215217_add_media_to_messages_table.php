<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('media_path')->nullable()->after('message'); // Jalur ke file
            $table->string('media_type')->nullable()->after('media_path'); // Tipe (image, video, etc.)
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('media_path');
            $table->dropColumn('media_type');
        });
    }
};