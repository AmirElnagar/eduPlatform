<?php

namespace Database\Seeders;

use App\Enums\GradeLevel;
use App\Models\Group;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = Teacher::with('user')->get();

        // كل عنصر: [teacher_index, grade_level, name, description, max, price, schedule]
        $groupsData = [
            [
                'teacher_index' => 0, // رياضيات
                'grade_level'   => GradeLevel::SECONDARY_3,
                'name'          => 'مجموعة الثالث الثانوي A',
                'description'   => 'مجموعة مكثفة للمرحلة الثانوية، تغطي كامل المنهج مع مراجعات دورية.',
                'max_students'  => 25,
                'price'         => 300.00,
                'schedule'      => 'السبت والثلاثاء 5-7 مساءً',
            ],
            [
                'teacher_index' => 0, // رياضيات
                'grade_level'   => GradeLevel::SECONDARY_2,
                'name'          => 'مجموعة الثاني الثانوي B',
                'description'   => 'شرح المنهج مع تمارين يومية ومتابعة مستمرة.',
                'max_students'  => 20,
                'price'         => 250.00,
                'schedule'      => 'الاثنين والأربعاء 4-6 مساءً',
            ],
            [
                'teacher_index' => 1, // عربي
                'grade_level'   => GradeLevel::SECONDARY_3,
                'name'          => 'مجموعة عربي الثالث الثانوي',
                'description'   => 'شرح النحو والبلاغة والأدب بأسلوب مبسط.',
                'max_students'  => 30,
                'price'         => 200.00,
                'schedule'      => 'الأحد والثلاثاء 6-8 مساءً',
            ],
            [
                'teacher_index' => 2, // فيزياء
                'grade_level'   => GradeLevel::SECONDARY_3,
                'name'          => 'فيزياء الثالث الثانوي - الفرقة الأولى',
                'description'   => 'تأهيل كامل لامتحان الفيزياء مع حل جميع امتحانات السنوات السابقة.',
                'max_students'  => 20,
                'price'         => 280.00,
                'schedule'      => 'الجمعة 10 صباحاً - 12 ظهراً',
            ],
            [
                'teacher_index' => 3, // كيمياء
                'grade_level'   => GradeLevel::SECONDARY_2,
                'name'          => 'كيمياء الثاني الثانوي',
                'description'   => 'شرح الكيمياء العضوية وغير العضوية مع تجارب عملية.',
                'max_students'  => 25,
                'price'         => 260.00,
                'schedule'      => 'الاثنين والخميس 5-7 مساءً',
            ],
            [
                'teacher_index' => 4, // إنجليزي
                'grade_level'   => GradeLevel::SECONDARY_3,
                'name'          => 'English Advanced Group',
                'description'   => 'Advanced English course covering grammar, writing and speaking skills.',
                'max_students'  => 15,
                'price'         => 350.00,
                'schedule'      => 'الثلاثاء والخميس 7-9 مساءً',
            ],
            [
                'teacher_index' => 5, // أحياء
                'grade_level'   => GradeLevel::SECONDARY_3,
                'name'          => 'أحياء الثالث الثانوي',
                'description'   => 'المنهج كاملاً مع مراجعة نهائية وحل امتحانات.',
                'max_students'  => 25,
                'price'         => 220.00,
                'schedule'      => 'الأربعاء والسبت 4-6 مساءً',
            ],
            [
                'teacher_index' => 6, // رياضيات 2
                'grade_level'   => GradeLevel::SECONDARY_1,
                'name'          => 'رياضيات الأول الثانوي',
                'description'   => 'بناء أساس قوي في الجبر والهندسة للمرحلة الثانوية.',
                'max_students'  => 20,
                'price'         => 200.00,
                'schedule'      => 'السبت والأربعاء 3-5 مساءً',
            ],
            [
                'teacher_index' => 7, // تاريخ
                'grade_level'   => GradeLevel::SECONDARY_2,
                'name'          => 'تاريخ الثاني الثانوي',
                'description'   => 'شرح منهج التاريخ مع ربطه بالأحداث الحديثة وتحليل الوثائق.',
                'max_students'  => 30,
                'price'         => 180.00,
                'schedule'      => 'الخميس 4-6 مساءً',
            ],
            [
                'teacher_index' => 8, // كمبيوتر
                'grade_level'   => GradeLevel::MIDDLE_3,
                'name'          => 'برمجة للمبتدئين - الثالث الإعدادي',
                'description'   => 'مقدمة في البرمجة وأساسيات علم الحاسوب للمرحلة الإعدادية.',
                'max_students'  => 15,
                'price'         => 230.00,
                'schedule'      => 'الجمعة 2-4 مساءً',
            ],
        ];

        foreach ($groupsData as $groupData) {
            $teacher = $teachers[$groupData['teacher_index']];

            Group::create([
                'teacher_id'      => $teacher->id,
                'name'            => $groupData['name'],
                'subject'         => $teacher->subject, // يرث الـ enum من المدرس مباشرة
                'grade_level'     => $groupData['grade_level'],
                'description'     => $groupData['description'],
                'max_students'    => $groupData['max_students'],
                'current_students' => 0,
                'academic_year'   => '2024/2025',
                'price'           => $groupData['price'],
                'schedule'        => $groupData['schedule'],
                'is_active'       => true,
            ]);
        }
    }
}
