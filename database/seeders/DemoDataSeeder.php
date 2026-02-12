<?php

namespace Database\Seeders;

use App\Enums\GradeLevel;
use App\Enums\Subject;
use App\Enums\UserRole;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\ParentModel;
use App\Models\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------
        | 1️⃣ إنشاء مدرسين
        |--------------------
        */

        $subjects = [
            Subject::MATH,
            Subject::ARABIC,
            Subject::ENGLISH,
            Subject::PHYSICS,
            Subject::CHEMISTRY,
        ];

        $teachers = [];

        for ($i = 1; $i <= 5; $i++) {

            $teacherUser = User::create([
                'name' => "مدرس رقم {$i}",
                'email' => "teacher{$i}@platform.com",
                'password' => Hash::make('123456'),
                'phone' => '0100000000' . $i,
                'role' => UserRole::TEACHER,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $teacher = Teacher::create([
                'user_id' => $teacherUser->id,
                'subject' => $subjects[$i - 1],
                'bio' => "مدرس مادة {$subjects[$i - 1]->value} بخبرة ممتازة",
                'years_of_experience' => rand(3, 15),
                'hourly_rate' => rand(100, 300),
            ]);

            $teachers[] = $teacher;
        }

        /*
        |--------------------
        | 2️⃣ إنشاء طلاب
        |--------------------
        */

        $students = [];

        for ($i = 1; $i <= 10; $i++) {

            $studentUser = User::create([
                'name' => "طالب رقم {$i}",
                'email' => "student{$i}@platform.com",
                'password' => Hash::make('123456'),
                'phone' => '0110000000' . $i,
                'role' => UserRole::STUDENT,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $student = Student::create([
                'user_id' => $studentUser->id,
                'grade_level' => GradeLevel::SECONDARY_3,
                'parent_phone' => '0129999999' . $i,
            ]);

            $students[] = $student;
        }

        /*
        |--------------------
        | 3️⃣ إنشاء أولياء أمور وربطهم بالطلاب
        |--------------------
        */

        for ($i = 1; $i <= 5; $i++) {

            $parentUser = User::create([
                'name' => "ولي أمر {$i}",
                'email' => "parent{$i}@platform.com",
                'password' => Hash::make('123456'),
                'phone' => '0129999999' . $i,
                'role' => UserRole::PARENT,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $parent = ParentModel::create([
                'user_id' => $parentUser->id,
            ]);

            // كل ولي أمر ليه طالبين
            $parent->students()->attach([
                $students[$i * 2 - 2]->id,
                $students[$i * 2 - 1]->id,
            ]);
        }

        /*
        |--------------------
        | 4️⃣ إنشاء مجموعات لكل مدرس
        |--------------------
        */

        foreach ($teachers as $index => $teacher) {

            Group::create([
                'teacher_id' => $teacher->id,
                'name' => "مجموعة {$teacher->subject->value} - ثالثة ثانوي",
                'subject' => $teacher->subject,
                'grade_level' => GradeLevel::SECONDARY_3,
                'description' => "مجموعة شرح ومراجعة مادة {$teacher->subject->value}",
                'max_students' => 25,
                'current_students' => rand(5, 15),
                'academic_year' => '2024/2025',
                'price' => rand(400, 700),
                'is_active' => true,
            ]);
        }
    }
}
