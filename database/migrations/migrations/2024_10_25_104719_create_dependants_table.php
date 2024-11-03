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
            $table->string('d_name');
            $table->string('d_gender');
            $table->string('d_nationality');
            $table->date('d_dob');
            $table->string('d_passport_no');
            $table->date('d_pass_issue_date');
            $table->date('d_pass_expiry_date');
            $table->string('d_visa_no');
            $table->date('d_visa_issue_date');
            $table->date('d_visa_expiry_date');
            $table->Integer('user_id');
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