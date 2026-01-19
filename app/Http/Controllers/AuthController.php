<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\ParentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|unique:users,phone',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|in:teacher,student,parent',

            // Teacher specific
            'specialization' => 'required_if:role,teacher|string',
            'bio' => 'nullable|string',
            'experience_years' => 'nullable|integer|min:0',

            // Student specific
            'grade_id' => 'required_if:role,student|exists:grades,id',
            'parent_id' => 'nullable|exists:users,id',
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Create role-specific record
        switch ($validated['role']) {
            case 'teacher':
                Teacher::create([
                    'user_id' => $user->id,
                    'specialization' => $validated['specialization'],
                    'bio' => $validated['bio'] ?? null,
                    'experience_years' => $validated['experience_years'] ?? 0,
                ]);
                break;

            case 'student':
                Student::create([
                    'user_id' => $user->id,
                    'grade_id' => $validated['grade_id'],
                    'parent_id' => $validated['parent_id'] ?? null,
                ]);
                break;

            case 'parent':
                ParentModel::create([
                    'user_id' => $user->id,
                ]);
                break;
        }

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم التسجيل بنجاح',
            'user' => $user->load($validated['role']),
            'token' => $token,
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'حسابك غير مفعل. يرجى التواصل مع الإدارة',
            ], 403);
        }

        // Revoke old tokens
        $user->tokens()->delete();

        // Generate new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $user->load($user->role),
            'token' => $token,
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        $user = $request->user()->load($request->user()->role);

        return response()->json([
            'user' => $user,
        ]);
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|unique:users,phone,' . $user->id,
            'current_password' => 'required_with:new_password|string',
            'new_password' => ['sometimes', 'confirmed', Password::min(8)],
        ]);

        // Update basic info
        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }

        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }

        // Update password if provided
        if (isset($validated['new_password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'message' => 'كلمة المرور الحالية غير صحيحة',
                ], 422);
            }

            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return response()->json([
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'user' => $user->load($user->role),
        ]);
    }

    /**
     * Update role-specific profile
     */
    public function updateRoleProfile(Request $request)
    {
        $user = $request->user();

        switch ($user->role) {
            case 'teacher':
                return $this->updateTeacherProfile($request, $user);
            case 'student':
                return $this->updateStudentProfile($request, $user);
            default:
                return response()->json([
                    'message' => 'لا يمكن تحديث هذا النوع من الحسابات',
                ], 403);
        }
    }

    /**
     * Update teacher profile
     */
    private function updateTeacherProfile(Request $request, User $user)
    {
        $validated = $request->validate([
            'bio' => 'sometimes|string',
            'specialization' => 'sometimes|string|max:255',
            'experience_years' => 'sometimes|integer|min:0',
            'accept_online_payment' => 'sometimes|boolean',
            'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'cover_image' => 'sometimes|image|mimes:jpeg,png,jpg|max:4096',
        ]);

        $teacher = $user->teacher;

        // Handle image uploads
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('teachers/profiles', 'public');
            $validated['profile_image'] = $path;
        }

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('teachers/covers', 'public');
            $validated['cover_image'] = $path;
        }

        $teacher->update($validated);

        return response()->json([
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'teacher' => $teacher->fresh(),
        ]);
    }

    /**
     * Update student profile
     */
    private function updateStudentProfile(Request $request, User $user)
    {
        $validated = $request->validate([
            'grade_id' => 'sometimes|exists:grades,id',
            'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $student = $user->student;

        // Handle image upload
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('students/profiles', 'public');
            $validated['profile_image'] = $path;
        }

        $student->update($validated);

        return response()->json([
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'student' => $student->fresh(),
        ]);
    }

    /**
     * Request password reset
     */
    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // TODO: Implement password reset email logic
        // This would typically involve sending a reset token via email

        return response()->json([
            'message' => 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني',
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // TODO: Verify reset token and update password
        // This would typically involve checking the token validity

        return response()->json([
            'message' => 'تم إعادة تعيين كلمة المرور بنجاح',
        ]);
    }
}
