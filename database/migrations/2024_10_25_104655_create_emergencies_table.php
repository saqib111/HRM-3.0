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
        Schema::create('emergencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('e_name')->nullable();
            $table->string('e_phone')->nullable();
            $table->string('e_email')->nullable();
            $table->string('e_address')->nullable();
            $table->string('e_country')->nullable();
            $table->string('e_gender')->nullable();
            $table->string('e_relationship')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergencies');
    }
};