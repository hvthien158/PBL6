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
            $table->date('_date');
            $table->time('time_check_in')->nullable();
            $table->time('time_check_out')->nullable();
            //0: Working, 1: Remote, 2: Not work
            $table->integer('status_am')->default(0);
            $table->integer('status_pm')->default(0);
            //0: User not request, 1: Waiting admin, 2: Admin accepted
            $table->integer('admin_accept_status')->nullable();
            $table->integer('admin_accept_time')->nullable();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('shift_id')->nullable();
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
