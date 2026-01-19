<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'grade_id',
        'profile_image',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
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
        return $this->enrollments()->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'enrollments')
            ->withPivot('status', 'enrolled_at', 'expires_at')
            ->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Check if student is enrolled in a group
    public function isEnrolledIn($groupId)
    {
        return $this->activeEnrollments()
            ->where('group_id', $groupId)
            ->exists();
    }

    // Get student's attendance rate for a group
    public function getAttendanceRate($groupId)
    {
        $total = $this->attendances()
            ->where('group_id', $groupId)
            ->count();

        if ($total === 0) return 0;

        $present = $this->attendances()
            ->where('group_id', $groupId)
            ->where('status', 'present')
            ->count();

        return round(($present / $total) * 100, 2);
    }

    // Get student's average score for a group
    public function getAverageScore($groupId)
    {
        return $this->examAttempts()
            ->whereHas('exam', function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            })
            ->where('status', 'graded')
            ->avg('score');
    }

    // Scope for students by grade
    public function scopeByGrade($query, $gradeId)
    {
        return $query->where('grade_id', $gradeId);
    }

    // Scope for students with parent
    public function scopeHasParent($query)
    {
        return $query->whereNotNull('parent_id');
    }
}
