<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cod    = 'cod';
    case Gcash  = 'gcash';
    case Bank   = 'bank';
    case Walkin = 'walkin';

    public function label(): string
    {
        return match($this) {
            self::Cod    => 'Cash on Delivery',
            self::Gcash  => 'GCash',
            self::Bank   => 'Bank Transfer',
            self::Walkin => 'Walk-in / Pick-up',
        };
    }

    public function hasDeliveryFee(): bool
    {
        return $this !== self::Walkin;
    }

    public function requiresAddress(): bool
    {
        return $this === self::Cod;
    }
}
