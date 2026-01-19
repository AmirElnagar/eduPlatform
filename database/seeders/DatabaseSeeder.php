<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\ParentModel;
use App\Models\Grade;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Grades
        $this->seedGrades();

        // Create Admin User
        $this->seedAdmin();

        // Create Sample Teacher
        $this->seedTeacher();

        // Create Sample Student
        $this->seedStudent();

        // Create Sample Parent
        $this->seedParent();
    }

    private function seedGrades()
    {
        $grades = [
            ['name' => 'Grade 1', 'name_ar' => 'الصف الأول الابتدائي', 'level' => 'primary', 'order_index' => 1],
            ['name' => 'Grade 2', 'name_ar' => 'الصف الثاني الابتدائي', 'level' => 'primary', 'order_index' => 2],
            ['name' => 'Grade 3', 'name_ar' => 'الصف الثالث الابتدائي', 'level' => 'primary', 'order_index' => 3],
            ['name' => 'Grade 4', 'name_ar' => 'الصف الرابع الابتدائي', 'level' => 'primary', 'order_index' => 4],
            ['name' => 'Grade 5', 'name_ar' => 'الصف الخامس الابتدائي', 'level' => 'primary', 'order_index' => 5],
            ['name' => 'Grade 6', 'name_ar' => 'الصف السادس الابتدائي', 'level' => 'primary', 'order_index' => 6],
            ['name' => 'Grade 7', 'name_ar' => 'الصف الأول الإعدادي', 'level' => 'preparatory', 'order_index' => 7],
            ['name' => 'Grade 8', 'name_ar' => 'الصف الثاني الإعدادي', 'level' => 'preparatory', 'order_index' => 8],
            ['name' => 'Grade 9', 'name_ar' => 'الصف الثالث الإعدادي', 'level' => 'preparatory', 'order_index' => 9],
            ['name' => 'Grade 10', 'name_ar' => 'الصف الأول الثانوي', 'level' => 'secondary', 'order_index' => 10],
            ['name' => 'Grade 11', 'name_ar' => 'الصف الثاني الثانوي', 'level' => 'secondary', 'order_index' => 11],
            ['name' => 'Grade 12', 'name_ar' => 'الصف الثالث الثانوي', 'level' => 'secondary', 'order_index' => 12],
        ];

        foreach ($grades as $grade) {
            Grade::create($grade);
        }
    }

    private function seedAdmin()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@eduplatform.com',
            'phone' => '01000000000',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    private function seedTeacher()
    {
        $teacherUser = User::create([
            'name' => 'أحمد محمود',
            'email' => 'teacher@eduplatform.com',
            'phone' => '01111111111',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'email_verified_at' => now(),
        ]);

        Teacher::create([
            'user_id' => $teacherUser->id,
            'specialization' => 'رياضيات',
            'bio' => 'مدرس رياضيات خبرة 10 سنوات في التدريس',
            'experience_years' => 10,
            'is_verified' => true,
            'accept_online_payment' => true,
        ]);
    }

    private function seedStudent()
    {
        $studentUser = User::create([
            'name' => 'محمد أحمد',
            'email' => 'student@eduplatform.com',
            'phone' => '01222222222',
            'password' => Hash::make('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        Student::create([
            'user_id' => $studentUser->id,
            'grade_id' => 10, // Grade 10
        ]);
    }

    private function seedParent()
    {
        $parentUser = User::create([
            'name' => 'سارة علي',
            'email' => 'parent@eduplatform.com',
            'phone' => '01333333333',
            'password' => Hash::make('password'),
            'role' => 'parent',
            'email_verified_at' => now(),
        ]);

        ParentModel::create([
            'user_id' => $parentUser->id,
        ]);
    }
}
