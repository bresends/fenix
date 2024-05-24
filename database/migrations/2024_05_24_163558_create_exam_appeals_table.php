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
        Schema::create('exam_appeals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('exam');
            $table->integer('question');
            $table->string('discipline');
            $table->enum('type', ['Teórica', 'Prática'])->default('Teórica');
            $table->text('motive');
            $table->text('bibliography');
            $table->boolean('accept_terms')->default(false);
            $table->string('file')->nullable();
            $table->string('status')->default('Em andamento');
            $table->text('final_judgment_reason')->nullable();
            $table->boolean('archived')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_appeals');
    }
};
