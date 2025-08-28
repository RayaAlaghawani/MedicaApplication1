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
            $table->foreignId('doctor_id')->nullable()->constrained('doctors');
            $table->enum('type', ['public', 'private']);
            $table->date('procedure_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('surgical_procedures');
    }
};
