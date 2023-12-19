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
        Schema::create('systemtimes', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned();
            $table->primary('id');
            $table->date('_date');
            $table->time('time_check_in')->default('00:00');
            $table->time('time_check_out')->default('00:00');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('systemtimes');
    }
};
