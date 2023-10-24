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
        Schema::create('time_keepings', function (Blueprint $table) {
            $table->id();
            $table->dateTime('time_check_in');
            $table->dateTime('time_check_out');
            $table->text('note');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('shift_id')->references('id')->on('shifts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_keepings');
    }
};
