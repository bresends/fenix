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
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();
        });

        Schema::table('make_up_exams', function (Blueprint $table) {
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();
        });

        Schema::table('sick_notes', function (Blueprint $table) {
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();
        });

        Schema::table('switch_shifts', function (Blueprint $table) {
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();
        });

        Schema::table('exam_appeals', function (Blueprint $table) {
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fos', function (Blueprint $table) {
            $table->dropForeign(['evaluated_by']);
            $table->dropColumn(['evaluated_by', 'evaluated_at']);
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['evaluated_by']);
            $table->dropColumn(['evaluated_by', 'evaluated_at']);
        });

        Schema::table('make_up_exams', function (Blueprint $table) {
            $table->dropForeign(['evaluated_by']);
            $table->dropColumn(['evaluated_by', 'evaluated_at']);
        });

        Schema::table('sick_notes', function (Blueprint $table) {
            $table->dropForeign(['evaluated_by']);
            $table->dropColumn(['evaluated_by', 'evaluated_at']);
        });

        Schema::table('switch_shifts', function (Blueprint $table) {
            $table->dropForeign(['evaluated_by']);
            $table->dropColumn(['evaluated_by', 'evaluated_at']);
        });

        Schema::table('exam_appeals', function (Blueprint $table) {
            $table->dropForeign(['evaluated_by']);
            $table->dropColumn(['evaluated_by', 'evaluated_at']);
        });
    }
};
