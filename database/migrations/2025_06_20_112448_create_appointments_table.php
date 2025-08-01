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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->date('appointment_date'); // مثال: 2025-07-17
            $table->time('appointment_time'); // مثال: 09:00:00
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->enum('reason_reservation', ['First_Visit', 'Follow_up'])->default('First_Visit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
