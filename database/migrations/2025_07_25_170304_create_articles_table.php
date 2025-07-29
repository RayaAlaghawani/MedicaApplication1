<?php

    /**
     * Run the migrations.
     */
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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('content_pdf_file');
            $table->string('image')->nullable();
            $table->string('category');
            $table->timestamp('published_at')->nullable();
            $table->enum('status', ['draft', 'published', 'reviewing'])->default('draft'); // حالة المقال
            $table->text('summary')->nullable();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('specialization_id')->constrained('specializations');  // ربط الطبيب بالتخصص
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
