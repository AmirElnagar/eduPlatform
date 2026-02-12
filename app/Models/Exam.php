<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id',
        'title',
        'description',
        'instructions',
        'duration_minutes',
        'total_marks',
        'passing_marks',
        'starts_at',
        'ends_at',
        'shuffle_questions',
        'show_results_immediately',
        'allow_retake',
        'max_attempts',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'total_marks' => 'decimal:2',
            'passing_marks' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'shuffle_questions' => 'boolean',
            'show_results_immediately' => 'boolean',
            'allow_retake' => 'boolean',
            'max_attempts' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    // Relationships
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    // Helper Methods
    public function isAvailable(): bool
    {
        if (!$this->is_published) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        return true;
    }

    public function canStudentTake(Student $student): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        if (!$this->allow_retake) {
            return !$this->attempts()->where('student_id', $student->id)->exists();
        }

        if ($this->max_attempts) {
            $attempts = $this->attempts()->where('student_id', $student->id)->count();
            return $attempts < $this->max_attempts;
        }

        return true;
    }

    public function getStudentAttempts(Student $student)
    {
        return $this->attempts()->where('student_id', $student->id)->count();
    }
}