<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('past_diseases', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المرض
            $table->enum('type', ['عام', 'خاص'])->default('عام'); // نوع المرض: عام أو خاص
            $table->string('code')->nullable(); // رمز المرض (مثل رمز ICD)
            $table->date('diagnosed_at')->nullable(); // تاريخ الإصابة
            $table->text('description')->nullable(); // وصف إضافي
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('past_diseases');
    }
};
