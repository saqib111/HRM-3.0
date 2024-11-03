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
        Schema::create('leave_management', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key to 'users' table
            $table->string('title'); // Leave title (mandatory)
            $table->text('description')->nullable(); // Optional description

            // Store leave details dynamically
            $table->json('leave_details'); // Store full-day, half-day, and off-day data in JSON format

            $table->integer('leave_balance')->nullable(); // Store the user's remaining annual leave balance at the time of application

            // Approval details
            $table->enum('status_1', ['pending', 'approved', 'rejected'])->default('pending'); // Team leader approval status
            $table->json('team_leader_ids')->nullable(); // JSON to store the list of team leader IDs for approval
            $table->enum('status_2', ['pending', 'approved', 'rejected'])->default('pending'); // Manager approval status
            $table->json('manager_ids')->nullable(); // JSON to store the list of manager IDs for approval

            // Approval timestamps and approver IDs
            $table->foreignId('first_approval_id')->nullable()->constrained('users'); // ID of the first approver (Team Leader)
            $table->timestamp('first_approval_created_time')->nullable(); // Timestamp for the first approval
            $table->foreignId('second_approval_id')->nullable()->constrained('users'); // ID of the second approver (Manager)
            $table->timestamp('second_approval_created_time')->nullable(); // Timestamp for the second approval
            $table->foreignId('hr_approval_id')->nullable()->constrained('users'); // ID of the HR approver
            $table->timestamp('hr_approval_created_time')->nullable(); // Timestamp for the HR approval

            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_management');
    }
};
