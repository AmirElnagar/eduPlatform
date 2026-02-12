<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('exam_questions')->cascadeOnDelete();
            
            // الإجابة
            $table->text('answer')->nullable(); // للـ MCQ و Essay
            
            // التصحيح (للأسئلة المقالية)
            $table->decimal('marks_obtained', 5, 2)->nullable();
            $table->text('teacher_feedback')->nullable();
            $table->boolean('is_correct')->nullable(); // للـ MCQ تلقائياً
            
            $table->timestamps();

            $table->index(['attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};