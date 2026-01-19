<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// ===================================
// Exam Model
// ===================================
class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'title',
        'description',
        'total_marks',
        'pass_marks',
        'duration_minutes',
        'type',
        'is_published',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'total_marks' => 'decimal:2',
        'pass_marks' => 'decimal:2',
        'is_published' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order_index');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    // Check if exam is available for students
    public function isAvailable()
    {
        if (!$this->is_published) return false;

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;

        return true;
    }

    // Check if student has attempted this exam
    public function hasBeenAttemptedBy($studentId)
    {
        return $this->attempts()
            ->where('student_id', $studentId)
            ->exists();
    }

    // Get student's attempt
    public function getStudentAttempt($studentId)
    {
        return $this->attempts()
            ->where('student_id', $studentId)
            ->latest()
            ->first();
    }

    // Calculate pass percentage
    public function getPassPercentageAttribute()
    {
        if ($this->total_marks == 0) return 0;
        return round(($this->pass_marks / $this->total_marks) * 100, 2);
    }

    // Scope for published exams
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Scope for available exams
    public function scopeAvailable($query)
    {
        return $query->published()
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });
    }
}
