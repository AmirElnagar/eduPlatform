<?php

namespace Database\Seeders;

use App\Enums\GradeLevel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            GradeLevel::SECONDARY_1,
            GradeLevel::SECONDARY_2,
            GradeLevel::SECONDARY_3,
            GradeLevel::MIDDLE_1,
            GradeLevel::MIDDLE_2,
            GradeLevel::MIDDLE_3,
            GradeLevel::PRIMARY_5,
            GradeLevel::PRIMARY_6,
        ];

        $studentUsers = User::where('role', 'student')->orderBy('id')->get();

        foreach ($studentUsers as $index => $user) {
            Student::create([
                'user_id'      => $user->id,
                'grade_level'  => $grades[$index % count($grades)],
                'parent_phone' => '010' . rand(10000000, 99999999),
                'address'      => ['القاهرة', 'الإسكندرية', 'الجيزة', 'المنصورة', 'أسيوط'][rand(0, 4)],
            ]);
        }
    }
}
