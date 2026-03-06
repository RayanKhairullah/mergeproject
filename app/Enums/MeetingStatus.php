<?php

declare(strict_types=1);

namespace App\Enums;

enum MeetingStatus: string
{
    case DRAFT = 'DRAFT';
    case PENDING_APPROVAL = 'PENDING_APPROVAL';
    case PUBLISHED = 'PUBLISHED';
    case COMPLETED = 'COMPLETED';
    case REJECTED = 'REJECTED';

    public function canTransitionTo(self $status): bool
    {
        return match ($this) {
            self::DRAFT => in_array($status, [self::PENDING_APPROVAL]),
            self::PENDING_APPROVAL => in_array($status, [self::PUBLISHED, self::REJECTED, self::DRAFT]),
            self::PUBLISHED => in_array($status, [self::COMPLETED, self::DRAFT]),
            self::COMPLETED => false,
            self::REJECTED => in_array($status, [self::DRAFT]),
        };
    }

    public function isVisibleOnMonitor(): bool
    {
        return $this === self::PUBLISHED;
    }

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PENDING_APPROVAL => 'Pending Approval',
            self::PUBLISHED => 'Published',
            self::COMPLETED => 'Completed',
            self::REJECTED => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING_APPROVAL => 'amber',
            self::PUBLISHED => 'mint',
            self::COMPLETED => 'emerald',
            self::REJECTED => 'red',
        };
    }
}
