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
        Schema::create('check_verify', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('type')->nullable();
            $table->string('device_ip')->nullable();
            $table->datetime('fingerprint_in');
            $table->timestamp('last_processed_timestamp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_verify');
    }
};
