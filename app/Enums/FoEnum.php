<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum FoEnum: string implements HasLabel, HasColor
{
    case Positivo = 'Positivo';
    case Negativo = 'Negativo';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Positivo => 'success',
            self::Negativo => 'danger',
        };

    }
}