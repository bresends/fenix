<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PlatoonEnum: string implements HasColor, HasLabel
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
    case JULIET = 'Juliet';
    case ZULU = 'Zulu';
    case CFO1 = 'CFO-1';
    case CFO1A = 'CFO-1 A';
    case CFO1B = 'CFO-1 B';
    case CFO2A = 'CFO-2 A';
    case CFO2B = 'CFO-2 B';
    case CFO3A = 'CFO-3 A';
    case CFO3B = 'CFO-3 B';
    case CFO2 = 'CFO-2';
    case CFO3 = 'CFO-3';

    case CHOA = 'CHOA';
    case EAS = 'EAS';
    case CAS = 'CAS';
    case EAC = 'EAC';
    case ADMINISTRACAO = 'Administração';

    public static function CFO(): array
    {
        return [self::CFO1A, self::CFO1B, self::CFO2, self::CFO2A, self::CFO2B, self::CFO3, self::CFO3A, self::CFO3B];
    }

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
            self::JULIET => 'Juliet',
            self::ZULU => 'Zulu',
            self::CFO1 => 'CFO-1',
            self::CFO1A => 'CFO-1 A',
            self::CFO1B => 'CFO-1 B',
            self::CFO2A => 'CFO-2 A',
            self::CFO2B => 'CFO-2 B',
            self::CFO3A => 'CFO-3 A',
            self::CFO3B => 'CFO-3 B',
            self::CFO2 => 'CFO-2',
            self::CFO3 => 'CFO-3',
            self::CHOA => 'CHOA',
            self::CAS => 'CAS',
            self::EAS => 'EAS',
            self::EAC => 'EAC',
            self::ADMINISTRACAO => 'Administração',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ALPHA, self::CHARLIE, self::BRAVO, self::DELTA, self::ECHO, self::FOXTROT, self::GOLF, self::HOTEL, self::INDIA, self::JULIET, self::ZULU, self::EAC, self::EAS => Color::Yellow,
            self::CFO1, self::CFO1A, self::CFO2A, self::CFO3A => Color::Red,
            self::CFO1B, self::CFO2B, self::CFO3B => Color::Orange,
            self::CFO2, self::CFO3 => Color::Blue,
            self::CHOA, self::CAS => Color::Green,
            self::ADMINISTRACAO => Color::Gray,
        };
    }
}
