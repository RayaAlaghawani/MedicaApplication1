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
            $table->string('name');
            $table->$table->foreign('medical_visit')->references('id')->on('medical_visits')->onDelete('medical_visits');
            $table->enum('type', ['خاص', 'عام']);
            $table->date('procedure_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('surgical_procedures');
    }
};
