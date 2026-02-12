<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonAttendance extends Model
{
    use HasFactory;

    protected $table = 'lesson_attendance';

    protected $fillable = [
        'lesson_id',
        'student_id',
        'status',
        'notes',
        'marked_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AttendanceStatus::class,
            'marked_at' => 'datetime',
        ];
    }

    // Relationships
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Helper Methods
    public function isPresent(): bool
    {
        return $this->status === AttendanceStatus::PRESENT;
    }

    public function isAbsent(): bool
    {
        return $this->status === AttendanceStatus::ABSENT;
    }

    public function isLate(): bool
    {
        return $this->status === AttendanceStatus::LATE;
    }

    public function isExcused(): bool
    {
        return $this->status === AttendanceStatus::EXCUSED;
    }
}