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
        Schema::create('record_medicals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');

            // مشترك
            $table->string('residence')->nullable();

            // أسئلة الأطفال
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->boolean('child_sleeps_well')->nullable();

            // أسئلة البالغين
            $table->string('phone_number')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('profession')->nullable();
            $table->string('education')->nullable();
            $table->boolean('insomnia')->nullable();

            // مشترك (جديد)
            $table->boolean('has_chronic_disease')->nullable();
            $table->boolean('takes_medications')->nullable();
            $table->boolean('has_allergies')->nullable();
            $table->timestamps();
            //مشتركة
            $table->string('blood_type')->nullable();
    $table->float('height')->nullable();
    $table->float('weight')->nullable();
    //عادات شخصية
        $table->enum('is_smoker', ['yes', 'no'])->nullable();
    $table->enum('physical_activity_level', ['active', 'moderate', 'low'])->nullable();
    $table->unsignedTinyInteger('sleep_hours')->nullable();
    $table->enum('diet_type', ['balanced', 'high_fat', 'high_sugar', 'vegetarian', 'irregular'])->nullable();


        });
    }

    /**
     * Reverse the migrations.

     */
    public function down(): void
    {
        Schema::dropIfExists('record_medicals');
    }
};
