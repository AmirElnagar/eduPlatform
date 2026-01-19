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

class StudentContentController extends Controller
{
    /**
     * Get group contents
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

        $group = Group::findOrFail($groupId);

        $contents = $group->publishedContents()
            ->orderBy('order_index')
            ->get()
            ->map(function ($content) use ($student) {
                return [
                    'id' => $content->id,
                    'title' => $content->title,
                    'description' => $content->description,
                    'type' => $content->type,
                    'duration_minutes' => $content->duration_minutes,
                    'thumbnail' => $content->thumbnail,
                    'is_free' => $content->is_free,
                    'can_access' => $content->canBeAccessedBy($student->id),
                    'views_count' => $content->views_count,
                ];
            });

        return response()->json([
            'contents' => $contents,
        ]);
    }

    /**
     * View content
     */
    public function view(Request $request, $groupId, $contentId)
    {
        $student = $request->user()->student;
        $content = Content::where('group_id', $groupId)->findOrFail($contentId);

        // Check access
        if (!$content->canBeAccessedBy($student->id)) {
            return response()->json([
                'message' => 'ليس لديك صلاحية الوصول لهذا المحتوى',
            ], 403);
        }

        // Increment views
        $content->incrementViews();

        return response()->json([
            'content' => $content,
        ]);
    }
}
