<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentAccess extends Model
{
    use HasFactory;

    protected $table = 'content_access';

    protected $fillable = [
        'content_id',
        'access_type',
        'required_exam_id',
        'required_attendance_count',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function requiredExam()
    {
        return $this->belongsTo(Exam::class, 'required_exam_id');
    }

    // Check if student meets access requirements
    public function checkAccess($studentId)
    {
        switch ($this->access_type) {
            case 'free':
                return true;

            case 'enrollment':
                return Enrollment::where('student_id', $studentId)
                    ->where('group_id', $this->content->group_id)
                    ->active()
                    ->exists();

            case 'after_exam':
                if (!$this->required_exam_id) return true;

                return ExamAttempt::where('student_id', $studentId)
                    ->where('exam_id', $this->required_exam_id)
                    ->where('status', 'graded')
                    ->exists();

            case 'after_attendance':
                if (!$this->required_attendance_count) return true;

                $attendanceCount = Attendance::where('student_id', $studentId)
                    ->where('group_id', $this->content->group_id)
                    ->where('status', 'present')
                    ->count();

                return $attendanceCount >= $this->required_attendance_count;

            default:
                return false;
        }
    }
}
