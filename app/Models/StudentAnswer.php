<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option_id',
        'essay_answer',
        'marks_obtained',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption()
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }

    // Check if answer is correct (for MCQ)
    public function isCorrect()
    {
        if ($this->question->type !== 'mcq') return null;

        return $this->question->isCorrectOption($this->selected_option_id);
    }
}
