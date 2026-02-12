<?php

namespace App\Enums;

enum Subject: string
{
    // مواد مشتركة
    case ARABIC = 'arabic';
    case ENGLISH = 'english';
    case MATH = 'math';
    case SCIENCE = 'science';
    case SOCIAL_STUDIES = 'social_studies';
    case COMPUTER = 'computer';
    
    // لغات أجنبية
    case FRENCH = 'french';
    case GERMAN = 'german';
    
    // ثانوي علمي
    case PHYSICS = 'physics';
    case CHEMISTRY = 'chemistry';
    case BIOLOGY = 'biology';
    case GEOLOGY = 'geology';
    
    // ثانوي أدبي
    case HISTORY = 'history';
    case GEOGRAPHY = 'geography';
    case PHILOSOPHY = 'philosophy';
    case PSYCHOLOGY = 'psychology';

    public function label(): string
    {
        return match($this) {
            self::ARABIC => 'اللغة العربية',
            self::ENGLISH => 'اللغة الإنجليزية',
            self::MATH => 'الرياضيات',
            self::SCIENCE => 'العلوم',
            self::SOCIAL_STUDIES => 'الدراسات الاجتماعية',
            self::COMPUTER => 'الكمبيوتر وتكنولوجيا المعلومات',
            
            self::FRENCH => 'اللغة الفرنسية',
            self::GERMAN => 'اللغة الألمانية',
            
            self::PHYSICS => 'الفيزياء',
            self::CHEMISTRY => 'الكيمياء',
            self::BIOLOGY => 'الأحياء',
            self::GEOLOGY => 'الجيولوجيا وعلوم البيئة',
            
            self::HISTORY => 'التاريخ',
            self::GEOGRAPHY => 'الجغرافيا',
            self::PHILOSOPHY => 'الفلسفة والمنطق',
            self::PSYCHOLOGY => 'علم النفس والاجتماع',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function forGradeLevel(GradeLevel $gradeLevel): array
    {
        $stage = $gradeLevel->stage();
        
        $common = [
            self::ARABIC,
            self::ENGLISH,
            self::MATH,
        ];

        return match($stage) {
            'ابتدائي', 'إعدادي' => array_merge($common, [
                self::SCIENCE,
                self::SOCIAL_STUDIES,
                self::COMPUTER,
            ]),
            'ثانوي' => array_merge($common, [
                self::FRENCH,
                self::GERMAN,
                self::PHYSICS,
                self::CHEMISTRY,
                self::BIOLOGY,
                self::GEOLOGY,
                self::HISTORY,
                self::GEOGRAPHY,
                self::PHILOSOPHY,
                self::PSYCHOLOGY,
            ]),
            default => $common,
        };
    }
}