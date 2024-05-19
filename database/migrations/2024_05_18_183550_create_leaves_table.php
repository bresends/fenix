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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date_leave')->default(now());
            $table->timestamp('date_back')->default(now());
            $table->string('motive');
            $table->string('missed_classes');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('accept_terms')->default(false);
            $table->string('file')->nullable();
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
        Schema::dropIfExists('leaves');
    }
};
