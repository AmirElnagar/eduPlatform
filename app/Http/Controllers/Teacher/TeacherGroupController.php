<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;

class TeacherGroupController extends Controller
{
    /**
     * Get teacher's groups
     */
    public function index(Request $request)
    {
        $teacher = $request->user()->teacher;

        $groups = $teacher->groups()
            ->with(['grade', 'enrollments'])
            ->withCount('contents', 'exams')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'groups' => $groups,
        ]);
    }

    /**
     * Get available grades for teacher
     */
    public function getAvailableGrades(Request $request)
    {
        $grades = Grade::active()->orderBy('order_index')->get();

        return response()->json([
            'grades' => $grades,
        ]);
    }

    /**
     * Create a new group
     */
    public function store(Request $request)
    {
        $teacher = $request->user()->teacher;

        $validated = $request->validate([
            'grade_id' => 'required|exists:grades,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_students' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'subscription_duration_days' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        $group = $teacher->groups()->create($validated);

        return response()->json([
            'message' => 'تم إنشاء المجموعة بنجاح',
            'group' => $group->load('grade'),
        ], 201);
    }

    /**
     * Get single group details
     */
    public function show(Request $request, $groupId)
    {
        $teacher = $request->user()->teacher;

        $group = $teacher->groups()
            ->with(['grade', 'contents', 'exams', 'students'])
            ->withCount('enrollments')
            ->findOrFail($groupId);

        return response()->json([
            'group' => $group,
        ]);
    }

    /**
     * Update group
     */
    public function update(Request $request, $groupId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'max_students' => 'sometimes|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'subscription_duration_days' => 'nullable|integer|min:1',
            'is_active' => 'sometimes|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        $group->update($validated);

        return response()->json([
            'message' => 'تم تحديث المجموعة بنجاح',
            'group' => $group->fresh()->load('grade'),
        ]);
    }

    /**
     * Delete group
     */
    public function destroy(Request $request, $groupId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);

        // Check if group has active enrollments
        if ($group->activeEnrollments()->count() > 0) {
            return response()->json([
                'message' => 'لا يمكن حذف مجموعة بها طلاب مسجلين حاليًا',
            ], 422);
        }

        $group->delete();

        return response()->json([
            'message' => 'تم حذف المجموعة بنجاح',
        ]);
    }

    /**
     * Get group students
     */
    public function getStudents(Request $request, $groupId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);

        $students = $group->students()
            ->with(['user', 'grade'])
            ->withPivot(['status', 'enrolled_at', 'expires_at'])
            ->get();

        return response()->json([
            'students' => $students,
        ]);
    }
}
