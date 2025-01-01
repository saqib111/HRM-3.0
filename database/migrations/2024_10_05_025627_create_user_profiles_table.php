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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('real_name')->nullable();
            $table->date('dob')->nullable();
            $table->string('phone')->nullable();
            $table->string('office')->nullable();
            $table->string('accomodation')->nullable();
            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();
            $table->string('telegram')->nullable();
            $table->enum('allowed_ul', ['0', '1'])->default(0);
            $table->string('gender')->nullable();
            $table->string('remarks')->nullable();
            $table->string('leave_assign_user_id')->nullable();
            $table->string('second_leave_assign_user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
