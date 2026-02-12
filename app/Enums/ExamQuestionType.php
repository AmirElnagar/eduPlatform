<?php

namespace App\Enums;

enum ExamQuestionType: string
{
    case MCQ = 'mcq';
    case ESSAY = 'essay';

    public function label(): string
    {
        return match($this) {
            self::MCQ => 'اختيار من متعدد',
            self::ESSAY => 'مقالي',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}