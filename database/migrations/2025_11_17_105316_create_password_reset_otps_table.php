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
    Schema::create('password_reset_otps', function (Blueprint $table) {
        $table->string('email')->primary(); // Email sebagai kunci utama
        $table->string('otp', 6);         // Kode OTP 6 digit
        $table->timestamp('created_at')->nullable(); // Waktu OTP dibuat
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_otps');
    }
};
