<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('grade_level'); // المرحلة الدراسية
            $table->string('parent_phone')->nullable(); // رقم ولي الأمر
            $table->text('address')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('grade_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};