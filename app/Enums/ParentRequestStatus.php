<?php

namespace App\Enums;

enum ParentRequestStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'في الانتظار',
            self::ACCEPTED => 'مقبول',
            self::REJECTED => 'مرفوض',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::ACCEPTED => 'success',
            self::REJECTED => 'danger',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}