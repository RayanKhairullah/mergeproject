<?php

declare(strict_types=1);

namespace App\Enums;

enum GuestType: string
{
    case VVIP = 'VVIP';
    case VIP = 'VIP';
    case INTERNAL = 'Internal';

    public function label(): string
    {
        return match ($this) {
            self::VVIP => 'VVIP Guest',
            self::VIP => 'VIP Guest',
            self::INTERNAL => 'Internal Staff',
        };
    }

    public function priority(): int
    {
        return match ($this) {
            self::VVIP => 1,
            self::VIP => 2,
            self::INTERNAL => 3,
        };
    }
}
