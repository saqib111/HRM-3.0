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
        Schema::create('unlock_leave_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key to 'users' table
            $table->string('title'); // Leave title (mandatory)
            $table->text('description')->nullable(); // Optional description

            // Store leave details dynamically
            $table->json('leave_details'); // Store full-day, half-day, and off-day data in JSON format

            $table->decimal('leave_balance', 8, 2)->nullable(); // Store user's remaining annual leave balance
            $table->json('images')->nullable();
            
            // Approval details
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Team leader approval status
            $table->json('superadmin_id')->nullable(); // JSON to store the list of team leader IDs for approval
            $table->timestamp('superadmin_created_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unlock_leave_categories');
    }
};
