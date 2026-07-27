<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Whether a car can still be bought. Set by staff — an enquiry arriving does not
 * move it on its own, or any passer-by could take a car off the market.
 */
enum CarStatus: string implements HasColor, HasLabel
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Sold = 'sold';

    public function getLabel(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Reserved => 'Reserved',
            self::Sold => 'Sold',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Available => 'success',
            self::Reserved => 'warning',
            self::Sold => 'gray',
        };
    }

    /**
     * A sold car is off the market; a reserved one still takes back-up interest.
     */
    public function acceptsEnquiries(): bool
    {
        return $this !== self::Sold;
    }
}
