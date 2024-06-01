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
        Schema::table('sick_notes', function (Blueprint $table) {
            $table->boolean('archived')->default(false);
            $table->boolean('received')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sick_notes', function (Blueprint $table) {
            $table->dropColumn('paid');
            $table->dropColumn('received');
        });
    }
};
