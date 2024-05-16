<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum PlatoonEnum: string implements HasLabel, HasColor
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
    case ADMINISTRACAO = 'Administração';

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
            self::ADMINISTRACAO => 'Administração',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::ALPHA => 'warning',
            self::BRAVO => 'warning',
            self::CHARLIE => 'warning',
            self::DELTA => 'warning',
            self::ECHO => 'warning',
            self::FOXTROT => 'warning',
            self::GOLF => 'warning',
            self::HOTEL => 'warning',
            self::INDIA => 'warning',
            self::CFO1 => 'info',
            self::CFO2 => 'info',
            self::CFO3 => 'info',
            self::CHOA => 'info',
            self::ADMINISTRACAO => 'gray',
        };

    }
}
