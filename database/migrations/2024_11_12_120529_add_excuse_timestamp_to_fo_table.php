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
        Schema::table('fos', function (Blueprint $table) {
            $table->timestamp('excuse_timestamp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fos', function (Blueprint $table) {
            $table->dropColumn('excuse_timestamp');
        });
    }
};
