<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class TeacherAttendanceController extends Controller
{
    /**
     * Get group attendance records
     */
    public function index(Request $request, $groupId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);

        $validated = $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $query = $group->attendances()->with('student.user');

        if (isset($validated['from_date'])) {
            $query->where('session_date', '>=', $validated['from_date']);
        }

        if (isset($validated['to_date'])) {
            $query->where('session_date', '<=', $validated['to_date']);
        }

        $attendance = $query->orderBy('session_date', 'desc')->get();

        return response()->json([
            'attendance' => $attendance,
        ]);
    }

    /**
     * Mark attendance for a session
     */
    public function markAttendance(Request $request, $groupId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);

        $validated = $request->validate([
            'session_date' => 'required|date',
            'students' => 'required|array',
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.status' => 'required|in:present,absent,late,excused',
            'students.*.notes' => 'nullable|string',
        ]);

        foreach ($validated['students'] as $studentData) {
            Attendance::updateOrCreate(
                [
                    'group_id' => $group->id,
                    'student_id' => $studentData['student_id'],
                    'session_date' => $validated['session_date'],
                ],
                [
                    'status' => $studentData['status'],
                    'notes' => $studentData['notes'] ?? null,
                    'marked_at' => now(),
                ]
            );
        }

        return response()->json([
            'message' => 'تم تسجيل الحضور بنجاح',
        ]);
    }

    /**
     * Get attendance for a specific date
     */
    public function getByDate(Request $request, $groupId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);

        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $attendance = $group->attendances()
            ->with('student.user')
            ->where('session_date', $validated['date'])
            ->get();

        // Get students not yet marked
        $markedStudentIds = $attendance->pluck('student_id');
        $unmarkedStudents = $group->students()
            ->whereNotIn('students.id', $markedStudentIds)
            ->with('user')
            ->get();

        return response()->json([
            'attendance' => $attendance,
            'unmarked_students' => $unmarkedStudents,
        ]);
    }

    /**
     * Get attendance statistics
     */
    public function getStats(Request $request, $groupId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);

        $students = $group->students()->with('user')->get();

        $stats = $students->map(function ($student) use ($group) {
            $total = Attendance::where('group_id', $group->id)
                ->where('student_id', $student->id)
                ->count();

            $present = Attendance::where('group_id', $group->id)
                ->where('student_id', $student->id)
                ->where('status', 'present')
                ->count();

            $absent = Attendance::where('group_id', $group->id)
                ->where('student_id', $student->id)
                ->where('status', 'absent')
                ->count();

            $late = Attendance::where('group_id', $group->id)
                ->where('student_id', $student->id)
                ->where('status', 'late')
                ->count();

            return [
                'student' => $student,
                'total_sessions' => $total,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'attendance_rate' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
            ];
        });

        return response()->json([
            'stats' => $stats,
        ]);
    }
}
