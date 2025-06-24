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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialization_id')->constrained('specializations');  // ربط الطبيب بالتخصص
            $table->string('first_name');
            $table->timestamp('email_verified_at');
            $table->string('last_name');
            $table->string('email');
            $table->string('image')->nullable()->default('hh');
            $table->text('device_token')->nullable()->default('sffry');
            $table->date('DateOfBirth');
            $table->string('phone');
            $table->string('password');
            $table->text('CurriculumVitae')->nullable()->default('ll');
            $table->string('Nationality');
            $table->string('ClinicAddress');
            $table->string('ProfessionalAssociationPhoto')->nullable()->default('eeeeee');
            $table->string('CertificateCopy')->nullable()->default('jh');
            $table->decimal('consultation_fee', 8, 2)->nullable();  // أجرة المعاينة للطبيب
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
