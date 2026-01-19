<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Group;
use App\Models\Content;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\StudentAnswer;
use App\Models\Grade;
use App\Models\Review;
use Illuminate\Http\Request;

class StudentExamController extends Controller
{
    /**
     * Get available exams
     */
    public function index(Request $request, $groupId)
    {
        $student = $request->user()->student;

        // Check enrollment
        if (!$student->isEnrolledIn($groupId)) {
            return response()->json([
                'message' => 'يجب التسجيل في المجموعة أولاً',
            ], 403);
        }

        $exams = Exam::where('group_id', $groupId)
            ->available()
            ->get()
            ->map(function ($exam) use ($student) {
                $attempt = $exam->getStudentAttempt($student->id);

                return [
                    'exam' => $exam,
                    'has_attempted' => $attempt !== null,
                    'attempt' => $attempt,
                ];
            });

        return response()->json([
            'exams' => $exams,
        ]);
    }

    /**
     * Start exam
     */
    public function start(Request $request, $groupId, $examId)
    {
        $student = $request->user()->student;
        $exam = Exam::where('group_id', $groupId)->available()->findOrFail($examId);

        // Check if already attempted
        if ($exam->hasBeenAttemptedBy($student->id)) {
            return response()->json([
                'message' => 'لقد قمت بحل هذا الامتحان من قبل',
            ], 422);
        }

        // Create attempt
        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Load questions
        $questions = $exam->questions()
            ->with('options')
            ->orderBy('order_index')
            ->get();

        return response()->json([
            'attempt' => $attempt,
            'questions' => $questions,
            'duration_minutes' => $exam->duration_minutes,
        ]);
    }

    /**
     * Submit answer
     */
    public function submitAnswer(Request $request, $groupId, $examId, $attemptId)
    {
        $student = $request->user()->student;
        $attempt = ExamAttempt::where('exam_id', $examId)
            ->where('student_id', $student->id)
            ->findOrFail($attemptId);

        if ($attempt->status !== 'in_progress') {
            return response()->json([
                'message' => 'لا يمكن تقديم إجابات بعد تسليم الامتحان',
            ], 422);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_option_id' => 'nullable|exists:question_options,id',
            'essay_answer' => 'nullable|string',
        ]);

        StudentAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'selected_option_id' => $validated['selected_option_id'] ?? null,
                'essay_answer' => $validated['essay_answer'] ?? null,
            ]
        );

        return response()->json([
            'message' => 'تم حفظ الإجابة',
        ]);
    }

    /**
     * Submit exam
     */
    public function submit(Request $request, $groupId, $examId, $attemptId)
    {
        $student = $request->user()->student;
        $attempt = ExamAttempt::where('exam_id', $examId)
            ->where('student_id', $student->id)
            ->findOrFail($attemptId);

        if ($attempt->status !== 'in_progress') {
            return response()->json([
                'message' => 'تم تسليم الامتحان من قبل',
            ], 422);
        }

        $attempt->submit();

        return response()->json([
            'message' => 'تم تسليم الامتحان بنجاح',
            'attempt' => $attempt->fresh(),
        ]);
    }

    /**
     * Get exam result
     */
    public function getResult(Request $request, $groupId, $examId, $attemptId)
    {
        $student = $request->user()->student;
        $attempt = ExamAttempt::where('exam_id', $examId)
            ->where('student_id', $student->id)
            ->with(['answers.question', 'exam'])
            ->findOrFail($attemptId);

        if ($attempt->status !== 'graded') {
            return response()->json([
                'message' => 'لم يتم تصحيح الامتحان بعد',
            ], 422);
        }

        return response()->json([
            'attempt' => $attempt,
            'passed' => $attempt->hasPassed(),
        ]);
    }
}

class StudentReviewController extends Controller
{
    /**
     * Submit review for teacher
     */
    public function submit(Request $request, $teacherId)
    {
        $student = $request->user()->student;

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        // Check if student is/was enrolled in any of teacher's groups
        $hasEnrollment = Enrollment::where('student_id', $student->id)
            ->whereHas('group', function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })
            ->exists();

        if (!$hasEnrollment) {
            return response()->json([
                'message' => 'يجب أن تكون مسجلاً في إحدى مجموعات المدرس لتقييمه',
            ], 403);
        }

        $review = Review::updateOrCreate(
            [
                'teacher_id' => $teacherId,
                'student_id' => $student->id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'is_approved' => false, // Needs admin approval
            ]
        );

        return response()->json([
            'message' => 'تم إرسال التقييم، في انتظار الموافقة',
            'review' => $review,
        ]);
    }
}
