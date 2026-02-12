<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            
            // معلومات الدفع
            $table->decimal('amount', 8, 2);
            $table->string('payment_method'); // online, cash
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            
            // للدفع الأونلاين
            $table->string('transaction_id')->nullable()->unique(); // معرف المعاملة من بوابة الدفع
            $table->string('payment_gateway')->nullable(); // paymob, fawry, etc
            $table->json('payment_details')->nullable(); // تفاصيل إضافية من بوابة الدفع
            
            // للدفع الكاش
            $table->text('notes')->nullable(); // ملاحظات (مثلاً: تم الدفع يوم كذا)
            $table->timestamp('paid_at')->nullable(); // تاريخ الدفع الفعلي
            
            // الفترة المدفوعة
            $table->date('period_start')->nullable(); // بداية الفترة المدفوعة
            $table->date('period_end')->nullable(); // نهاية الفترة المدفوعة
            
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'group_id']);
            $table->index('status');
            $table->index('payment_method');
            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};