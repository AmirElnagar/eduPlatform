<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            
            // حالة الانضمام
            $table->string('status')->default('pending'); // pending, active, expired, cancelled
            
            // حالة الدفع
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, refunded
            $table->string('payment_method')->nullable(); // online, cash
            
            // التواريخ
            $table->timestamp('enrolled_at')->nullable(); // تاريخ التفعيل
            $table->timestamp('expires_at')->nullable(); // تاريخ انتهاء الاشتراك
            $table->timestamp('cancelled_at')->nullable(); // تاريخ الإلغاء
            
            // المبالغ
            $table->decimal('amount_paid', 8, 2)->default(0); // المبلغ المدفوع
            
            $table->text('notes')->nullable(); // ملاحظات المدرس
            $table->timestamps();

            $table->unique(['group_id', 'student_id']);
            $table->index('status');
            $table->index('payment_status');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_student');
    }
};