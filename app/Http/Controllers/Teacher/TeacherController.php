<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Enums\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $subjectFilter = $request->query('subject');

        $query = Teacher::with('user')
            ->whereHas('user', fn($q) => $q->where('is_active', true));

        if ($subjectFilter) {
            $query->where('subject', $subjectFilter);
        }

        $teachers = $query->get();

        $teachersBySubject = $teachers->groupBy(function ($teacher) {
            $subject = $teacher->subject;
            if ($subject instanceof Subject) {
                return $subject->label();
            }
            try {
                return Subject::from($subject)->label();
            } catch (\ValueError $e) {
                return $subject;
            }
        });

        $subjects = collect(Subject::cases())->mapWithKeys(
            fn(Subject $s) => [$s->value => $s->label()]
        );

        return view('teachers.index', compact('teachersBySubject', 'subjects', 'subjectFilter'));
    }

    public function show(Teacher $teacher)
    {
        $teacher->load('user');

        $teacher->loadCount(['reviews' => fn($q) => $q->where('is_approved', true)]);
        $teacher->loadAvg(['reviews' => fn($q) => $q->where('is_approved', true)], 'rating');

        $relatedTeachers = Teacher::with('user')
            ->where('subject', $teacher->getRawOriginal('subject'))
            ->where('id', '!=', $teacher->id)
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->take(3)
            ->get();

        $reviews = $teacher->reviews()
            ->where('is_approved', true)
            ->with('student.user')
            ->latest()
            ->take(5)
            ->get();

        return view('teachers.show', compact('teacher', 'relatedTeachers', 'reviews'));
    }
}
