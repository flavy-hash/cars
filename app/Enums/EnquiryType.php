<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EnquiryType: string implements HasColor, HasLabel
{
    case Reservation = 'reservation';
    case TestDrive = 'test_drive';

    public function getLabel(): string
    {
        return match ($this) {
            self::Reservation => 'Reservation',
            self::TestDrive => 'Test drive',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Reservation => 'primary',
            self::TestDrive => 'info',
        };
    }

    /**
     * A test drive is the only type that needs a date from the buyer.
     */
    public function needsPreferredDate(): bool
    {
        return $this === self::TestDrive;
    }
}
