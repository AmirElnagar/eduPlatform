<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('name'); // اسم المجموعة (يحدده المدرس)
            $table->string('subject'); // المادة
            $table->string('grade_level'); // المرحلة الدراسية
            $table->text('description')->nullable();
            $table->integer('max_students')->default(30); // الحد الأقصى للطلاب
            $table->integer('current_students')->default(0); // عدد الطلاب الحاليين
            $table->string('academic_year')->nullable(); // السنة الدراسية (2024/2025)
            
            // معلومات الاشتراك
            $table->decimal('price', 8, 2)->nullable(); // السعر الشهري
            $table->string('schedule')->nullable(); // مواعيد الدروس (JSON أو Text)
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['teacher_id', 'grade_level']);
            $table->index('subject');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};