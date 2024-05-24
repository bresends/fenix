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
        Schema::create('switch_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type');
            $table->timestamp('first_shift_date')->default(now());
            $table->string('first_shift_place');
            $table->string('first_shift_paying_military');
            $table->string('first_shift_receiving_military');
            $table->timestamp('second_shift_date')->default(now());
            $table->string('second_shift_place');
            $table->string('second_shift_paying_military');
            $table->string('second_shift_receiving_military');
            $table->text('motive');
            $table->string('file')->nullable();
            $table->string('status')->default('Em andamento');
            $table->text('final_judgment_reason')->nullable();
            $table->boolean('paid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('switch_shifts');
    }
};
