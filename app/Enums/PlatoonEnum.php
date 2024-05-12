<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PlatoonEnum: string implements HasLabel
{
    case ALPHA = 'Alpha';
    case BRAVO = 'Bravo';
    case CHARLIE = 'Charlie';
    case DELTA = 'Delta';
    case ECHO = 'Echo';
    case FOXTROT = 'Foxtrot';
    case GOLF = 'Golf';
    case HOTEL = 'Hotel';
    case INDIA = 'Índia';
    case CFO1 = 'CFO-1';
    case CFO2 = 'CFO-2';
    case CFO3 = 'CFO-3';
    case CHOA = 'CHOA';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ALPHA => 'Alpha',
            self::BRAVO => 'Bravo',
            self::CHARLIE => 'Charlie',
            self::DELTA => 'Delta',
            self::ECHO => 'Echo',
            self::FOXTROT => 'Foxtrot',
            self::GOLF => 'Golf',
            self::HOTEL => 'Hotel',
            self::INDIA => 'Índia',
            self::CFO1 => 'CFO-1',
            self::CFO2 => 'CFO-2',
            self::CFO3 => 'CFO-3',
            self::CHOA => 'CHOA',
        };
    }
}
