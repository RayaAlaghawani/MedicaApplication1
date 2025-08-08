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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complaintable_type')->nullable();
            $table->unsignedBigInteger('complaintable_id')->nullable();
            $table->string('subject');
            $table->text('message');
            $table->enum('status', ['Pending', 'Accepted', 'Rejected'])->default('Pending');
            $table->text('admin_response')->nullable();
           // $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();
        });}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
