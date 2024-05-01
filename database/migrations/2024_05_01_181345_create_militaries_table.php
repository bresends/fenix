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
        Schema::create('militaries', function (Blueprint $table) {
            $table->id();
            $table->integer('rg')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('rank', ['Al Sd', 'Sd 2ª Classe', 'Sd 1ª Classe', 'Cb', '1º Sgt', '2º Sgt', 'ST', 'Al Of Adm', 'Cad', 'Asp Of', '2º Ten', '1º Ten', 'Cap', 'Major', 'TC', 'Cel'])->default('Al Sd');
            $table->enum('division', ['QP/Combatente', 'QOC', 'QP/Músico', 'QOA/Administrativo', 'QOA/Músico', 'QOS/Dentista', 'QOS/Médico'])->default('QP/Combatente');
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->default('A+');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('militaries');
    }
};
