<?php

namespace App\Models;

use App\Enums\GradeLevel;
use App\Enums\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'name',
        'subject',
        'grade_level',
        'description',
        'max_students',
        'current_students',
        'academic_year',
        'price',
        'schedule',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'subject' => Subject::class,
            'grade_level' => GradeLevel::class,
            'max_students' => 'integer',
            'current_students' => 'integer',
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'group_student')
            ->withPivot([
                'status',
                'payment_status',
                'payment_method',
                'enrolled_at',
                'expires_at',
                'cancelled_at',
                'amount_paid',
                'notes',
            ])
            ->withTimestamps();
    }

    public function activeStudents()
    {
        return $this->students()
            ->wherePivot('status', 'active')
            ->wherePivot('payment_status', 'paid');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Helper Methods
    public function isFull(): bool
    {
        return $this->current_students >= $this->max_students;
    }

    public function hasAvailableSeats(): bool
    {
        return $this->current_students < $this->max_students;
    }

    public function availableSeats(): int
    {
        return max(0, $this->max_students - $this->current_students);
    }

    public function incrementStudents()
    {
        $this->increment('current_students');
    }

    public function decrementStudents()
    {
        $this->decrement('current_students');
    }
}