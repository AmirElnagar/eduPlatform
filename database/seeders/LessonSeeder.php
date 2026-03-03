<?php

namespace Database\Seeders;

use App\Enums\LessonType;
use App\Enums\Subject;
use App\Models\Group;
use App\Models\Lesson;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        // المفاتيح هي قيم الـ Enum (string values)
        $lessonTitles = [
            Subject::MATH->value => [
                'مقدمة في حساب المثلثات',
                'المعادلات التربيعية',
                'الهندسة التحليلية',
                'التفاضل والتكامل - الأساسيات',
                'نظرية الحدين',
                'الإحصاء والاحتمالات',
            ],
            Subject::ARABIC->value => [
                'النحو - الجملة الاسمية',
                'البلاغة - علم البيان',
                'الأدب الجاهلي',
                'الصرف - الأفعال',
                'التعبير الكتابي',
                'الأدب الحديث',
            ],
            Subject::PHYSICS->value => [
                'قوانين نيوتن',
                'الكهرباء الساكنة',
                'الموجات والصوت',
                'الضوء والبصريات',
                'الفيزياء النووية',
                'الحركة الدورانية',
            ],
            Subject::CHEMISTRY->value => [
                'الجدول الدوري والروابط',
                'الكيمياء العضوية - الهيدروكربونات',
                'التفاعلات الكيميائية',
                'الكيمياء الحرارية',
                'الكيمياء الكهربائية',
                'المحاليل والتركيز',
            ],
            Subject::ENGLISH->value => [
                'Tenses Review',
                'Conditionals & Modals',
                'Essay Writing',
                'Reading Comprehension Skills',
                'Vocabulary Building',
                'Speaking Practice',
            ],
            Subject::BIOLOGY->value => [
                'الخلية وتركيبها',
                'الوراثة والجينات',
                'التمثيل الضوئي والتنفس',
                'الجهاز العصبي',
                'الجهاز المناعي',
                'التطور والانتخاب الطبيعي',
            ],
            Subject::HISTORY->value => [
                'الحضارة المصرية القديمة',
                'الفتوحات الإسلامية',
                'العالم في القرن العشرين',
                'الحرب العالمية الثانية وتداعياتها',
                'الثورات الحديثة',
            ],
            Subject::GEOGRAPHY->value => [
                'الجغرافيا الطبيعية لمصر',
                'الجغرافيا البشرية',
                'المناخ والمناطق المناخية',
                'التضاريس والمسطحات المائية',
            ],
            Subject::COMPUTER->value => [
                'أساسيات البرمجة بـ Python',
                'البرمجة الكائنية',
                'قواعد البيانات SQL',
                'الشبكات والإنترنت',
                'خوارزميات الفرز والبحث',
            ],
            Subject::SCIENCE->value => [
                'المادة وخصائصها',
                'الكائنات الحية',
                'الفيزياء العامة',
                'الكيمياء الأساسية',
            ],
        ];

        $types = [
            LessonType::OFFLINE,
            LessonType::RECORDED,
            LessonType::LIVE,
        ];

        foreach (Group::with('teacher')->get() as $group) {
            // subject هو Enum object، نجيب الـ value منه
            $subjectValue = $group->subject instanceof Subject
                ? $group->subject->value
                : (string) $group->subject;

            $titles = $lessonTitles[$subjectValue]
                ?? ['الدرس الأول', 'الدرس الثاني', 'الدرس الثالث', 'الدرس الرابع'];

            foreach ($titles as $order => $title) {
                $type   = $types[rand(0, 2)];
                $isPast = rand(0, 1);

                $scheduled = $isPast
                    ? now()->subDays(rand(1, 45))->setHour(rand(14, 20))
                    : now()->addDays(rand(1, 30))->setHour(rand(14, 20));

                $lesson = Lesson::create([
                    'group_id'         => $group->id,
                    'title'            => $title,
                    'description'      => 'شرح تفصيلي لـ ' . $title . ' مع أمثلة تطبيقية.',
                    'type'             => $type,
                    'video_path'       => $type === LessonType::RECORDED
                        ? 'lessons/video_' . rand(100, 999) . '.mp4'
                        : null,
                    'duration_minutes' => $type === LessonType::RECORDED
                        ? rand(30, 90)
                        : rand(45, 120),
                    'meeting_url'      => $type === LessonType::LIVE
                        ? 'https://zoom.us/j/' . rand(10000000000, 99999999999)
                        : null,
                    'scheduled_at'     => $scheduled,
                    'started_at'       => ($type !== LessonType::OFFLINE && $isPast) ? $scheduled : null,
                    'ended_at'         => ($type !== LessonType::OFFLINE && $isPast)
                        ? (clone $scheduled)->modify('+90 minutes')
                        : null,
                    'is_published'     => true,
                    'is_free'          => $order === 0,
                    'order'            => $order + 1,
                ]);

                // تسجيل حضور للدروس الأوفلاين الماضية
                if ($isPast && $type === LessonType::OFFLINE) {
                    $enrolledStudents = DB::table('group_student')
                        ->where('group_id', $group->id)
                        ->where('status', 'active')
                        ->pluck('student_id');

                    foreach ($enrolledStudents as $studentId) {
                        $statuses     = ['present', 'present', 'present', 'absent', 'late'];
                        $attendStatus = $statuses[rand(0, 4)];

                        DB::table('lesson_attendance')->insertOrIgnore([
                            'lesson_id'  => $lesson->id,
                            'student_id' => $studentId,
                            'status'     => $attendStatus,
                            'notes'      => $attendStatus === 'absent' ? 'لم يحضر بدون إشعار' : null,
                            'marked_at'  => $scheduled,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // تسجيل مشاهدة للدروس المسجلة الماضية
                if ($type === LessonType::RECORDED && $isPast) {
                    $enrolledStudents = DB::table('group_student')
                        ->where('group_id', $group->id)
                        ->where('status', 'active')
                        ->pluck('student_id');

                    foreach ($enrolledStudents as $studentId) {
                        $duration = ($lesson->duration_minutes ?? 60) * 60;
                        $watched  = rand(0, 1);
                        $position = $watched ? $duration : rand(0, (int) ($duration * 0.6));

                        DB::table('lesson_views')->insertOrIgnore([
                            'lesson_id'      => $lesson->id,
                            'student_id'     => $studentId,
                            'watch_duration' => $position,
                            'last_position'  => $position,
                            'completed'      => $watched && $position >= $duration * 0.9,
                            'last_watched_at' => $watched ? now()->subDays(rand(1, 10)) : null,
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ]);
                    }
                }
            }
        }
    }
}
