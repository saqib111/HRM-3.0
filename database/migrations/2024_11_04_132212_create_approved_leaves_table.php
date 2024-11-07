<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApprovedLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approved_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('leave_type'); // 1 = Annual Leave, 2 = Birthday Leave, 3 = Unpaid Leave, 4 = Marriage Leave
            $table->string('leave_sub_type')->nullable(); // To distinguish between full day or half day for Annual Leave
            $table->date('date'); // For both full and half day
            $table->time('start_time')->nullable(); // Only for half-day leaves
            $table->time('end_time')->nullable(); // Only for half-day leaves
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approved_leaves');
    }
}
