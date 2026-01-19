<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherContentController extends Controller
{
    /**
     * Get group contents
     */
    public function index(Request $request, $groupId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);

        $contents = $group->contents()
            ->with('accessControl')
            ->orderBy('order_index')
            ->get();

        return response()->json([
            'contents' => $contents,
        ]);
    }

    /**
     * Upload new content
     */
    public function store(Request $request, $groupId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:video,document,link,live',
            'file' => 'required_unless:type,link|file|max:524288', // 500MB max
            'link_url' => 'required_if:type,link|url',
            'thumbnail' => 'nullable|image|max:2048',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_free' => 'sometimes|boolean',
            'is_published' => 'sometimes|boolean',
            'order_index' => 'nullable|integer|min:0',
        ]);

        // Handle file upload
        if ($request->hasFile('file') && $validated['type'] !== 'link') {
            $file = $request->file('file');
            $path = $file->store("groups/{$groupId}/contents", 'public');

            $validated['file_path'] = $path;
            $validated['file_size'] = $file->getSize();
        } elseif ($validated['type'] === 'link') {
            $validated['file_path'] = $validated['link_url'];
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailPath = $thumbnail->store("groups/{$groupId}/thumbnails", 'public');
            $validated['thumbnail'] = $thumbnailPath;
        }

        // Set order index if not provided
        if (!isset($validated['order_index'])) {
            $maxOrder = $group->contents()->max('order_index') ?? -1;
            $validated['order_index'] = $maxOrder + 1;
        }

        $content = $group->contents()->create($validated);

        return response()->json([
            'message' => 'تم رفع المحتوى بنجاح',
            'content' => $content,
        ], 201);
    }

    /**
     * Get single content
     */
    public function show(Request $request, $groupId, $contentId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);

        $content = $group->contents()
            ->with('accessControl')
            ->findOrFail($contentId);

        return response()->json([
            'content' => $content,
        ]);
    }

    /**
     * Update content
     */
    public function update(Request $request, $groupId, $contentId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);
        $content = $group->contents()->findOrFail($contentId);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'thumbnail' => 'nullable|image|max:2048',
            'is_free' => 'sometimes|boolean',
            'is_published' => 'sometimes|boolean',
            'order_index' => 'nullable|integer|min:0',
        ]);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($content->thumbnail) {
                Storage::disk('public')->delete($content->thumbnail);
            }

            $thumbnail = $request->file('thumbnail');
            $thumbnailPath = $thumbnail->store("groups/{$groupId}/thumbnails", 'public');
            $validated['thumbnail'] = $thumbnailPath;
        }

        $content->update($validated);

        return response()->json([
            'message' => 'تم تحديث المحتوى بنجاح',
            'content' => $content->fresh(),
        ]);
    }

    /**
     * Delete content
     */
    public function destroy(Request $request, $groupId, $contentId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);
        $content = $group->contents()->findOrFail($contentId);

        // Delete files
        if ($content->file_path && $content->type !== 'link') {
            Storage::disk('public')->delete($content->file_path);
        }

        if ($content->thumbnail) {
            Storage::disk('public')->delete($content->thumbnail);
        }

        $content->delete();

        return response()->json([
            'message' => 'تم حذف المحتوى بنجاح',
        ]);
    }

    /**
     * Publish/Unpublish content
     */
    public function togglePublish(Request $request, $groupId, $contentId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);
        $content = $group->contents()->findOrFail($contentId);

        $content->is_published = !$content->is_published;

        if ($content->is_published) {
            $content->published_at = now();
        }

        $content->save();

        return response()->json([
            'message' => $content->is_published ? 'تم نشر المحتوى' : 'تم إلغاء نشر المحتوى',
            'content' => $content,
        ]);
    }

    /**
     * Reorder contents
     */
    public function reorder(Request $request, $groupId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);

        $validated = $request->validate([
            'contents' => 'required|array',
            'contents.*.id' => 'required|exists:contents,id',
            'contents.*.order_index' => 'required|integer|min:0',
        ]);

        foreach ($validated['contents'] as $item) {
            $group->contents()
                ->where('id', $item['id'])
                ->update(['order_index' => $item['order_index']]);
        }

        return response()->json([
            'message' => 'تم إعادة ترتيب المحتوى بنجاح',
        ]);
    }

    /**
     * Set content access control
     */
    public function setAccessControl(Request $request, $groupId, $contentId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);
        $content = $group->contents()->findOrFail($contentId);

        $validated = $request->validate([
            'access_type' => 'required|in:free,enrollment,after_exam,after_attendance',
            'required_exam_id' => 'nullable|required_if:access_type,after_exam|exists:exams,id',
            'required_attendance_count' => 'nullable|required_if:access_type,after_attendance|integer|min:1',
        ]);

        $content->accessControl()->updateOrCreate(
            ['content_id' => $content->id],
            $validated
        );

        return response()->json([
            'message' => 'تم تحديث إعدادات الوصول للمحتوى',
            'content' => $content->fresh()->load('accessControl'),
        ]);
    }
}
