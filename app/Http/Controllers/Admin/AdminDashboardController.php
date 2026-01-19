<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStats(Request $request)
    {
        $stats = [
            'total_users' => User::count(),
            'total_teachers' => Teacher::count(),
            'verified_teachers' => Teacher::verified()->count(),
            'total_students' => Student::count(),
            'total_groups' => Group::count(),
            'active_groups' => Group::active()->count(),
            'total_revenue' => Payment::completed()->sum('amount'),
            'pending_reviews' => Review::where('is_approved', false)->count(),
        ];

        // Recent activity
        $recentUsers = User::orderBy('created_at', 'desc')->limit(10)->get();
        $recentEnrollments = \App\Models\Enrollment::with(['student.user', 'group'])
            ->orderBy('enrolled_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent_users' => $recentUsers,
            'recent_enrollments' => $recentEnrollments,
        ]);
    }

    /**
     * Get all users
     */
    public function getUsers(Request $request)
    {
        $validated = $request->validate([
            'role' => 'nullable|in:teacher,student,parent',
            'search' => 'nullable|string',
        ]);

        $query = User::query();

        if (isset($validated['role'])) {
            $query->where('role', $validated['role']);
        }

        if (isset($validated['search'])) {
            $query->where(function ($q) use ($validated) {
                $q->where('name', 'like', "%{$validated['search']}%")
                    ->orWhere('email', 'like', "%{$validated['search']}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json($users);
    }

    /**
     * Toggle user active status
     */
    public function toggleUserStatus(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        if ($user->role === 'admin') {
            return response()->json([
                'message' => 'لا يمكن تعطيل حساب مسؤول',
            ], 403);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'message' => $user->is_active ? 'تم تفعيل الحساب' : 'تم تعطيل الحساب',
            'user' => $user,
        ]);
    }

    /**
     * Delete user
     */
    public function deleteUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        if ($user->role === 'admin') {
            return response()->json([
                'message' => 'لا يمكن حذف حساب مسؤول',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'تم حذف المستخدم بنجاح',
        ]);
    }
}
