<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->string('type'); // mcq, essay
            $table->text('question');
            $table->text('question_image')->nullable(); // صورة السؤال (اختياري)
            
            // للأسئلة MCQ
            $table->json('options')->nullable(); // الخيارات المتعددة
            $table->string('correct_answer')->nullable(); // الإجابة الصحيحة (A, B, C, D)
            
            // التقييم
            $table->decimal('marks', 5, 2); // درجة السؤال
            
            $table->integer('order')->default(0); // ترتيب السؤال
            $table->timestamps();

            $table->index(['exam_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};