<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum MakeUpExamStatusEnum: string implements HasLabel, HasColor
{
    case TEORICA = 'Teórica';
    case PRATICA = 'Prática';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TEORICA => 'Teórica',
            self::PRATICA => 'Prática',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::TEORICA => 'info',
            self::PRATICA => 'success',
        };

    }
}
