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
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('day_of_week'); // 0 = الأحد
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('slot_duration'); // بالدقائق
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations
     *
     *
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
    }
};
