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
            // مشترك: مكان السكن
            $table->string('residence')->nullable();

            // أسئلة الأطفال
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->boolean('child_sleeps_well')->nullable(); // نعم/لا

            // أسئلة البالغين
            $table->string('phone_number')->nullable();
            $table->string('marital_status')->nullable(); // عازب/متزوج
            $table->string('profession')->nullable();
            $table->string('education')->nullable();
            $table->boolean('insomnia')->nullable(); // صعوبة في النوم

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
