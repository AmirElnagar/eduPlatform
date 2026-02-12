<?php

namespace App\Models;

use App\Enums\ExamQuestionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'type',
        'question',
        'question_image',
        'options',
        'correct_answer',
        'marks',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'type' => ExamQuestionType::class,
            'options' => 'array',
            'marks' => 'decimal:2',
            'order' => 'integer',
        ];
    }

    // Relationships
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'question_id');
    }

    // Helper Methods
    public function isMcq(): bool
    {
        return $this->type === ExamQuestionType::MCQ;
    }

    public function isEssay(): bool
    {
        return $this->type === ExamQuestionType::ESSAY;
    }

    public function checkAnswer(string $answer): bool
    {
        if ($this->isEssay()) {
            return false; // Essay questions need manual grading
        }

        return strtoupper(trim($answer)) === strtoupper(trim($this->correct_answer));
    }

    public function getQuestionImageUrlAttribute(): ?string
    {
        return $this->question_image ? asset('storage/' . $this->question_image) : null;
    }
}