<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectsSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            // مواد مشتركة
            ['name' => 'اللغة العربية', 'slug' => 'arabic'],
            ['name' => 'اللغة الإنجليزية', 'slug' => 'english'],
            ['name' => 'الرياضيات', 'slug' => 'math'],
            ['name' => 'العلوم', 'slug' => 'science'],
            ['name' => 'الدراسات الاجتماعية', 'slug' => 'social_studies'],
            ['name' => 'الكمبيوتر وتكنولوجيا المعلومات', 'slug' => 'computer'],
            
            // لغات أجنبية
            ['name' => 'اللغة الفرنسية', 'slug' => 'french'],
            ['name' => 'اللغة الألمانية', 'slug' => 'german'],
            
            // ثانوي علمي
            ['name' => 'الفيزياء', 'slug' => 'physics'],
            ['name' => 'الكيمياء', 'slug' => 'chemistry'],
            ['name' => 'الأحياء', 'slug' => 'biology'],
            ['name' => 'الجيولوجيا وعلوم البيئة', 'slug' => 'geology'],
            
            // ثانوي أدبي
            ['name' => 'التاريخ', 'slug' => 'history'],
            ['name' => 'الجغرافيا', 'slug' => 'geography'],
            ['name' => 'الفلسفة والمنطق', 'slug' => 'philosophy'],
            ['name' => 'علم النفس والاجتماع', 'slug' => 'psychology'],
        ];

        DB::table('subjects')->insert($subjects);

    }
}