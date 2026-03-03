<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Group::all()->take(4) as $group) {
            $exam = Exam::create([
                'group_id'                 => $group->id,
                'title'                    => 'اختبار ' . $group->subject . ' - ' . $group->name,
                'description'              => 'اختبار شامل لقياس مستوى الطالب في ' . $group->subject,
                'instructions'             => 'اقرأ الأسئلة بتمعن. الإجابة على جميع الأسئلة إلزامية. وقت الاختبار ' . '60 دقيقة.',
                'duration_minutes'         => 60,
                'total_marks'              => 50,
                'passing_marks'            => 25,
                'starts_at'                => now()->subDays(rand(5, 15)),
                'ends_at'                  => now()->addDays(rand(5, 10)),
                'shuffle_questions'        => rand(0, 1),
                'show_results_immediately' => true,
                'allow_retake'             => true,
                'max_attempts'             => 2,
                'is_published'             => true,
            ]);

            // أضف 5 أسئلة MCQ
            $mcqQuestions = [
                ['question' => 'أي من التالي صحيح؟', 'options' => ['الخيار الأول', 'الخيار الثاني', 'الخيار الثالث', 'الخيار الرابع'], 'answer' => 'A'],
                ['question' => 'ما هو الناتج الصحيح؟',  'options' => ['النتيجة أ', 'النتيجة ب', 'النتيجة ج', 'النتيجة د'], 'answer' => 'C'],
                ['question' => 'اختر الإجابة الصحيحة:', 'options' => ['القيمة 1', 'القيمة 2', 'القيمة 3', 'القيمة 4'], 'answer' => 'B'],
                ['question' => 'من هو صاحب هذه النظرية؟', 'options' => ['العالم الأول', 'العالم الثاني', 'العالم الثالث', 'العالم الرابع'], 'answer' => 'D'],
                ['question' => 'ما المقصود بهذا المفهوم؟', 'options' => ['التعريف أ', 'التعريف ب', 'التعريف ج', 'التعريف د'], 'answer' => 'A'],
            ];

            foreach ($mcqQuestions as $i => $q) {
                ExamQuestion::create([
                    'exam_id'        => $exam->id,
                    'type'           => 'mcq',
                    'question'       => $q['question'] . ' (' . $group->subject . ' - سؤال ' . ($i + 1) . ')',
                    'options'        => json_encode(['A' => $q['options'][0], 'B' => $q['options'][1], 'C' => $q['options'][2], 'D' => $q['options'][3]]),
                    'correct_answer' => $q['answer'],
                    'marks'          => 6,
                    'order'          => $i + 1,
                ]);
            }

            // أضف سؤال مقالي واحد
            ExamQuestion::create([
                'exam_id'  => $exam->id,
                'type'     => 'essay',
                'question' => 'اكتب تعريفاً شاملاً لأحد المفاهيم الأساسية التي تعلمتها في مادة ' . $group->subject . ' مع ذكر مثال تطبيقي.',
                'marks'    => 20,
                'order'    => 6,
            ]);

            // سجل محاولات لبعض الطلاب
            $enrolledStudents = DB::table('group_student')
                ->where('group_id', $group->id)
                ->where('status', 'active')
                ->pluck('student_id')
                ->take(3);

            foreach ($enrolledStudents as $studentId) {
                $score      = rand(20, 50);
                $percentage = round(($score / 50) * 100, 2);

                $attempt = DB::table('exam_attempts')->insertGetId([
                    'exam_id'           => $exam->id,
                    'student_id'        => $studentId,
                    'attempt_number'    => 1,
                    'score'             => $score,
                    'percentage'        => $percentage,
                    'passed'            => $score >= 25,
                    'started_at'        => now()->subDays(rand(1, 5)),
                    'submitted_at'      => now()->subDays(rand(1, 5))->addMinutes(rand(30, 60)),
                    'time_taken_minutes' => rand(30, 60),
                    'status'            => 'graded',
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                // سجل إجابات MCQ
                $questions = ExamQuestion::where('exam_id', $exam->id)->where('type', 'mcq')->get();
                foreach ($questions as $question) {
                    $options     = ['A', 'B', 'C', 'D'];
                    $givenAnswer = $options[rand(0, 3)];
                    DB::table('exam_answers')->insert([
                        'attempt_id'   => $attempt,
                        'question_id'  => $question->id,
                        'answer'       => $givenAnswer,
                        'is_correct'   => $givenAnswer === $question->correct_answer,
                        'marks_obtained' => $givenAnswer === $question->correct_answer ? $question->marks : 0,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }
        }
    }
}
