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
    Schema::table('users', function (Blueprint $table) {
        $table->date('tanggal_lahir')->nullable()->after('email');
        $table->text('alamat')->nullable()->after('tanggal_lahir');
        $table->string('no_hp')->nullable()->after('alamat');
        $table->string('profile_photo_path', 2048)->nullable()->after('no_hp');
    });
}
public function down(): void { // Untuk rollback
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['tanggal_lahir', 'alamat', 'no_hp', 'profile_photo_path']);
    });
}
};
