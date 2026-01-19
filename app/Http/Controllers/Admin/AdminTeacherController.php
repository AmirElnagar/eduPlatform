<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;

class AdminTeacherController extends Controller
{
    /**
     * Get all teachers
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'verified' => 'nullable|boolean',
            'search' => 'nullable|string',
        ]);

        $query = Teacher::with('user');

        if (isset($validated['verified'])) {
            $query->where('is_verified', $validated['verified']);
        }

        if (isset($validated['search'])) {
            $query->whereHas('user', function ($q) use ($validated) {
                $q->where('name', 'like', "%{$validated['search']}%")
                    ->orWhere('email', 'like', "%{$validated['search']}%");
            });
        }

        $teachers = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json($teachers);
    }

    /**
     * Verify teacher
     */
    public function verify(Request $request, $teacherId)
    {
        $teacher = Teacher::findOrFail($teacherId);
        $teacher->update(['is_verified' => true]);

        // Send notification to teacher
        \App\Models\Notification::send(
            $teacher->user_id,
            'تم التحقق من حسابك',
            'تم التحقق من حسابك كمدرس. يمكنك الآن البدء في إنشاء المجموعات ورفع المحتوى.',
            'success'
        );

        return response()->json([
            'message' => 'تم التحقق من المدرس',
            'teacher' => $teacher,
        ]);
    }

    /**
     * Unverify teacher
     */
    public function unverify(Request $request, $teacherId)
    {
        $teacher = Teacher::findOrFail($teacherId);
        $teacher->update(['is_verified' => false]);

        return response()->json([
            'message' => 'تم إلغاء التحقق من المدرس',
            'teacher' => $teacher,
        ]);
    }
}
