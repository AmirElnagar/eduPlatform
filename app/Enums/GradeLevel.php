<?php

namespace App\Enums;

enum GradeLevel: string
{
    case PRIMARY_1 = 'primary_1';
    case PRIMARY_2 = 'primary_2';
    case PRIMARY_3 = 'primary_3';
    case PRIMARY_4 = 'primary_4';
    case PRIMARY_5 = 'primary_5';
    case PRIMARY_6 = 'primary_6';
    
    case MIDDLE_1 = 'middle_1';
    case MIDDLE_2 = 'middle_2';
    case MIDDLE_3 = 'middle_3';
    
    case SECONDARY_1 = 'secondary_1';
    case SECONDARY_2 = 'secondary_2';
    case SECONDARY_3 = 'secondary_3';

    public function label(): string
    {
        return match($this) {
            self::PRIMARY_1 => 'الصف الأول الابتدائي',
            self::PRIMARY_2 => 'الصف الثاني الابتدائي',
            self::PRIMARY_3 => 'الصف الثالث الابتدائي',
            self::PRIMARY_4 => 'الصف الرابع الابتدائي',
            self::PRIMARY_5 => 'الصف الخامس الابتدائي',
            self::PRIMARY_6 => 'الصف السادس الابتدائي',
            
            self::MIDDLE_1 => 'الصف الأول الإعدادي',
            self::MIDDLE_2 => 'الصف الثاني الإعدادي',
            self::MIDDLE_3 => 'الصف الثالث الإعدادي',
            
            self::SECONDARY_1 => 'الصف الأول الثانوي',
            self::SECONDARY_2 => 'الصف الثاني الثانوي',
            self::SECONDARY_3 => 'الصف الثالث الثانوي',
        };
    }

    public function stage(): string
    {
        return match($this) {
            self::PRIMARY_1, self::PRIMARY_2, self::PRIMARY_3,
            self::PRIMARY_4, self::PRIMARY_5, self::PRIMARY_6 => 'ابتدائي',
            
            self::MIDDLE_1, self::MIDDLE_2, self::MIDDLE_3 => 'إعدادي',
            
            self::SECONDARY_1, self::SECONDARY_2, self::SECONDARY_3 => 'ثانوي',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function byStage(string $stage): array
    {
        return array_filter(self::cases(), function($grade) use ($stage) {
            return $grade->stage() === $stage;
        });
    }
}