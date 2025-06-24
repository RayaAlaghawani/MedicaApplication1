<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الدواء
            $table->string('dosage'); // جرعة الدواء (مثلاً 500mg)
            $table->integer('frequency'); // عدد مرات التناول في اليوم
            $table->date('start_date'); // تاريخ بدء الاستخدام
            $table->date('end_date')->nullable(); // تاريخ التوقف (يمكن أن يكون فارغًا)
            $table->text('notes')->nullable(); // ملاحظات
            $table->enum('type', ['عام', 'خاص'])->default('خاص'); // نوع الدواء
            $table->timestamps(); // created_at و updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
