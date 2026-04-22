<?php

namespace App\Enums;

enum ReportType: string
{
    case Scam               = 'scam';
    case FakeListing        = 'fake_listing';
    case Spam               = 'spam';
    case Harassment         = 'harassment';
    case InappropriateContent = 'inappropriate_content';
    case NonDelivery        = 'non_delivery';
    case Other              = 'other';

    public function label(): string
    {
        return match($this) {
            self::Scam                 => 'Scam / Fraud',
            self::FakeListing          => 'Fake or Misleading Listing',
            self::Spam                 => 'Spam',
            self::Harassment           => 'Harassment',
            self::InappropriateContent => 'Inappropriate Content',
            self::NonDelivery          => 'Non-Delivery of Order',
            self::Other                => 'Other',
        };
    }
}
