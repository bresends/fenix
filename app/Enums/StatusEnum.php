<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;

enum StatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case EM_ANDAMENTO = 'Em andamento';
    case DEFERIDO = 'Deferido';
    case INDEFERIDO = 'Indeferido';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::EM_ANDAMENTO => 'Em andamento',
            self::DEFERIDO => 'Deferido',
            self::INDEFERIDO => 'Indeferido',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::EM_ANDAMENTO => 'warning',
            self::DEFERIDO => 'success',
            self::INDEFERIDO => 'danger',
        };

    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::EM_ANDAMENTO => 'heroicon-s-exclamation-triangle',
            self::DEFERIDO => 'heroicon-s-check-badge',
            self::INDEFERIDO => 'heroicon-s-x-circle',
        };

    }
}
