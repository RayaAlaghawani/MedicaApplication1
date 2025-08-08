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
        Schema::create('record_medical_past_diseases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('past_disease_id')->constrained('past_diseases')->cascadeOnDelete();
            $table->foreignId('record_medical_id')->constrained('record_medicals');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record_medical_past_diseases');
    }
};
