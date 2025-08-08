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
        Schema::create('record_medical_surgical_procedures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgical_procedure_id')->constrained('surgical_procedures')->cascadeOnDelete();
            $table->foreignId('record_medical_id')->constrained('record_medicals');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record_medical_surgical_procedures');
    }
};
