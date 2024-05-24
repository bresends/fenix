<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum MakeUpExamStatusEnum: string implements HasLabel, HasColor, HasIcon
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

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::TEORICA => 'info',
            self::PRATICA => 'primary',
        };

    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::TEORICA => 'heroicon-s-academic-cap',
            self::PRATICA => 'heroicon-s-fire',
        };

    }
}
