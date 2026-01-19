<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'question_text',
        'type',
        'marks',
        'order_index',
    ];

    protected $casts = [
        'marks' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order_index');
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    // Get correct option (for MCQ)
    public function getCorrectOption()
    {
        return $this->options()->where('is_correct', true)->first();
    }

    // Check if answer is correct (for MCQ)
    public function isCorrectOption($optionId)
    {
        return $this->options()
            ->where('id', $optionId)
            ->where('is_correct', true)
            ->exists();
    }
}
