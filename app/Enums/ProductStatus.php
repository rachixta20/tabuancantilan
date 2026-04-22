<?php

namespace App\Enums;

enum ProductStatus: string
{
    case Pending  = 'pending';
    case Active   = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match($this) {
            self::Pending  => 'Pending Review',
            self::Active   => 'Active',
            self::Inactive => 'Inactive',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending  => 'yellow',
            self::Active   => 'green',
            self::Inactive => 'gray',
        };
    }
}
