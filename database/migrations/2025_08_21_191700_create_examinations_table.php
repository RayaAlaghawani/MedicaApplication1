<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('doctor_id')->nullable()->constrained('doctors');
            $table->foreignId('record_medical_id')->constrained('record_medicals')->onDelete('cascade');
            // اسم الفحص
            $table->enum('type', ['Laboratory', 'Radiology']); // نوع الفحص
            $table->text('summary'); // ملخص النتيجة
            $table->string('image_path')->nullable(); // صورة/ملف مرفق
            $table->date('exam_date')->nullable(); // تاريخ الفحص
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examinations');
    }
};
