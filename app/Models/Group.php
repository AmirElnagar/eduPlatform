<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'grade_id',
        'name',
        'description',
        'max_students',
        'current_students',
        'price',
        'subscription_duration_days',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments()
    {
        return $this->enrollments()->where('status', 'active');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments')
            ->withPivot('status', 'enrolled_at', 'expires_at')
            ->withTimestamps();
    }

    public function contents()
    {
        return $this->hasMany(Content::class)->orderBy('order_index');
    }

    public function publishedContents()
    {
        return $this->contents()->where('is_published', true);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Check if group is full
    public function isFull()
    {
        return $this->current_students >= $this->max_students;
    }

    // Check if group is active and within date range
    public function isAvailable()
    {
        if (!$this->is_active) return false;

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;

        return true;
    }

    // Increment student count
    public function incrementStudents()
    {
        $this->increment('current_students');
    }

    // Decrement student count
    public function decrementStudents()
    {
        if ($this->current_students > 0) {
            $this->decrement('current_students');
        }
    }

    // Get available slots
    public function getAvailableSlotsAttribute()
    {
        return max(0, $this->max_students - $this->current_students);
    }

    // Scope for active groups
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for available groups (active and not full)
    public function scopeAvailable($query)
    {
        return $query->active()
            ->whereRaw('current_students < max_students');
    }

    // Scope by grade
    public function scopeByGrade($query, $gradeId)
    {
        return $query->where('grade_id', $gradeId);
    }

    // Scope by teacher
    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }
}
