<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $comments = [
            5 => [
                'مدرس ممتاز، شرحه واضح جداً وأسلوبه مشوق. استفدت كثيراً من الدروس.',
                'أفضل مدرس تعاملت معه. يهتم بكل طالب ويتابع التقدم باستمرار.',
                'شرح رائع ومنهجية ممتازة. أنصح كل طالب بالتسجيل في مجموعاته.',
            ],
            4 => [
                'مدرس جيد جداً، المادة تحسنت معه بشكل ملحوظ. الشرح واضح.',
                'مجموعة منظمة ومدرس متعاون. أتمنى لو كان عنده وقت أكثر للأسئلة.',
            ],
            3 => [
                'الشرح معقول ولكن أتمنى أكثر تنوعاً في التمارين.',
                'المدرس كفء لكن المجموعة كبيرة شوية.',
            ],
        ];

        $teachers = Teacher::all();
        $students = DB::table('students')->pluck('id')->toArray();

        foreach ($teachers as $teacher) {
            $usedStudents = [];
            $numReviews   = rand(3, 6);

            for ($i = 0; $i < $numReviews; $i++) {
                $available = array_diff($students, $usedStudents);
                if (empty($available)) break;

                $studentId      = $available[array_rand($available)];
                $usedStudents[] = $studentId;

                $rating         = [5, 5, 5, 4, 4, 3][rand(0, 5)];
                $ratingComments = $comments[$rating];

                DB::table('reviews')->insertOrIgnore([
                    'teacher_id'  => $teacher->id,
                    'student_id'  => $studentId,
                    'rating'      => $rating,
                    'comment'     => $ratingComments[rand(0, count($ratingComments) - 1)],
                    'is_approved' => rand(0, 4) > 0, // 80% مقبولة
                    'created_at'  => now()->subDays(rand(1, 90)),
                    'updated_at'  => now(),
                ]);
            }
        }
    }
}
