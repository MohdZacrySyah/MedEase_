<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_doctor_availabilities_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_availabilities', function (Blueprint $table) {
            $table->id();
            
// 👇 PERBAIKAN: Gunakan tenaga_medis_id dan constrained ke tabel 'tenaga_medis'
    $table->foreignId('tenaga_medis_id')->constrained('tenaga_medis');
    
    $table->date('date');
    
    // Batasan unique gabungan
    $table->unique(['tenaga_medis_id', 'date']);
    
    $table->integer('max_slots')->default(0); 
    $table->integer('booked_slots')->default(0); 
    $table->boolean('is_available')->default(true); 
    $table->string('reason')->nullable(); 
    $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_availabilities');
    }
};