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

            // Lifestyle Fields
            $table->string('profession')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('education')->nullable();
            $table->string('residence')->nullable();
            $table->string('phone_number')->nullable();
            $table->boolean('smoker')->default(false);
            $table->boolean('alcohol')->default(false);
            $table->boolean('caffeine')->default(false);
            $table->boolean('exercise')->default(false);
            $table->enum('diet', ['vegetarian', 'gluten_free', 'natural', 'none'])->default('none');

            // Sleep & Mental Health Fields
            $table->integer('sleep_hours')->nullable();
            $table->boolean('insomnia')->default(false);
            $table->boolean('wakes_up_often')->default(false);
            $table->boolean('wakes_up_tired')->default(false);
            $table->boolean('uses_sleep_medication')->default(false);
            $table->boolean('recent_depression')->default(false);
            $table->boolean('anxiety_or_stress')->default(false);
            $table->boolean('visited_psychologist')->default(false);
            $table->boolean('trauma_experience')->default(false);
            $table->boolean('sleeps_due_to_overthinking')->default(false);

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
