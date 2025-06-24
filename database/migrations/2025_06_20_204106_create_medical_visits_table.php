<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medical_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->text('main_complaint')->nullable();
            $table->text('main_complaint_details')->nullable();
            $table->text('surgical_symptoms')->nullable();
            $table->text('other_systems_review')->nullable();
            $table->text('clinical_exam')->nullable();
            $table->text('clinical_direction')->nullable();
            $table->text('final_diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->text('recommendations')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_visits');
    }
};
