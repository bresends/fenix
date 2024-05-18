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
        Schema::create('sick_notes', function (Blueprint $table) {
            $table->id();
            $table->string('file');
            $table->timestamp('date_issued')->default(now());
            $table->integer('days_absent');
            $table->string('motive');
            $table->string('restrictions');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sick_notes');
    }
};
