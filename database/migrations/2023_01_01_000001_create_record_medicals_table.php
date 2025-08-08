<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
            public function up(): void {
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

                    // الحقول الجديدة
                    $table->decimal('weight', 5, 2)->nullable();
                    $table->decimal('height', 5, 2)->nullable();
                    $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable(); // زَمْرَة الدم
//حقول عادات شخصية
                    $table->enum('diet_type', ['Vegetarian', 'Regular', 'High Fat', 'Low Carb', 'Other'])->nullable()->comment('Type of diet');
                    $table->boolean('smokes')->nullable()->comment('Does the patient smoke');
                    $table->boolean('exercises_regularly')->nullable();
                    $table->integer('sleep_hours')->nullable();

                    $table->timestamps();
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
