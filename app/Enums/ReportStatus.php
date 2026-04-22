<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Pending   = 'pending';
    case Reviewed  = 'reviewed';
    case Resolved  = 'resolved';
    case Dismissed = 'dismissed';

    public function label(): string
    {
        return match($this) {
            self::Pending   => 'Pending',
            self::Reviewed  => 'Under Review',
            self::Resolved  => 'Resolved',
            self::Dismissed => 'Dismissed',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending   => 'amber',
            self::Reviewed  => 'blue',
            self::Resolved  => 'green',
            self::Dismissed => 'gray',
        };
    }
}
