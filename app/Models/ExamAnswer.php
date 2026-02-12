<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer',
        'marks_obtained',
        'teacher_feedback',
        'is_correct',
    ];

    protected function casts(): array
    {
        return [
            'marks_obtained' => 'decimal:2',
            'is_correct' => 'boolean',
        ];
    }

    // Relationships
    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'question_id');
    }

    // Helper Methods
    public function checkMcqAnswer()
    {
        if ($this->question->isMcq()) {
            $isCorrect = $this->question->checkAnswer($this->answer);
            
            $this->update([
                'is_correct' => $isCorrect,
                'marks_obtained' => $isCorrect ? $this->question->marks : 0,
            ]);

            return $isCorrect;
        }

        return null;
    }

    public function gradeEssay(float $marks, ?string $feedback = null)
    {
        if ($this->question->isEssay()) {
            $this->update([
                'marks_obtained' => min($marks, $this->question->marks),
                'teacher_feedback' => $feedback,
            ]);

            // Recalculate attempt score
            $this->attempt->calculateFinalScore();
        }
    }
}