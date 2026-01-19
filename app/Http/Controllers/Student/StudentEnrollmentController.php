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

class StudentEnrollmentController extends Controller
{
    /**
     * Get student's enrollments
     */
    public function index(Request $request)
    {
        $student = $request->user()->student;

        $enrollments = $student->enrollments()
            ->with(['group.teacher.user', 'group.grade'])
            ->orderBy('enrolled_at', 'desc')
            ->get();

        return response()->json([
            'enrollments' => $enrollments,
        ]);
    }

    /**
     * Enroll in a group
     */
    public function enroll(Request $request, $groupId)
    {
        $student = $request->user()->student;
        $group = Group::active()->available()->findOrFail($groupId);

        // Check if already enrolled
        if ($student->isEnrolledIn($groupId)) {
            return response()->json([
                'message' => 'أنت مسجل بالفعل في هذه المجموعة',
            ], 422);
        }

        // Check if group is full
        if ($group->isFull()) {
            return response()->json([
                'message' => 'المجموعة ممتلئة',
            ], 422);
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:online,offline,free',
        ]);

        // Create enrollment
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'payment_status' => $validated['payment_method'] === 'free' ? 'paid' : 'unpaid',
            'amount_paid' => $validated['payment_method'] === 'free' ? 0 : $group->price,
        ]);

        // If free or offline payment, activate immediately
        if (in_array($validated['payment_method'], ['free', 'offline'])) {
            $enrollment->activate();
            $group->incrementStudents();

            return response()->json([
                'message' => 'تم التسجيل في المجموعة بنجاح',
                'enrollment' => $enrollment->fresh(),
            ], 201);
        }

        // For online payment, return payment URL
        return response()->json([
            'message' => 'يرجى إتمام عملية الدفع',
            'enrollment' => $enrollment,
            'payment_required' => true,
            // TODO: Add payment gateway integration
        ], 201);
    }

    /**
     * Cancel enrollment
     */
    public function cancel(Request $request, $enrollmentId)
    {
        $student = $request->user()->student;
        $enrollment = $student->enrollments()->findOrFail($enrollmentId);

        if ($enrollment->status !== 'active') {
            return response()->json([
                'message' => 'لا يمكن إلغاء هذا الاشتراك',
            ], 422);
        }

        $enrollment->cancel();

        return response()->json([
            'message' => 'تم إلغاء الاشتراك بنجاح',
        ]);
    }
}
