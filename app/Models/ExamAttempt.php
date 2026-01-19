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
        'score',
        'status',
        'started_at',
        'submitted_at',
        'graded_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

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
        return $this->hasMany(StudentAnswer::class, 'attempt_id');
    }

    // Submit attempt
    public function submit()
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Auto-grade MCQ questions
        $this->autoGradeMcq();
    }

    // Auto-grade MCQ questions
    public function autoGradeMcq()
    {
        $totalScore = 0;

        foreach ($this->answers as $answer) {
            if ($answer->question->type === 'mcq' && $answer->selected_option_id) {
                if ($answer->question->isCorrectOption($answer->selected_option_id)) {
                    $marks = $answer->question->marks;
                    $answer->update(['marks_obtained' => $marks]);
                    $totalScore += $marks;
                } else {
                    $answer->update(['marks_obtained' => 0]);
                }
            }
        }

        // Check if all questions are MCQ
        $allMcq = $this->exam->questions()->where('type', '!=', 'mcq')->count() === 0;

        if ($allMcq) {
            $this->update([
                'score' => $totalScore,
                'status' => 'graded',
                'graded_at' => now(),
            ]);
        }
    }

    // Calculate final score
    public function calculateScore()
    {
        $totalScore = $this->answers()->sum('marks_obtained');

        $this->update([
            'score' => $totalScore,
            'status' => 'graded',
            'graded_at' => now(),
        ]);

        return $totalScore;
    }

    // Check if student passed
    public function hasPassed()
    {
        return $this->score >= $this->exam->pass_marks;
    }

    // Get percentage
    public function getPercentageAttribute()
    {
        if ($this->exam->total_marks == 0) return 0;
        return round(($this->score / $this->exam->total_marks) * 100, 2);
    }

    // Check if exam time has expired
    public function hasExpired()
    {
        if (!$this->started_at || !$this->exam->duration_minutes) return false;

        $endTime = $this->started_at->addMinutes($this->exam->duration_minutes);
        return now()->gt($endTime);
    }

    // Get remaining time in minutes
    public function getRemainingMinutes()
    {
        if (!$this->started_at || !$this->exam->duration_minutes) return null;

        $endTime = $this->started_at->addMinutes($this->exam->duration_minutes);
        $remaining = now()->diffInMinutes($endTime, false);

        return max(0, $remaining);
    }
}
