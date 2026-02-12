<?php

namespace App\Enums;

enum LessonType: string
{
    case OFFLINE = 'offline';
    case RECORDED = 'recorded';
    case LIVE = 'live';

    public function label(): string
    {
        return match($this) {
            self::OFFLINE => 'حضور (أوفلاين)',
            self::RECORDED => 'مسجل',
            self::LIVE => 'مباشر (لايف)',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::OFFLINE => '🏫',
            self::RECORDED => '📹',
            self::LIVE => '🔴',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}