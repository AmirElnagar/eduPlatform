<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable(); // تعليمات الامتحان
            
            // الإعدادات
            $table->integer('duration_minutes')->nullable(); // مدة الامتحان
            $table->decimal('total_marks', 5, 2); // الدرجة الكلية
            $table->decimal('passing_marks', 5, 2); // درجة النجاح
            
            // التوقيت
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            
            // الخيارات
            $table->boolean('shuffle_questions')->default(false); // ترتيب عشوائي للأسئلة
            $table->boolean('show_results_immediately')->default(true); // إظهار النتيجة فوراً
            $table->boolean('allow_retake')->default(true); // السماح بإعادة الامتحان
            $table->integer('max_attempts')->nullable(); // عدد المحاولات المسموح بها
            
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['group_id', 'is_published']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};