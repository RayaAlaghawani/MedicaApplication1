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
        Schema::create('allergies', function (Blueprint $table) {
            $table->id();
          $table->foreignId('record_medical_id')->constrained('record_medicals');
            $table->string('allergy_type');
            $table->string('allergen');
            $table->text('reaction_description')->nullable();
            $table->enum('severity', ['خفيفة', 'متوسطة', 'شديدة']);
            $table->date('start_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_private')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allergies');
    }
};
