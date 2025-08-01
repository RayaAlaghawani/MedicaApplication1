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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_banned')->default(false); // القيمة الافتراضية تكون false (غير محظور)
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->boolean('email_verified')->default(false);
            $table->string('email_verification_code')->nullable();
            $table->string('profile_image')->nullable();  // **هنا الحقل الجديد**
            $table->integer('age');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
