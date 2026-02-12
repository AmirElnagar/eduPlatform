<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'attempt_number',
        'score',
        'percentage',
        'passed',
        'started_at',
        'submitted_at',
        'time_taken_minutes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'attempt_number' => 'integer',
            'score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'passed' => 'boolean',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'time_taken_minutes' => 'integer',
        ];
    }

    // Relationships
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'attempt_id');
    }

    // Helper Methods
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }

    public function submit()
    {
        $this->update([
            'submitted_at' => now(),
            'time_taken_minutes' => $this->started_at->diffInMinutes(now()),
            'status' => 'submitted',
        ]);

        $this->autoGradeMcq();
    }

    private function autoGradeMcq()
    {
        $totalScore = 0;
        $mcqAnswers = $this->answers()->whereHas('question', function ($q) {
            $q->where('type', 'mcq');
        })->get();

        foreach ($mcqAnswers as $answer) {
            if ($answer->is_correct) {
                $totalScore += $answer->question->marks;
                $answer->update(['marks_obtained' => $answer->question->marks]);
            }
        }

        // Check if all questions are MCQ
        $hasEssay = $this->answers()->whereHas('question', function ($q) {
            $q->where('type', 'essay');
        })->exists();

        if (!$hasEssay) {
            $this->calculateFinalScore();
        }
    }

    public function calculateFinalScore()
    {
        $totalScore = $this->answers()->sum('marks_obtained');
        $percentage = ($totalScore / $this->exam->total_marks) * 100;
        $passed = $totalScore >= $this->exam->passing_marks;

        $this->update([
            'score' => $totalScore,
            'percentage' => $percentage,
            'passed' => $passed,
            'status' => 'graded',
        ]);
    }
}