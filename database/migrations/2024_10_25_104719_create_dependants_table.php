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
        Schema::create('dependants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('d_name')->nullable();
            $table->string('d_gender')->nullable();
            $table->string('d_nationality')->nullable();
            $table->date('d_dob')->nullable();
            $table->string('d_passport_no')->nullable();
            $table->date('d_pass_issue_date')->nullable();
            $table->date('d_pass_expiry_date')->nullable();
            $table->string('d_visa_no')->nullable();
            $table->date('d_visa_issue_date')->nullable();
            $table->date('d_visa_expiry_date')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dependants');
    }
};