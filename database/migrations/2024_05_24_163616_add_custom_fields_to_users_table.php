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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->default('A+');
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable();

            // Vehicle Fields
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_brand')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('vehicle_color')->nullable();
            $table->string('vehicle_licence_plate')->nullable();

            // Emergency Contact Fields
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_phone_number')->nullable();
            $table->string('emergency_contact_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('blood_type');
            $table->dropColumn('phone_number');
            $table->dropColumn('address');
            $table->dropColumn('vehicle_type');
            $table->dropColumn('vehicle_brand');
            $table->dropColumn('vehicle_color');
            $table->dropColumn('vehicle_licence_plate');
            $table->dropColumn('emergency_contact_name');
            $table->dropColumn('emergency_contact_relationship');
            $table->dropColumn('emergency_contact_phone_number');
            $table->dropColumn('emergency_contact_address');
        });
    }
};
