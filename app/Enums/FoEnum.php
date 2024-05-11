<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FoEnum: string implements HasLabel
{
    case NEGATIVO = 'Negativo';
    case POSITIVO = 'Positivo';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NEGATIVO => 'Negativo',
            self::POSITIVO => 'Positivo',
        };
    }
}
