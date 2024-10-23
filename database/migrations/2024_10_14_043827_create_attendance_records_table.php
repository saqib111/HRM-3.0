<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('shift_id');
            $table->datetime('shift_in');
            $table->datetime('shift_out');
            $table->integer('duty_hours')->nullable();
            $table->datetime('check_in')->nullable();
            $table->datetime('check_out')->nullable();
            $table->integer('emergency_checkout')->nullable();
            $table->enum('status', ['0', '1'])->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
