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
            $table->string('name');
            $table->foreignId('doctor_id')->nullable()->constrained('doctors');
            $table->enum('type', ['public','private'])->default('public');
            $table->string('code')->nullable();
            $table->date('diagnosed_at')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('past_diseases');
    }
};
