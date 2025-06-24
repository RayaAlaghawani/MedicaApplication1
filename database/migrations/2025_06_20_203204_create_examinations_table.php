<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الفحص
            $table->enum('type', ['مخبري', 'شعاعي']); // نوع الفحص
            $table->string('image_path')->nullable(); // مسار الصورة (اختياري)
            $table->date('exam_date'); // تاريخ الفحص
            $table->text('summary')->nullable(); // ملخص النتائج

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('examinations');
    }
};
