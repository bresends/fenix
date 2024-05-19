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
        Schema::create('fos', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Positivo', 'Negativo'])->default('Negativo');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('issuer')->constrained('militaries')->cascadeOnDelete();
            $table->timestamp('date_issued')->default(now());
            $table->string('reason');
            $table->string('excuse')->nullable();
            $table->string('observation')->nullable();
            $table->string('status')->default('Em andamento');
            $table->string('final_judgment_reason')->nullable();
            $table->boolean('paid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fos');
    }
};
