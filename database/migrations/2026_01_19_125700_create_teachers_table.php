<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject'); // المادة التي يدرسها
            $table->text('bio')->nullable();
            $table->integer('years_of_experience')->default(0);
            $table->decimal('hourly_rate', 8, 2)->nullable(); // سعر الساعة (للمستقبل)
            
            // Subscription fields (معطلة حالياً - للمستقبل)
            $table->boolean('is_subscribed')->default(false);
            $table->timestamp('subscription_start')->nullable();
            $table->timestamp('subscription_end')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('subject');
            $table->index('is_subscribed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};