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
        Schema::create('annual_leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Set user_id as unsigned big integer
            $table->integer('leave_type'); // Assuming 14 or 28 leave types
            $table->decimal('leave_balance', 8, 2);
            $table->decimal('last_year_balance', 8, 2)->nullable(); // Nullable for last year's balance
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annual_leaves');
    }
};