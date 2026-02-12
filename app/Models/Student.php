<?php

namespace App\Models;

use App\Enums\GradeLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'grade_level',
        'parent_phone',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'grade_level' => GradeLevel::class,
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_student')
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

    public function activeGroups()
    {
        return $this->groups()
            ->wherePivot('status', 'active')
            ->wherePivot('payment_status', 'paid');
    }

    public function parents()
    {
        return $this->belongsToMany(ParentModel::class, 'parent_student', 'student_id', 'parent_id')
            ->withTimestamps();
    }

    public function parentRequests()
    {
        return $this->hasMany(ParentStudentRequest::class);
    }

    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Group::class);
    }

    public function attendance()
    {
        return $this->hasMany(LessonAttendance::class);
    }

    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function lessonViews()
    {
        return $this->hasMany(LessonView::class);
    }

    // Helper Methods
    public function hasAccessToGroup(Group $group): bool
    {
        return $this->activeGroups()->where('groups.id', $group->id)->exists();
    }

    public function totalPaidAmount(): float
    {
        return $this->payments()
            ->where('status', 'completed')
            ->sum('amount');
    }
}