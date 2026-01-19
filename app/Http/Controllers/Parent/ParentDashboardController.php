<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class ParentDashboardController extends Controller
{
    /**
     * Get parent's children
     */
    public function getChildren(Request $request)
    {
        $parent = $request->user()->parent;

        $students = $parent->students()
            ->with(['user', 'grade'])
            ->get();

        return response()->json([
            'students' => $students,
        ]);
    }

    /**
     * Link a student to parent
     */
    public function linkStudent(Request $request)
    {
        $parent = $request->user()->parent;

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::findOrFail($validated['student_id']);

        // Check if student already has a parent
        if ($student->parent_id) {
            return response()->json([
                'message' => 'هذا الطالب مرتبط بولي أمر آخر',
            ], 422);
        }

        $student->update(['parent_id' => $parent->user_id]);

        return response()->json([
            'message' => 'تم ربط الطالب بنجاح',
            'student' => $student->fresh()->load('user'),
        ]);
    }

    /**
     * Get all enrollments for all children
     */
    public function getAllEnrollments(Request $request)
    {
        $parent = $request->user()->parent;
        $enrollments = $parent->getAllEnrollments();

        return response()->json([
            'enrollments' => $enrollments,
        ]);
    }

    /**
     * Get attendance for a specific child
     */
    public function getStudentAttendance(Request $request, $studentId)
    {
        $parent = $request->user()->parent;

        // Verify student belongs to parent
        $student = $parent->students()->findOrFail($studentId);

        $validated = $request->validate([
            'group_id' => 'nullable|exists:groups,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $query = $student->attendances()->with('group.teacher.user');

        if (isset($validated['group_id'])) {
            $query->where('group_id', $validated['group_id']);
        }

        if (isset($validated['from_date'])) {
            $query->where('session_date', '>=', $validated['from_date']);
        }

        if (isset($validated['to_date'])) {
            $query->where('session_date', '<=', $validated['to_date']);
        }

        $attendance = $query->orderBy('session_date', 'desc')->get();

        return response()->json([
            'student' => $student,
            'attendance' => $attendance,
        ]);
    }

    /**
     * Get exam results for a specific child
     */
    public function getStudentExamResults(Request $request, $studentId)
    {
        $parent = $request->user()->parent;

        // Verify student belongs to parent
        $student = $parent->students()->findOrFail($studentId);

        $validated = $request->validate([
            'group_id' => 'nullable|exists:groups,id',
        ]);

        $query = $student->examAttempts()
            ->where('status', 'graded')
            ->with(['exam.group.teacher.user']);

        if (isset($validated['group_id'])) {
            $query->whereHas('exam', function ($q) use ($validated) {
                $q->where('group_id', $validated['group_id']);
            });
        }

        $results = $query->orderBy('graded_at', 'desc')->get();

        return response()->json([
            'student' => $student,
            'results' => $results,
        ]);
    }

    /**
     * Get complete overview for a child
     */
    public function getStudentOverview(Request $request, $studentId)
    {
        $parent = $request->user()->parent;

        // Verify student belongs to parent
        $student = $parent->students()
            ->with(['user', 'grade'])
            ->findOrFail($studentId);

        // Active enrollments
        $enrollments = $student->activeEnrollments()
            ->with('group.teacher.user')
            ->get();

        // Recent attendance
        $recentAttendance = $student->attendances()
            ->with('group')
            ->orderBy('session_date', 'desc')
            ->limit(10)
            ->get();

        // Recent exam results
        $recentResults = $student->examAttempts()
            ->where('status', 'graded')
            ->with('exam')
            ->orderBy('graded_at', 'desc')
            ->limit(10)
            ->get();

        // Statistics per group
        $groupStats = $enrollments->map(function ($enrollment) use ($student) {
            $groupId = $enrollment->group_id;

            return [
                'group' => $enrollment->group,
                'attendance_rate' => $student->getAttendanceRate($groupId),
                'average_score' => $student->getAverageScore($groupId),
            ];
        });

        return response()->json([
            'student' => $student,
            'enrollments' => $enrollments,
            'recent_attendance' => $recentAttendance,
            'recent_results' => $recentResults,
            'group_stats' => $groupStats,
        ]);
    }
}
