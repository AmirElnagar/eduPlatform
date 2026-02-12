<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
    case PARENT = 'parent';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'مدير',
            self::TEACHER => 'مدرس',
            self::STUDENT => 'طالب',
            self::PARENT => 'ولي أمر',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}