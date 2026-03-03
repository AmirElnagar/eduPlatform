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
        $this->call([
            UserSeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
            ParentSeeder::class,
            GroupSeeder::class,
            GroupStudentSeeder::class,
            LessonSeeder::class,
            ExamSeeder::class,
            ReviewSeeder::class,
            PaymentSeeder::class,
        ]);
    }

}
