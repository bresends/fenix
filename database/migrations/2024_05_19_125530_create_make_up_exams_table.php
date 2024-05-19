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
        Schema::create('make_up_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('discipline_name');
            $table->timestamp('exam_date')->default(now());
            $table->enum('type', ['Teórica', 'Prática'])->default('Teórica');
            $table->text('motive');
            $table->string('file')->nullable();
            $table->string('status')->default('Em andamento');
            $table->timestamp('date_back')->default(now());
            $table->text('final_judgment_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('make_up_exams');
    }
};
