<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeacherExamController extends Controller
{
    /**
     * Get group exams
     */
    public function index(Request $request, $groupId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);

        $exams = $group->exams()
            ->withCount(['questions', 'attempts'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'exams' => $exams,
        ]);
    }

    /**
     * Create new exam
     */
    public function store(Request $request, $groupId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_marks' => 'required|numeric|min:0',
            'pass_marks' => 'required|numeric|min:0|lte:total_marks',
            'duration_minutes' => 'required|integer|min:1',
            'type' => 'required|in:mcq,essay,mixed',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        $exam = $group->exams()->create($validated);

        return response()->json([
            'message' => 'تم إنشاء الامتحان بنجاح',
            'exam' => $exam,
        ], 201);
    }

    /**
     * Get exam details
     */
    public function show(Request $request, $groupId, $examId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);

        $exam = $group->exams()
            ->with(['questions.options'])
            ->withCount('attempts')
            ->findOrFail($examId);

        return response()->json([
            'exam' => $exam,
        ]);
    }

    /**
     * Update exam
     */
    public function update(Request $request, $groupId, $examId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);
        $exam = $group->exams()->findOrFail($examId);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'total_marks' => 'sometimes|numeric|min:0',
            'pass_marks' => 'sometimes|numeric|min:0',
            'duration_minutes' => 'sometimes|integer|min:1',
            'type' => 'sometimes|in:mcq,essay,mixed',
            'is_published' => 'sometimes|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        $exam->update($validated);

        return response()->json([
            'message' => 'تم تحديث الامتحان بنجاح',
            'exam' => $exam->fresh(),
        ]);
    }

    /**
     * Delete exam
     */
    public function destroy(Request $request, $groupId, $examId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);
        $exam = $group->exams()->findOrFail($examId);

        // Check if exam has attempts
        if ($exam->attempts()->count() > 0) {
            return response()->json([
                'message' => 'لا يمكن حذف امتحان قام طلاب بحله',
            ], 422);
        }

        $exam->delete();

        return response()->json([
            'message' => 'تم حذف الامتحان بنجاح',
        ]);
    }

    /**
     * Add question to exam
     */
    public function addQuestion(Request $request, $groupId, $examId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);
        $exam = $group->exams()->findOrFail($examId);

        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:mcq,essay',
            'marks' => 'required|numeric|min:0',
            'order_index' => 'nullable|integer|min:0',

            // For MCQ questions
            'options' => 'required_if:type,mcq|array|min:2',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'required|boolean',
        ]);

        // Set order index if not provided
        if (!isset($validated['order_index'])) {
            $maxOrder = $exam->questions()->max('order_index') ?? -1;
            $validated['order_index'] = $maxOrder + 1;
        }

        $question = $exam->questions()->create([
            'question_text' => $validated['question_text'],
            'type' => $validated['type'],
            'marks' => $validated['marks'],
            'order_index' => $validated['order_index'],
        ]);

        // Add options for MCQ
        if ($validated['type'] === 'mcq' && isset($validated['options'])) {
            foreach ($validated['options'] as $index => $option) {
                $question->options()->create([
                    'option_text' => $option['text'],
                    'is_correct' => $option['is_correct'],
                    'order_index' => $index,
                ]);
            }
        }

        return response()->json([
            'message' => 'تم إضافة السؤال بنجاح',
            'question' => $question->load('options'),
        ], 201);
    }

    /**
     * Update question
     */
    public function updateQuestion(Request $request, $groupId, $examId, $questionId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);
        $exam = $group->exams()->findOrFail($examId);
        $question = $exam->questions()->findOrFail($questionId);

        $validated = $request->validate([
            'question_text' => 'sometimes|string',
            'marks' => 'sometimes|numeric|min:0',
            'order_index' => 'nullable|integer|min:0',

            // For MCQ questions
            'options' => 'sometimes|array|min:2',
            'options.*.id' => 'nullable|exists:question_options,id',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'required|boolean',
        ]);

        $question->update([
            'question_text' => $validated['question_text'] ?? $question->question_text,
            'marks' => $validated['marks'] ?? $question->marks,
            'order_index' => $validated['order_index'] ?? $question->order_index,
        ]);

        // Update options for MCQ
        if (isset($validated['options']) && $question->type === 'mcq') {
            // Delete old options
            $question->options()->delete();

            // Create new options
            foreach ($validated['options'] as $index => $option) {
                $question->options()->create([
                    'option_text' => $option['text'],
                    'is_correct' => $option['is_correct'],
                    'order_index' => $index,
                ]);
            }
        }

        return response()->json([
            'message' => 'تم تحديث السؤال بنجاح',
            'question' => $question->fresh()->load('options'),
        ]);
    }

    /**
     * Delete question
     */
    public function deleteQuestion(Request $request, $groupId, $examId, $questionId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);
        $exam = $group->exams()->findOrFail($examId);
        $question = $exam->questions()->findOrFail($questionId);

        $question->delete();

        return response()->json([
            'message' => 'تم حذف السؤال بنجاح',
        ]);
    }

    /**
     * Get exam attempts/results
     */
    public function getAttempts(Request $request, $groupId, $examId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);
        $exam = $group->exams()->findOrFail($examId);

        $attempts = $exam->attempts()
            ->with(['student.user'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        return response()->json([
            'attempts' => $attempts,
        ]);
    }

    /**
     * Grade essay answers
     */
    public function gradeEssay(Request $request, $groupId, $examId, $attemptId)
    {
        $teacher = $request->user()->teacher;
        $group = $teacher->groups()->findOrFail($groupId);
        $exam = $group->exams()->findOrFail($examId);
        $attempt = $exam->attempts()->findOrFail($attemptId);

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.answer_id' => 'required|exists:student_answers,id',
            'answers.*.marks' => 'required|numeric|min:0',
        ]);

        foreach ($validated['answers'] as $answerData) {
            $answer = $attempt->answers()->findOrFail($answerData['answer_id']);
            $answer->update(['marks_obtained' => $answerData['marks']]);
        }

        // Calculate final score
        $attempt->calculateScore();

        return response()->json([
            'message' => 'تم تصحيح الإجابات بنجاح',
            'attempt' => $attempt->fresh(),
        ]);
    }
}
