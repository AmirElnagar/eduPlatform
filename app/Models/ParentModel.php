<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id', 'user_id');
    }

    // Get all enrollments for all children
    public function getAllEnrollments()
    {
        $studentIds = $this->students->pluck('id');

        return Enrollment::whereIn('student_id', $studentIds)
            ->with(['student', 'group'])
            ->get();
    }

    // Get attendance summary for all children
    public function getAttendanceSummary()
    {
        $studentIds = $this->students->pluck('id');

        return Attendance::whereIn('student_id', $studentIds)
            ->with(['student', 'group'])
            ->orderBy('session_date', 'desc')
            ->get();
    }

    // Get exam results for all children
    public function getExamResults()
    {
        $studentIds = $this->students->pluck('id');

        return ExamAttempt::whereIn('student_id', $studentIds)
            ->where('status', 'graded')
            ->with(['student', 'exam'])
            ->orderBy('graded_at', 'desc')
            ->get();
    }
}
