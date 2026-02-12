<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type'); // offline, recorded, live
            
            // للدروس المسجلة
            $table->string('video_path')->nullable();
            $table->integer('duration_minutes')->nullable(); // مدة الفيديو
            $table->bigInteger('file_size')->nullable(); // حجم الملف بالبايت
            
            // للدروس المباشرة
            $table->string('meeting_url')->nullable();
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();
            
            // الجدولة
            $table->timestamp('scheduled_at')->nullable(); // موعد الدرس
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            
            // التحكم في الوصول
            $table->boolean('is_published')->default(false);
            $table->boolean('is_free')->default(false); // درس مجاني (تجريبي)
            
            $table->integer('order')->default(0); // ترتيب الدرس
            $table->timestamps();
            $table->softDeletes();

            $table->index(['group_id', 'type']);
            $table->index('is_published');
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};