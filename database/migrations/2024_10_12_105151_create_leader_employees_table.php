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
        Schema::create('leader_employees', function (Blueprint $table) {
            $table->id();
            $table->integer('Leader_id');
            $table->integer('employee_id');
            $table->string('off_day')->nullable();
            
            $table->enum('status', ['0', '1'])->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leader_employees');
    }
};
