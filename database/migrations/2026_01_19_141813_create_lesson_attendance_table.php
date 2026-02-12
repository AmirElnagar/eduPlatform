<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('status'); // present, absent, late, excused
            $table->text('notes')->nullable();
            $table->timestamp('marked_at')->useCurrent();
            $table->timestamps();

            $table->unique(['lesson_id', 'student_id']);
            $table->index('status');
        });

        // جدول لتتبع مشاهدة الدروس المسجلة
        Schema::create('lesson_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->integer('watch_duration')->default(0); // بالثواني
            $table->integer('last_position')->default(0); // آخر موضع في الفيديو
            $table->boolean('completed')->default(false);
            $table->timestamp('last_watched_at')->nullable();
            $table->timestamps();

            $table->unique(['lesson_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_views');
        Schema::dropIfExists('lesson_attendance');
    }
};