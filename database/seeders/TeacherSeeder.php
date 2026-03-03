<?php

namespace Database\Seeders;

use App\Enums\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'subject'             => Subject::MATH,
                'bio'                 => 'مدرس رياضيات بخبرة ١٢ سنة في تدريس الثانوية العامة. متخصص في الجبر والهندسة وحساب المثلثات. حققت نسبة نجاح ٩٨٪ لطلابي في امتحانات الثانوية.',
                'years_of_experience' => 12,
                'hourly_rate'         => 150.00,
            ],
            [
                'subject'             => Subject::ARABIC,
                'bio'                 => 'دكتوراه في اللغة العربية وآدابها. أدرّس النحو والصرف والبلاغة والأدب. أساعد الطلاب على إتقان مهارات الكتابة الإبداعية والتحليل الأدبي.',
                'years_of_experience' => 15,
                'hourly_rate'         => 120.00,
            ],
            [
                'subject'             => Subject::PHYSICS,
                'bio'                 => 'مهندس ومدرس فيزياء. أشرح المفاهيم المعقدة بأسلوب مبسط ومشوق. متخصص في الميكانيكا والكهرباء والموجات.',
                'years_of_experience' => 8,
                'hourly_rate'         => 130.00,
            ],
            [
                'subject'             => Subject::CHEMISTRY,
                'bio'                 => 'ماجستير كيمياء تطبيقية. أعتمد على التجارب العملية وحل المسائل بالتفصيل. خبرة في تدريس الثانوية والمرحلة الجامعية الأولى.',
                'years_of_experience' => 10,
                'hourly_rate'         => 140.00,
            ],
            [
                'subject'             => Subject::ENGLISH,
                'bio'                 => 'بكالوريوس لغة إنجليزية وتعليم. شهادة TEFL دولية. متخصص في محادثة وقواعد وتأهيل الطلاب لامتحانات IELTS وTOEFL.',
                'years_of_experience' => 7,
                'hourly_rate'         => 160.00,
            ],
            [
                'subject'             => Subject::BIOLOGY,
                'bio'                 => 'دكتوراه في الأحياء الجزيئية. أدرّس الأحياء بأسلوب تفاعلي مع ربط المعلومات بالحياة اليومية. نسبة نجاح طلابي في الثانوية ٩٥٪.',
                'years_of_experience' => 11,
                'hourly_rate'         => 125.00,
            ],
            [
                'subject'             => Subject::MATH,
                'bio'                 => 'بكالوريوس رياضيات بحتة. متخصص في الإحصاء والتفاضل والتكامل. أقدم شرحاً مفصلاً لكل درس مع أمثلة متنوعة.',
                'years_of_experience' => 6,
                'hourly_rate'         => 110.00,
            ],
            [
                'subject'             => Subject::HISTORY,
                'bio'                 => 'مؤرخة متخصصة في التاريخ الإسلامي. أجعل المادة ممتعة من خلال القصص والتحليل التاريخي.',
                'years_of_experience' => 9,
                'hourly_rate'         => 100.00,
            ],
            [
                'subject'             => Subject::COMPUTER,
                'bio'                 => 'مطور برمجيات ومدرس علوم حاسوب. أدرّس البرمجة بـ Python وJava وأساسيات الشبكات وقواعد البيانات.',
                'years_of_experience' => 5,
                'hourly_rate'         => 200.00,
            ],
            [
                'subject'             => Subject::PHYSICS,
                'bio'                 => 'مدرسة فيزياء بخبرة واسعة في إعداد الطلاب للجامعات. أركز على حل المسائل وربط الفيزياء بالتطبيقات العملية.',
                'years_of_experience' => 13,
                'hourly_rate'         => 135.00,
            ],
        ];

        $teacherUsers = User::where('role', 'teacher')->orderBy('id')->get();

        foreach ($teacherUsers as $index => $user) {
            Teacher::create(array_merge($data[$index], [
                'user_id'            => $user->id,
                'is_subscribed'      => true,
                'subscription_start' => now()->subMonths(rand(1, 12)),
                'subscription_end'   => now()->addMonths(rand(1, 6)),
            ]));
        }
    }
}
