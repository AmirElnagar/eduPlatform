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

class StudentBrowseController extends Controller
{
    /**
     * Browse teachers
     */
    public function browseTeachers(Request $request)
    {
        $validated = $request->validate([
            'grade_id' => 'nullable|exists:grades,id',
            'specialization' => 'nullable|string',
            'min_rating' => 'nullable|numeric|min:0|max:5',
        ]);

        $query = Teacher::with(['user', 'grades'])
            ->verified()
            ->withCount('groups');

        if (isset($validated['grade_id'])) {
            $query->whereHas('grades', function ($q) use ($validated) {
                $q->where('grades.id', $validated['grade_id']);
            });
        }

        if (isset($validated['specialization'])) {
            $query->bySpecialization($validated['specialization']);
        }

        if (isset($validated['min_rating'])) {
            $query->where('rating', '>=', $validated['min_rating']);
        }

        $teachers = $query->orderBy('rating', 'desc')
            ->paginate(20);

        return response()->json($teachers);
    }

    /**
     * Get teacher profile
     */
    public function getTeacherProfile(Request $request, $teacherId)
    {
        $teacher = Teacher::with(['user', 'grades', 'approvedReviews.student.user'])
            ->verified()
            ->findOrFail($teacherId);

        $activeGroups = $teacher->activeGroups()
            ->with('grade')
            ->available()
            ->get();

        return response()->json([
            'teacher' => $teacher,
            'groups' => $activeGroups,
        ]);
    }

    /**
     * Browse groups
     */
    public function browseGroups(Request $request)
    {
        $student = $request->user()->student;

        $validated = $request->validate([
            'grade_id' => 'nullable|exists:grades,id',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $query = Group::with(['teacher.user', 'grade'])
            ->active()
            ->available();

        // Filter by student's grade by default
        if (!isset($validated['grade_id']) && $student->grade_id) {
            $query->where('grade_id', $student->grade_id);
        } elseif (isset($validated['grade_id'])) {
            $query->where('grade_id', $validated['grade_id']);
        }

        if (isset($validated['teacher_id'])) {
            $query->where('teacher_id', $validated['teacher_id']);
        }

        $groups = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($groups);
    }

    /**
     * Get group details
     */
    public function getGroupDetails(Request $request, $groupId)
    {
        $student = $request->user()->student;

        $group = Group::with(['teacher.user', 'grade'])
            ->withCount('contents', 'exams')
            ->findOrFail($groupId);

        $isEnrolled = $student->isEnrolledIn($groupId);

        return response()->json([
            'group' => $group,
            'is_enrolled' => $isEnrolled,
        ]);
    }
}
