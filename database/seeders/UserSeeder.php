<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ==============================
        // Admin
        // ==============================
        User::create([
            'name'       => 'مدير النظام',
            'email'      => 'admin@platform.com',
            'password'   => Hash::make('password'),
            'phone'      => '01000000000',
            'role'       => 'admin',
            'is_active'  => true,
        ]);

        // ==============================
        // Teachers (10)
        // ==============================
        $teachers = [
            ['name' => 'أحمد محمد علي',     'email' => 'ahmed.ali@platform.com',    'phone' => '01011111111'],
            ['name' => 'سارة حسن إبراهيم',  'email' => 'sara.hassan@platform.com',  'phone' => '01022222222'],
            ['name' => 'محمود عبد الله',     'email' => 'mahmoud@platform.com',      'phone' => '01033333333'],
            ['name' => 'فاطمة الزهراء',     'email' => 'fatima@platform.com',       'phone' => '01044444444'],
            ['name' => 'عمر خالد النمر',    'email' => 'omar.khaled@platform.com',  'phone' => '01055555555'],
            ['name' => 'نورا سعيد جمال',    'email' => 'noura.saeed@platform.com',  'phone' => '01066666666'],
            ['name' => 'كريم طارق سليم',    'email' => 'karim.tarek@platform.com',  'phone' => '01077777777'],
            ['name' => 'منى رضا أحمد',      'email' => 'mona.reda@platform.com',    'phone' => '01088888888'],
            ['name' => 'يوسف إبراهيم حسن',  'email' => 'youssef@platform.com',      'phone' => '01099999999'],
            ['name' => 'هدى علي محمود',     'email' => 'hoda.ali@platform.com',     'phone' => '01010101010'],
        ];

        foreach ($teachers as $teacher) {
            User::create(array_merge($teacher, [
                'password'  => Hash::make('password'),
                'role'      => 'teacher',
                'is_active' => true,
            ]));
        }

        // ==============================
        // Students (20)
        // ==============================
        $students = [
            ['name' => 'علي أحمد سالم',       'email' => 'ali.ahmed@student.com'],
            ['name' => 'مريم محمد فارس',      'email' => 'mariam@student.com'],
            ['name' => 'خالد عمر ناصر',       'email' => 'khaled.omar@student.com'],
            ['name' => 'ليلى حسن عبد الله',   'email' => 'layla@student.com'],
            ['name' => 'أنس يوسف كمال',       'email' => 'anas@student.com'],
            ['name' => 'نادية سمير عطية',     'email' => 'nadia@student.com'],
            ['name' => 'محمد كريم سلطان',     'email' => 'mohamed.karim@student.com'],
            ['name' => 'رنا عبد الرحمن',      'email' => 'rana@student.com'],
            ['name' => 'طارق سعد الدين',      'email' => 'tarek.saad@student.com'],
            ['name' => 'دينا محمد الشافعي',   'email' => 'dina@student.com'],
            ['name' => 'أيمن علي البدري',     'email' => 'ayman@student.com'],
            ['name' => 'شيماء خالد فؤاد',     'email' => 'shaimaa@student.com'],
            ['name' => 'حسام مصطفى رضا',      'email' => 'hossam@student.com'],
            ['name' => 'آية عمرو حلمي',       'email' => 'aya@student.com'],
            ['name' => 'سامي نادر القاسم',    'email' => 'sami@student.com'],
            ['name' => 'إيمان رامي صالح',     'email' => 'iman@student.com'],
            ['name' => 'عبد الله وليد زكي',   'email' => 'abdallah@student.com'],
            ['name' => 'هبة جمال الدين',      'email' => 'heba@student.com'],
            ['name' => 'زياد فادي منصور',     'email' => 'ziyad@student.com'],
            ['name' => 'سلمى إياد حسين',      'email' => 'salma@student.com'],
        ];

        foreach ($students as $student) {
            User::create(array_merge($student, [
                'password'  => Hash::make('password'),
                'phone'     => '010' . rand(10000000, 99999999),
                'role'      => 'student',
                'is_active' => true,
            ]));
        }

        // ==============================
        // Parents (5)
        // ==============================
        $parents = [
            ['name' => 'وليد أحمد سالم',   'email' => 'walid@parent.com'],
            ['name' => 'هناء محمد فارس',   'email' => 'hanaa@parent.com'],
            ['name' => 'سمير خالد ناصر',   'email' => 'samir@parent.com'],
            ['name' => 'أميرة حسن علي',    'email' => 'amira@parent.com'],
            ['name' => 'رامي يوسف كمال',   'email' => 'rami@parent.com'],
        ];

        foreach ($parents as $parent) {
            User::create(array_merge($parent, [
                'password'  => Hash::make('password'),
                'phone'     => '010' . rand(10000000, 99999999),
                'role'      => 'parent',
                'is_active' => true,
            ]));
        }
    }
}
