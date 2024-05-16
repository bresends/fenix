<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FoEnum: string implements HasLabel
{
    case NEGATIVO = 'Negativo';
    case POSITIVO = 'Positivo';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::POSITIVO => 'success',
            self::NEGATIVO => 'danger',
        };

    }
}