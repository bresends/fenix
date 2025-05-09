<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

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
    case JULIET = 'Juliet';
    case CFO1 = 'CFO-1';
    case CFO1A = 'CFO-1 A';
    case CFO1B = 'CFO-1 B';
    case CFO2 = 'CFO-2';
    case CFO3 = 'CFO-3';
    case CHOA = 'CHOA';
    case EAS = 'EAS';
    case CAS = 'CAS';
    case ADMINISTRACAO = 'Administração';

    public static function CFO(): array
    {
        return [self::CFO1A->value, self::CFO1B->value, self::CFO2, self::CFO3];
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
            self::CFO1 => 'CFO-1',
            self::CFO1A => 'CFO-1 A',
            self::CFO1B => 'CFO-1 B',
            self::CFO2 => 'CFO-2',
            self::CFO3 => 'CFO-3',
            self::CHOA => 'CHOA',
            self::CAS => 'CAS',
            self::EAS => 'EAS',
            self::ADMINISTRACAO => 'Administração',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ALPHA, self::CHARLIE, self::BRAVO, self::DELTA, self::ECHO, self::FOXTROT, self::GOLF, self::HOTEL, self::INDIA, self::JULIET, self::EAS => Color::Yellow,
            self::CFO1, self::CFO1A => Color::Red,
            self::CFO1B => Color::Orange,
            self::CFO2, self::CFO3 => Color::Blue,
            self::CHOA, self::CAS => Color::Green,
            self::ADMINISTRACAO => Color::Gray,
        };
    }
}
