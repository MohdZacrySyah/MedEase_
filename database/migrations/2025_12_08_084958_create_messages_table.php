<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // Kolom penting untuk multi-table
            $table->unsignedBigInteger('sender_id');
            $table->string('sender_type'); // 'user' atau 'medis'
            $table->unsignedBigInteger('receiver_id');
            $table->string('receiver_type'); // 'user' atau 'medis'

            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
};