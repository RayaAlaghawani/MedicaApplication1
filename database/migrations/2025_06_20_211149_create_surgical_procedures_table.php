<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('surgical_procedures', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم العملية أو اسم المريض حسب الحاجة
            $table->enum('type', ['خاص', 'عام']); // نوع العملية
            $table->date('procedure_date'); // تاريخ الإجراء
            $table->text('notes')->nullable(); // ملاحظات (اختيارية)
            $table->timestamps(); // created_at و updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('surgical_procedures');
    }
};
