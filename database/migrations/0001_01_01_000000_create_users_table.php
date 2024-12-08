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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique();
            $table->string('username');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('joining_date');
            $table->enum('confirmation_status', ['0', '1'])->default('0');
            $table->string('image')->nullable();
            $table->integer('company_id');
            $table->integer('department_id');
            $table->integer('designation_id');
            $table->string('brand');
            $table->enum('week_days', ['5', '6'])->default('6');
            $table->enum('status', ['0', '1'])->default('1');
            $table->enum('role', ['1', '2', '3', '4', '5'])->default('5');
            $table->enum('userpass', ['0', '1'])->default(0);
            $table->rememberToken();
            $table->timestamps();
        });



        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
