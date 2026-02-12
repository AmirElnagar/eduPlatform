<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->integer('attempt_number')->default(1);
            
            // النتائج
            $table->decimal('score', 5, 2)->nullable(); // الدرجة المحصلة
            $table->decimal('percentage', 5, 2)->nullable(); // النسبة المئوية
            $table->boolean('passed')->nullable();
            
            // التوقيت
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->integer('time_taken_minutes')->nullable(); // الوقت المستغرق
            
            // الحالة
            $table->string('status')->default('in_progress'); // in_progress, submitted, graded
            
            $table->timestamps();

            $table->index(['exam_id', 'student_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};