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
        Schema::create('visa_infos', function (Blueprint $table) {
            $table->id();
            $table->string('passport_no')->nullable();
            $table->date('p_issue_date')->nullable();
            $table->date('p_expiry_date')->nullable();
            $table->string('visa_no')->nullable();
            $table->date('v_issue_date')->nullable();
            $table->date('v_expiry_date')->nullable();
            $table->string('foreign_no')->nullable();
            $table->date('f_expiry_date')->nullable();
            $table->foreignId('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_infos');
    }
};